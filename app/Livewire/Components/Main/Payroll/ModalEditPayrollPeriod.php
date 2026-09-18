<?php

namespace App\Livewire\Components\Main\Payroll;

use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalEditPayrollPeriod extends Component
{
    public PayrollPeriod $period;

    public bool $show = false;

    public string $name = '';

    public string $startDate = '';

    public string $endDate = '';

    public ?string $paymentDate = null;


    public function mount(PayrollPeriod $period): void
    {
        $this->period = $period;

        $this->name = $period->name;

        $this->startDate = $period->start_date->format('Y-m-d');

        $this->endDate = $period->end_date->format('Y-m-d');

        $this->paymentDate = $period->payment_date?->format('Y-m-d');
    }


    public function save(): void
    {
        abort_unless(
            Auth::user()->can('edit-period-payroll'),
            403
        );

        if ($this->period->status !== 'draft') {
            $this->addError(
                'form',
                'Payroll periode yang sudah diproses tidak dapat diedit.'
            );

            return;
        }


        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'startDate' => [
                'required',
                'date',
            ],

            'endDate' => [
                'required',
                'date',
                'after_or_equal:startDate',
            ],

            'paymentDate' => [
                'nullable',
                'date',
            ],
        ]);


        $hasOverlap = PayrollPeriod::query()
            ->whereKeyNot($this->period->id)
            ->whereNotIn('status', [
                'cancelled',
            ])
            ->whereDate(
                'start_date',
                '<=',
                $validated['endDate']
            )
            ->whereDate(
                'end_date',
                '>=',
                $validated['startDate']
            )
            ->exists();


        if ($hasOverlap) {
            $this->addError(
                'startDate',
                'Periode payroll bertabrakan dengan periode yang sudah ada.'
            );

            return;
        }


        $this->period->update([
            'name' => $validated['name'],
            'start_date' => $validated['startDate'],
            'end_date' => $validated['endDate'],
            'payment_date' => $validated['paymentDate'],
        ]);


        $this->period->refresh();


        $this->show = false;


        $this->dispatch(
            'payroll-period-updated'
        );

        $this->dispatch('wirekit-modal-close', name: 'edit-payroll-period');
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'berhasil',
            message: 'Periode payroll berhasil diperbarui.'
        );
    }


    public function render()
    {
        return view(
            'livewire.components.main.payroll.modal-edit-payroll-period'
        );
    }
}
