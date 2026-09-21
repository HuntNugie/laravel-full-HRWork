<?php

namespace App\Livewire\Components\Main\Leave;

use App\Models\ContractLeaveEntitlements;
use App\Models\Employees;
use App\Service\LeaveRequestService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalLeaveRequest extends Component
{
    public ?int $leaveTypeId = null;

    public ?string $startDate = null;

    public ?string $endDate = null;

    public int $totalDays = 0;

    public ?string $reason = null;

    public Collection $entitlements;

    public function mount(): void
    {
        $employee = Auth::user()->employees;
        $service = app(LeaveRequestService::class);

        $contract = $employee
            ? $service->currentActiveContract($employee)
            : null;

        $this->entitlements = $contract
            ? $contract->contractLeave()->with('leaveType')->get()
            : new Collection();
    }

    public function getSelectedEntitlementProperty(): ?ContractLeaveEntitlements
    {
        if (!$this->leaveTypeId) {
            return null;
        }

        return $this->entitlements->firstWhere(
            'leave_type_id',
            $this->leaveTypeId
        );
    }

    public function usedLeave(int $leaveTypeId): int
    {
        $entitlement = $this->entitlements->firstWhere(
            'leave_type_id',
            $leaveTypeId
        );

        return $entitlement
            ? app(LeaveRequestService::class)->usedDays($entitlement, now()->year)
            : 0;
    }

    public function pendingLeave(int $leaveTypeId): int
    {
        $entitlement = $this->entitlements->firstWhere(
            'leave_type_id',
            $leaveTypeId
        );

        return $entitlement
            ? app(LeaveRequestService::class)->pendingDays($entitlement, now()->year)
            : 0;
    }

    public function remainingLeave(
        ContractLeaveEntitlements $entitlement
    ): int {
        return app(LeaveRequestService::class)
            ->remainingDays($entitlement, now()->year);
    }

    public function updatedStartDate(): void
    {
        $this->calculateTotalDays();
    }

    public function updatedEndDate(): void
    {
        $this->calculateTotalDays();
    }

    private function calculateTotalDays(): void
    {
        $this->totalDays = 0;

        if (!$this->startDate || !$this->endDate) {
            return;
        }

        try {
            $start = \Carbon\Carbon::parse($this->startDate);
            $end = \Carbon\Carbon::parse($this->endDate);
        } catch (\Throwable) {
            return;
        }

        if ($end->lt($start)) {
            return;
        }

        $this->totalDays = $start->diffInDays($end) + 1;
    }

    public function submit(LeaveRequestService $leaveRequestService): void
    {
        $this->validate([
            'leaveTypeId' => ['required', 'integer'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'leaveTypeId.required' => 'Silakan pilih jenis cuti.',
            'leaveTypeId.integer' => 'Jenis cuti tidak valid.',
            'startDate.required' => 'Tanggal mulai wajib diisi.',
            'startDate.date' => 'Tanggal mulai tidak valid.',
            'endDate.required' => 'Tanggal selesai wajib diisi.',
            'endDate.date' => 'Tanggal selesai tidak valid.',
            'endDate.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'reason.required' => 'Alasan pengajuan cuti wajib diisi.',
            'reason.min' => 'Alasan pengajuan minimal 5 karakter.',
            'reason.max' => 'Alasan pengajuan maksimal 1000 karakter.',
        ]);

        $employee = Auth::user()->employees;

        if (!$employee) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Data karyawan tidak ditemukan.'
            );

            return;
        }

        $this->calculateTotalDays();

        try {
            $leaveRequestService->createPending(
                employee: $employee,
                leaveTypeId: $this->leaveTypeId,
                startDate: $this->startDate,
                endDate: $this->endDate,
                reason: $this->reason,
            );
        } catch (\LogicException $exception) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Pengajuan Tidak Dapat Diproses',
                message: $exception->getMessage()
            );

            return;
        }

        $this->resetForm();
        $this->dispatch('leave-request');
        $this->dispatch(
            'wirekit-modal-close',
            name: 'create-leave-request'
        );
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Pengajuan cuti berhasil dikirim dan menunggu persetujuan.'
        );
    }

    public function resetForm(): void
    {
        $this->reset([
            'leaveTypeId',
            'startDate',
            'endDate',
            'totalDays',
            'reason',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view(
            'livewire.components.main.leave.modal-leave-request'
        );
    }
}
