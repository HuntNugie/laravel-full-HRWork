<?php

namespace App\Livewire\Page\Main\Resignation;

use App\Models\EmployeeResignation;
use App\Models\Employees;
use App\Service\ResignationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Pengunduran Diri Saya'])]
class MyResignation extends Component
{
    public Employees $employee;

    public string $proposedLastWorkingDate = '';
    public string $reason = '';
    public string $notes = '';

    public function mount(): void
    {
        $this->employee = Auth::user()
            ->employees()
            ->firstOrFail();

        $this->proposedLastWorkingDate = today()->toDateString();
    }

    #[Computed]
    public function resignation(): ?EmployeeResignation
    {
        return $this->employee
            ->resignations()
            ->with([
                'employeeContract',
                'reviewer',
                'histories.actor',
                'clearances.verifier',
                'handoverItems.handoverTo',
                'finalPayrolls',
            ])
            ->latest('id')
            ->first();
    }

    public function submit(): void
    {
        $this->validate([
            'proposedLastWorkingDate' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            app(ResignationService::class)->create(
                employee: $this->employee,
                submittedBy: Auth::user(),
                proposedLastWorkingDate: $this->proposedLastWorkingDate,
                reason: $this->reason,
                notes: $this->notes ?: null,
            );

            $this->reset(['reason', 'notes']);
            unset($this->resignation);

            session()->flash('success', 'Pengajuan resign berhasil dikirim.');
        } catch (\LogicException $exception) {
            $this->addError('form', $exception->getMessage());
        }
    }

    public function cancel(): void
    {
        $resignation = $this->resignation;

        if (!$resignation) {
            return;
        }

        if ($resignation->status !== EmployeeResignation::STATUS_SUBMITTED) {
            $this->addError('form', 'Pengajuan yang sudah disetujui harus diproses oleh HR.');
            return;
        }

        try {
            app(ResignationService::class)->cancel(
                resignation: $resignation,
                actor: Auth::user(),
            );

            unset($this->resignation);

            session()->flash('success', 'Pengajuan resign berhasil dibatalkan.');
        } catch (\LogicException $exception) {
            $this->addError('form', $exception->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.page.main.resignation.my-resignation');
    }
}
