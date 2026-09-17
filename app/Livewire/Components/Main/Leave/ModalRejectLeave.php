<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalRejectLeave extends Component
{
    public LeaveRequest $request;

    public ?string $rejectionReason = null;

    public function mount(LeaveRequest $request): void
    {
        $this->request = $request;
    }

    public function reject(): void
    {
        $this->validate([
            'rejectionReason' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ], [
            'rejectionReason.required' =>
            'Alasan penolakan wajib diisi.',

            'rejectionReason.min' =>
            'Alasan penolakan minimal 5 karakter.',

            'rejectionReason.max' =>
            'Alasan penolakan maksimal 1000 karakter.',
        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan request masih pending
    |--------------------------------------------------------------------------
    */

        $this->request->refresh();

        if ($this->request->status !== 'pending') {

            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Tidak dapat diproses',
                message: 'Pengajuan cuti ini sudah tidak berstatus menunggu.'
            );

            return;
        }


        /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */

        $this->request->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $this->rejectionReason,
        ]);


        /*
    |--------------------------------------------------------------------------
    | Refresh model
    |--------------------------------------------------------------------------
    */

        $this->request->refresh();


        /*
    |--------------------------------------------------------------------------
    | Reset form
    |--------------------------------------------------------------------------
    */

        $this->rejectionReason = null;

        $this->resetValidation();


        /*
    |--------------------------------------------------------------------------
    | Beritahu parent management leave
    |--------------------------------------------------------------------------
    */

        $this->dispatch('leave-management-refresh');


        /*
    |--------------------------------------------------------------------------
    | Tutup modal
    |--------------------------------------------------------------------------
    */

        $this->dispatch(
            'wirekit-modal-close',
            name: 'reject-leave-' . $this->request->id
        );

        $this->dispatch(
            'wirekit-modal-close',
            name: 'management-leave-detail-' . $this->request->id
        );


        /*
    |--------------------------------------------------------------------------
    | Toast
    |--------------------------------------------------------------------------
    */

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Pengajuan ditolak',
            message: 'Pengajuan cuti berhasil ditolak.'
        );
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-reject-leave'
        );
    }
}
