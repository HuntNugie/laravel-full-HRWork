<?php

namespace App\Livewire\Components\Main\Payroll;

use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ModalCreatePayrollPeriod extends Component
{
    public ?string $name = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?string $paymentDate = null;


    public function submit(): void
    {
        $this->validate(
            [
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
            ],
            [
                'name.required' =>
                'Nama periode wajib diisi.',

                'name.max' =>
                'Nama periode maksimal 255 karakter.',

                'startDate.required' =>
                'Tanggal mulai wajib diisi.',

                'startDate.date' =>
                'Tanggal mulai tidak valid.',

                'endDate.required' =>
                'Tanggal selesai wajib diisi.',

                'endDate.after_or_equal' =>
                'Tanggal selesai harus sama atau setelah tanggal mulai.',

                'paymentDate.date' =>
                'Tanggal pembayaran tidak valid.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Cek periode bentrok
        |--------------------------------------------------------------------------
        */

        $hasOverlap = PayrollPeriod::query()
            ->whereNotIn('status', ['cancelled'])
            ->whereDate(
                'start_date',
                '<=',
                $this->endDate
            )
            ->whereDate(
                'end_date',
                '>=',
                $this->startDate
            )
            ->exists();


        if ($hasOverlap) {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Periode Tidak Valid',
                message: 'Periode penggajian bertabrakan dengan periode yang sudah ada.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () {

            PayrollPeriod::create([
                'name' =>
                $this->name,

                'start_date' =>
                $this->startDate,

                'end_date' =>
                $this->endDate,

                'payment_date' =>
                $this->paymentDate,

                'status' =>
                'draft',

                'created_by' =>
                Auth::id(),
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        $this->resetForm();


        /*
        |--------------------------------------------------------------------------
        | Refresh halaman
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'payroll-period-refresh'
        );


        /*
        |--------------------------------------------------------------------------
        | Tutup modal
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-modal-close',
            name: 'create-payroll-period'
        );


        /*
        |--------------------------------------------------------------------------
        | Toast
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Periode penggajian berhasil dibuat.'
        );
    }


    private function resetForm(): void
    {
        $this->reset([
            'name',
            'startDate',
            'endDate',
            'paymentDate',
        ]);

        $this->resetValidation();
    }


    public function render()
    {
        return view(
            'livewire.components.main.payroll.modal-create-payroll-period'
        );
    }
}
