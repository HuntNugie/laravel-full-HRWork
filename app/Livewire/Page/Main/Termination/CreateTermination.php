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
    public string $employeeSearch = '';
    public bool $employeeDropdownOpen = false;
    public string $selectedEmployeeName = '';
    public string $reasonType = '';
    public string $proposedEffectiveDate = '';
    public string $reason = '';
    public string $notes = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->can('create-termination'), 403);

        $this->proposedEffectiveDate = today()->toDateString();
    }

    public function updatedEmployeeSearch(): void
    {
        $this->employeeDropdownOpen = true;

        if ($this->employeeSearch !== $this->selectedEmployeeName) {
            $this->employeeId = '';
            $this->selectedEmployeeName = '';
        }
    }

    public function openEmployeeDropdown(): void
    {
        $this->employeeDropdownOpen = true;
    }

    public function closeEmployeeDropdown(): void
    {
        $this->employeeDropdownOpen = false;
    }

    public function selectEmployee(int $employeeId): void
    {
        $employee = Employees::query()
            ->whereKey($employeeId)
            ->where('status_employee', 'active')
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
            ->whereHas('employeeContract', function ($query) {
                $query
                    ->where('status', 'active')
                    ->whereDate('start_date', '<=', today())
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', today());
                    });
            })
            ->with('user')
            ->firstOrFail();

        $this->employeeId = (string) $employee->id;
        $this->selectedEmployeeName = ($employee->user?->name ?? '—')
            . ' · '
            . ($employee->employee_code ?? '—');
        $this->employeeSearch = $this->selectedEmployeeName;
        $this->employeeDropdownOpen = false;

        $this->resetErrorBag('employeeId');
    }

    public function clearSelectedEmployee(): void
    {
        $this->employeeId = '';
        $this->employeeSearch = '';
        $this->selectedEmployeeName = '';
        $this->employeeDropdownOpen = true;
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
                effectiveDate: $this->proposedEffectiveDate,
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
        $employees = collect();

        $search = trim($this->employeeSearch);

        if (mb_strlen($search) >= 2) {
            $employees = Employees::query()
                ->where('status_employee', 'active')
                ->whereHas('user', fn ($query) => $query->where('status', 'active'))
                ->where(function ($query) use ($search) {
                    $query
                        ->where('employee_code', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                })
                ->whereHas('employeeContract', function ($query) {
                    $query
                        ->where('status', 'active')
                        ->whereDate('start_date', '<=', today())
                        ->where(function ($query) {
                            $query->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', today());
                        });
                })
                ->with('user')
                ->orderBy('employee_code')
                ->limit(10)
                ->get();
        }

        $reasonTypes = app(TerminationService::class)->reasonTypes();

        return view('livewire.page.main.termination.create-termination', compact(
            'employees',
            'reasonTypes',
        ));
    }
}
