<?php

namespace App\Livewire\Page\Main\Termination;

use App\Models\Employees;
use App\Service\TerminationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Buat PHK'])]
class CreateTermination extends Component
{
    public string $employeeId = '';
    public string $reasonType = '';
    public string $proposedEffectiveDate = '';
    public string $reason = '';
    public string $notes = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->can('create-termination'), 403);

        $this->proposedEffectiveDate = today()->toDateString();
    }

    public function save(): void
    {
        abort_unless(Auth::user()->can('create-termination'), 403);

        $this->validate([
            'employeeId' => ['required', 'integer', 'exists:employees,id'],
            'reasonType' => ['required', 'string', 'max:50'],
            'proposedEffectiveDate' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        try {
            $termination = app(TerminationService::class)->create(
                employee: Employees::query()->findOrFail((int) $this->employeeId),
                initiatedBy: Auth::user(),
                proposedEffectiveDate: $this->proposedEffectiveDate,
                reasonType: $this->reasonType,
                reason: $this->reason,
                notes: $this->notes ?: null,
            );

            session()->flash('success', 'Pengajuan PHK berhasil dibuat.');
            $this->redirectRoute('termination.show', $termination, navigate: true);
        } catch (\LogicException $exception) {
            $this->addError('action', $exception->getMessage());
        }
    }

    public function render()
    {
        $employees = Employees::query()
            ->where('status_employee', 'active')
            ->with('user')
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
            ->orderBy('employee_code')
            ->get()
            ->mapWithKeys(fn (Employees $employee) => [
                $employee->id => ($employee->user?->name ?? '—') . ' · ' . ($employee->employee_code ?? '—'),
            ])
            ->all();

        $reasonTypes = app(TerminationService::class)->reasonTypes();

        return view('livewire.page.main.termination.create-termination', compact(
            'employees',
            'reasonTypes',
        ));
    }
}
