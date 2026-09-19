<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalIssueWarningLetter extends Component
{
    public EmployeeWarningLetter $warningLetter;

    public function mount(EmployeeWarningLetter $warningLetter): void
    {
        $this->warningLetter = $warningLetter->load([
            'employee.user',
        ]);
    }

    public function issue(): void
    {
        abort_unless(
            Auth::user()->can('issue-warning-letter'),
            403
        );

        $this->warningLetter->refresh();

        if ($this->warningLetter->status !== 'draft') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat diterbitkan',
                message: 'Surat Peringatan hanya dapat diterbitkan ketika berstatus draft.'
            );

            return;
        }

        $this->warningLetter->update([
            'status' => 'issued',
            'issued_by' => Auth::id(),
            'issued_at' => now(),
        ]);

        $this->warningLetter->refresh();

        $this->dispatch('warning-letter-issued');

        $this->dispatch(
            'wirekit-modal-close',
            name: 'issue-warning-letter-' . $this->warningLetter->id
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Surat Peringatan berhasil diterbitkan.'
        );
    }

    public function render()
    {
        return view(
            'livewire.components.main.dicipline.modal-issue-warning-letter'
        );
    }
}
