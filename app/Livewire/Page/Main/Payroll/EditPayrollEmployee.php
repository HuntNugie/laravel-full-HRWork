<?php

namespace App\Livewire\Page\Main\Payroll;

use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Edit Payroll Karyawan'])]
class EditPayrollEmployee extends Component
{
    public PayrollPeriod $period;

    public Payroll $payroll;

    public Employees $employee;

    public EmployeeContract $contract;

    public string $notes = '';

    public ?int $editingItemId = null;

    public string $itemType = 'earning';

    public string $itemName = '';

    public string $itemAmount = '';

    public string $itemDescription = '';

    public function mount(
        PayrollPeriod $period,
        Payroll $payroll
    ): void {
        abort_unless(
            Auth::user()->can('edit-period-payroll'),
            403
        );

        abort_unless(
            $payroll->payroll_period_id === $period->id,
            404
        );

        abort_unless(
            $payroll->status === 'draft',
            422
        );

        $this->period = $period;

        $this->payroll = $payroll;

        $this->employee = Employees::query()
            ->with('user')
            ->findOrFail($payroll->employee_id);

        $this->contract = EmployeeContract::query()
            ->findOrFail($payroll->employee_contract_id);

        $this->notes = $payroll->notes ?? '';
    }

    #[Computed]
    public function items(): Collection
    {
        return PayrollItem::query()
            ->where('payroll_id', $this->payroll->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    #[Computed]
    public function systemItems(): Collection
    {
        return $this->items
            ->where('source', 'system')
            ->values();
    }

    #[Computed]
    public function manualItems(): Collection
    {
        return $this->items
            ->where('source', 'manual')
            ->values();
    }

    #[Computed]
    public function totalEarning(): float
    {
        return (float) $this->items
            ->where('type', 'earning')
            ->sum('amount');
    }

    #[Computed]
    public function totalDeduction(): float
    {
        return (float) $this->items
            ->where('type', 'deduction')
            ->sum('amount');
    }

    #[Computed]
    public function totalNet(): float
    {
        return $this->totalEarning - $this->totalDeduction;
    }

    private function ensureEditable(): void
    {
        abort_unless(
            Auth::user()->can('edit-period-payroll'),
            403
        );

        $this->payroll->refresh();

        if ($this->payroll->status !== 'draft') {
            abort(
                422,
                'Payroll hanya dapat diedit ketika masih berstatus draft.'
            );
        }
    }

    public function saveNotes(): void
    {
        $this->ensureEditable();

        $validated = $this->validate([
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $this->payroll->update([
            'notes' => $validated['notes'] ?: null,
        ]);

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Catatan payroll berhasil diperbarui.'
        );
    }

    public function startEditItem(int $itemId): void
    {
        $this->ensureEditable();

        $item = PayrollItem::query()
            ->where('payroll_id', $this->payroll->id)
            ->whereKey($itemId)
            ->firstOrFail();

        if ($item->source !== 'manual') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat diedit',
                message: 'Komponen yang dibuat oleh sistem tidak dapat diedit.'
            );

            return;
        }

        $this->editingItemId = $item->id;
        $this->itemType = $item->type;
        $this->itemName = $item->name;
        $this->itemAmount = (string) $item->amount;
        $this->itemDescription = $item->description ?? '';
    }

    public function saveItem(): void
    {
        $this->ensureEditable();
        $message = "";
        $validated = $this->validate([
            'itemType' => [
                'required',
                'in:earning,deduction',
            ],

            'itemName' => [
                'required',
                'string',
                'max:255',
            ],

            'itemAmount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'itemDescription' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($validated) {

            $amount = (float) $validated['itemAmount'];

            if ($this->editingItemId !== null) {

                $item = PayrollItem::query()
                    ->where('payroll_id', $this->payroll->id)
                    ->whereKey($this->editingItemId)
                    ->firstOrFail();

                abort_unless(
                    $item->source === 'manual',
                    422
                );

                $item->update([
                    'name' => $validated['itemName'],
                    'type' => $validated['itemType'],
                    'category' => 'manual',
                    'amount' => $amount,
                    'quantity' => 1,
                    'rate' => $amount,
                    'source' => 'manual',
                    'description' => $validated['itemDescription'] ?: null,
                ]);

                $message = 'Item payroll berhasil diperbarui.';
            } else {

                $maxSortOrder = PayrollItem::query()
                    ->where('payroll_id', $this->payroll->id)
                    ->max('sort_order');

                PayrollItem::create([
                    'payroll_id' => $this->payroll->id,
                    'name' => $validated['itemName'],
                    'type' => $validated['itemType'],
                    'category' => 'manual',
                    'amount' => $amount,
                    'quantity' => 1,
                    'rate' => $amount,
                    'source' => 'manual',
                    'description' => $validated['itemDescription'] ?: null,
                    'sort_order' => ((int) $maxSortOrder) + 1,
                ]);

                $message = 'Item payroll berhasil ditambahkan.';
            }

            $this->recalculatePayroll();
        });

        $this->resetItemForm();

        unset(
            $this->items,
            $this->systemItems,
            $this->manualItems,
            $this->totalEarning,
            $this->totalDeduction,
            $this->totalNet
        );

        $this->payroll->refresh();
        $message = "Berhasil menambahkan item manual";
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: $message
        );
    }

    public function deleteItem(int $itemId): void
    {
        $this->ensureEditable();

        $item = PayrollItem::query()
            ->where('payroll_id', $this->payroll->id)
            ->whereKey($itemId)
            ->firstOrFail();

        if ($item->source !== 'manual') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat dihapus',
                message: 'Komponen yang dibuat oleh sistem tidak dapat dihapus.'
            );

            return;
        }

        DB::transaction(function () use ($item) {

            $item->delete();

            $this->recalculatePayroll();
        });

        unset(
            $this->items,
            $this->systemItems,
            $this->manualItems,
            $this->totalEarning,
            $this->totalDeduction,
            $this->totalNet
        );

        $this->payroll->refresh();

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Item payroll berhasil dihapus.'
        );
    }

    private function recalculatePayroll(): void
    {
        $items = PayrollItem::query()
            ->where('payroll_id', $this->payroll->id)
            ->get();

        $grossAmount = (float) $items
            ->where('type', 'earning')
            ->sum('amount');

        $deductionAmount = (float) $items
            ->where('type', 'deduction')
            ->sum('amount');

        $netAmount = $grossAmount - $deductionAmount;

        $this->payroll->update([
            'gross_amount' => $grossAmount,
            'deduction_amount' => $deductionAmount,
            'net_amount' => $netAmount,
        ]);
    }

    public function cancelEditItem(): void
    {
        $this->resetItemForm();
    }

    private function resetItemForm(): void
    {
        $this->reset([
            'editingItemId',
            'itemName',
            'itemAmount',
            'itemDescription',
        ]);

        $this->itemType = 'earning';
    }

    public function money(float|int|string|null $value): string
    {
        return 'Rp' . number_format(
            (float) ($value ?? 0),
            0,
            ',',
            '.'
        );
    }

    public function render(): View
    {
        return view(
            'livewire.page.main.payroll.edit-payroll-employee'
        );
    }
}
