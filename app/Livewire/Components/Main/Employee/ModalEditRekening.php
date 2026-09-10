<?php

namespace App\Livewire\Components\Main\Employee;

use App\Models\Bank;
use App\Models\Employees;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ModalEditRekening extends Component
{
    public Employees $employee;

    #[Validate(['required', 'exists:banks,id'])]
    public string $bankId = "";

    #[Validate(['required', 'numeric'], message: ['accountNumber.required' => 'wajib mengisi nomor rekening', 'accountNumber.numeric' => 'yang di isi wajib angka'])]
    public string $noRek = "";


    #[Validate(['required'], message: ['accountHolder.required' => 'Nama pemilik rekening wajib di isi'])]
    public string $holder = "";

    public function mount()
    {
        $this->bankId = $this->employee->profile->bankAccount->bank_id;
        $this->noRek = $this->employee->profile->bankAccount->account_number;
        $this->holder = $this->employee->profile->bankAccount->account_holder;
    }

    public function update()
    {
        $this->authorize('update', $this->employee);

        $this->validate();

        $this->employee->profile->bankAccount->update([
            'bank_id' => $this->bankId,
            'account_number' => $this->noRek,
            'account_holder' => $this->holder
        ]);

        $this->dispatch("wirekit-modal-close", name: 'edit-rekening');
        $this->dispatch('change-team');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil mengupdate rekening', message: "anda berhasil merubah rekening");
    }

    public function canSubmit()
    {
        return filled($this->bankId) && $this->noRek && $this->holder;
    }
    public function render()
    {
        $banks = Bank::query()->pluck('name', 'id')->toArray();
        return view('livewire.components.main.employee.modal-edit-rekening', compact('banks'));
    }
}
