<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\Employees;
use App\Service\WarningLetterService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalCreateWarningLetter extends Component
{
    public string $employeeId = '';

    public string $employeeSearch = '';

    public bool $employeeDropdownOpen = false;

    public string $selectedEmployeeName = '';

    public string $warningLevel = 'SP1';

    public string $letterNumber = '';

    public string $issuedDate = '';

    public string $reason = '';

    public string $description = '';

    public function mount(): void
    {
        $this->issuedDate = now()->toDateString();
        $this->loadPreviewNumber();
    }

    public function open(): void
    {
        abort_unless(
            Auth::user()->can('create-warning-letter'),
            403
        );

        $this->resetForm();
        $this->loadPreviewNumber();

        $this->dispatch(
            'wirekit-modal-show',
            name: 'create-warning-letter'
        );
    }

    #[On('prepare-warning-letter-from-indication')]
    public function prepareFromIndication(
        int $employeeId,
        string $reason,
        string $description,
    ): void {
        abort_unless(
            Auth::user()->can('create-warning-letter'),
            403
        );

        $this->resetForm();

        $employee = Employees::query()
            ->whereKey($employeeId)
            ->with('user')
            ->firstOrFail();

        $this->employeeId = (string) $employee->id;
        $this->selectedEmployeeName = ($employee->user?->name ?? '—')
            . ' · '
            . ($employee->employee_code ?? '—');
        $this->employeeSearch = $this->selectedEmployeeName;

        $this->reason = $reason;
        $this->description = $description;

        $this->loadPreviewNumber();

        $this->dispatch(
            'wirekit-modal-show',
            name: 'create-warning-letter'
        );
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

    private function loadPreviewNumber(): void
    {
        $this->letterNumber = app(
            WarningLetterService::class
        )->previewWarningLetterNumber();
    }

    private function resetForm(): void
    {
        $this->reset([
            'employeeId',
            'employeeSearch',
            'selectedEmployeeName',
            'reason',
            'description',
            'employeeDropdownOpen',
        ]);

        $this->warningLevel = 'SP1';
        $this->issuedDate = now()->toDateString();
        $this->letterNumber = '';
    }

    public function employeeOptions(): array
    {
        return Employees::query()
            ->with('user')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn(Employees $employee) => [
                $employee->id => $employee->user->name .
                    ' (' . $employee->employee_code . ')',
            ])
            ->toArray();
    }

    public function employees(): \Illuminate\Support\Collection
    {
        $search = trim($this->employeeSearch);

        if (mb_strlen($search) < 2 || $this->employeeId) {
            return collect();
        }

        return Employees::query()
            ->where(function ($query) use ($search) {
                $query
                    ->where('employee_code', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            })
            ->with('user')
            ->orderBy('employee_code')
            ->limit(10)
            ->get();
    }

    public function save(): void
    {
        abort_unless(
            Auth::user()->can('create-warning-letter'),
            403
        );

        $validated = $this->validate([
            'employeeId' => [
                'required',
                'exists:employees,id',
            ],
            'warningLevel' => [
                'required',
                'in:SP1,SP2,SP3',
            ],
            'issuedDate' => [
                'required',
                'date',
            ],
            'reason' => [
                'required',
                'string',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        app(WarningLetterService::class)->createDraft(
            attributes: [
                'employee_id' => (int) $validated['employeeId'],
                'warning_level' => $validated['warningLevel'],
                'issued_date' => $validated['issuedDate'],
                'reason' => $validated['reason'],
                'description' => $validated['description'] ?? null,
            ],
            createdBy: (int) Auth::id(),
        );

        $this->dispatch('warning-letter-saved');

        $this->dispatch(
            'wirekit-modal-close',
            name: 'create-warning-letter'
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Surat Peringatan berhasil dibuat sebagai draft.'
        );

        $this->resetForm();
        $this->loadPreviewNumber();
    }

    public function render()
    {
        return view(
            'livewire.components.main.dicipline.modal-create-warning-letter',
            [
                'employees' => $this->employees(),
            ]
        );
    }
}
