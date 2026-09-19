<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
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

        $this->warningLetter->refresh();

        if (! in_array($this->warningLetter->status, ['draft', 'issued'], true)) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat dibatalkan',
                message: 'Surat Peringatan yang sudah dibatalkan tidak dapat dibatalkan kembali.'
            );

            return;
        }

        $validated = $this->validate([
            'cancellationReason' => [
                'required',
                'string',
            ],
        ]);

        $this->warningLetter->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellationReason'],
        ]);

        $this->warningLetter->refresh();

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
