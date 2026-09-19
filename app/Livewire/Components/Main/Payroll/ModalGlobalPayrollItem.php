<?php

namespace App\Livewire\Components\Main\Payroll;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ModalGlobalPayrollItem extends Component
{
    public PayrollPeriod $period;

    public ?PayrollItem $item = null;

    public bool $editing = false;

    public string $type = 'earning';

    public string $name = '';

    public string $amount = '';

    public string $description = '';

    public function mount(
        PayrollPeriod $period,
        ?PayrollItem $item = null
    ): void {
        $this->period = $period;
        $this->item = $item;

        if ($item) {
            $this->editing = true;

            $this->type = $item->type;
            $this->name = $item->name;
            $this->amount = (string) $item->amount;
            $this->description = $item->description ?? '';
        }
    }

    private function modalName(): string
    {
        return 'global-payroll-item-'
            . $this->period->id
            . '-'
            . ($this->item?->id ?? 'create');
    }

    private function ensureEditable(): void
    {
        abort_unless(
            Auth::user()->can('edit-period-payroll'),
            403
        );

        $this->period->refresh();

        if ($this->period->status !== 'draft') {
            abort(
                422,
                'Komponen payroll hanya dapat diubah ketika periode masih draft.'
            );
        }
    }

    /**
     * Payroll draft dalam periode ini.
     */
    private function draftPayrolls()
    {
        return Payroll::query()
            ->where('payroll_period_id', $this->period->id)
            ->where('status', 'draft')
            ->get();
    }

    public function save(): void
    {
        $this->ensureEditable();

        $validated = $this->validate([
            'type' => [
                'required',
                'in:earning,deduction',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $payrolls = $this->draftPayrolls();

        if ($payrolls->isEmpty()) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Payroll Belum Dibuat',
                message: 'Generate payroll terlebih dahulu sebelum menambahkan komponen global.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Nama komponen global tidak boleh duplikat dalam satu periode.
        |--------------------------------------------------------------------------
        */

        $nameExists = PayrollItem::query()
            ->whereIn('payroll_id', $payrolls->pluck('id'))
            ->where('category', 'global')
            ->where('source', 'manual')
            ->where('name', $validated['name'])
            ->when(
                $this->editing && $this->item,
                fn($query) =>
                $query->where('name', '!=', $this->item->name)
            )
            ->exists();

        if ($nameExists) {
            throw ValidationException::withMessages([
                'name' => 'Komponen global dengan nama tersebut sudah ada pada periode ini.',
            ]);
        }

        $message = '';

        DB::transaction(function () use (
            $validated,
            $payrolls,
            &$message
        ) {
            $period = PayrollPeriod::query()
                ->whereKey($this->period->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($period->status !== 'draft') {
                abort(
                    422,
                    'Payroll periode sudah tidak dalam status draft.'
                );
            }

            if ($this->editing && $this->item) {

                $representative = PayrollItem::query()
                    ->whereKey($this->item->id)
                    ->where('category', 'global')
                    ->where('source', 'manual')
                    ->firstOrFail();

                $oldName = $representative->name;

                $items = PayrollItem::query()
                    ->whereIn('payroll_id', $payrolls->pluck('id'))
                    ->where('category', 'global')
                    ->where('source', 'manual')
                    ->where('name', $oldName)
                    ->get();

                foreach ($items as $item) {

                    $item->update([
                        'name' => $validated['name'],
                        'type' => $validated['type'],
                        'amount' => $validated['amount'],
                        'quantity' => 1,
                        'rate' => $validated['amount'],
                        'description' => $validated['description'] ?: null,
                    ]);

                    $this->recalculatePayroll(
                        $item->payroll_id
                    );
                }

                $message = 'Komponen payroll global berhasil diperbarui.';
            } else {

                foreach ($payrolls as $payroll) {

                    PayrollItem::create([
                        'payroll_id' => $payroll->id,

                        'name' => $validated['name'],
                        'type' => $validated['type'],
                        'category' => 'global',

                        'amount' => $validated['amount'],
                        'quantity' => 1,
                        'rate' => $validated['amount'],

                        'source' => 'manual',

                        'description' => $validated['description'] ?: null,

                        'sort_order' => 100,
                    ]);

                    $this->recalculatePayroll(
                        $payroll->id
                    );
                }

                $message = 'Komponen payroll global berhasil ditambahkan.';
            }
        });

        $this->dispatch(
            'payroll-period-updated'
        );

        $this->dispatch(
            'wirekit-modal-close',
            name: $this->modalName()
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: $message
        );
    }

    public function delete(): void
    {
        $this->ensureEditable();

        if (!$this->item) {
            return;
        }

        DB::transaction(function () {

            $payrolls = $this->draftPayrolls();

            $representative = PayrollItem::query()
                ->whereKey($this->item->id)
                ->where('category', 'global')
                ->where('source', 'manual')
                ->firstOrFail();

            PayrollItem::query()
                ->whereIn('payroll_id', $payrolls->pluck('id'))
                ->where('category', 'global')
                ->where('source', 'manual')
                ->where('name', $representative->name)
                ->get()
                ->each(function (PayrollItem $item) {
                    $payrollId = $item->payroll_id;

                    $item->delete();

                    $this->recalculatePayroll(
                        $payrollId
                    );
                });
        });

        $this->dispatch(
            'payroll-period-updated'
        );

        $this->dispatch(
            'wirekit-modal-close',
            name: $this->modalName()
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Komponen payroll global berhasil dihapus.'
        );
    }

    private function recalculatePayroll(int $payrollId): void
    {
        $items = PayrollItem::query()
            ->where('payroll_id', $payrollId)
            ->get();

        $grossAmount = (float) $items
            ->where('type', 'earning')
            ->sum('amount');

        $deductionAmount = (float) $items
            ->where('type', 'deduction')
            ->sum('amount');

        Payroll::query()
            ->whereKey($payrollId)
            ->update([
                'gross_amount' => $grossAmount,
                'deduction_amount' => $deductionAmount,
                'net_amount' => $grossAmount - $deductionAmount,
            ]);
    }

    public function render()
    {
        return view(
            'livewire.components.main.payroll.modal-global-payroll-item'
        );
    }
}
