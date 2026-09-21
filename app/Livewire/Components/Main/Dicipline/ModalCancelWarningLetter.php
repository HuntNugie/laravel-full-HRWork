<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use App\Service\WarningLetterService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalCancelWarningLetter extends Component
{
    public EmployeeWarningLetter $warningLetter;

    public string $cancellationReason = '';

    public function mount(EmployeeWarningLetter $warningLetter): void
    {
        $this->warningLetter = $warningLetter->load([
            'employee.user',
        ]);
    }

    public function cancel(): void
    {
        abort_unless(
            Auth::user()->can('cancel-warning-letter'),
            403
        );

        $validated = $this->validate([
            'cancellationReason' => [
                'required',
                'string',
            ],
        ]);

        try {
            $this->warningLetter = app(WarningLetterService::class)
                ->cancel(
                    warningLetter: $this->warningLetter,
                    reason: $validated['cancellationReason'],
                    cancelledBy: (int) Auth::id(),
                );
        } catch (\RuntimeException $e) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat dibatalkan',
                message: $e->getMessage()
            );

            return;
        }

        $this->dispatch('warning-letter-cancelled');

        $this->dispatch(
            'wirekit-modal-close',
            name: 'cancel-warning-letter-' . $this->warningLetter->id
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Surat Peringatan berhasil dibatalkan.'
        );

        $this->cancellationReason = '';
    }

    public function render()
    {
        return view(
            'livewire.components.main.dicipline.modal-cancel-warning-letter'
        );
    }
}
