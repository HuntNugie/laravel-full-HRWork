<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use App\Service\WarningLetterService;
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

        try {
            $this->warningLetter = app(WarningLetterService::class)
                ->issue(
                    warningLetter: $this->warningLetter,
                    issuedBy: (int) Auth::id(),
                );
        } catch (\RuntimeException $e) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat diterbitkan',
                message: $e->getMessage()
            );

            return;
        }

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
