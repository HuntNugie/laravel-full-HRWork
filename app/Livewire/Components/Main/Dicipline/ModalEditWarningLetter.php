<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use App\Service\WarningLetterService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalEditWarningLetter extends Component
{
    public EmployeeWarningLetter $warningLetter;

    public string $employeeId = '';

    public string $warningLevel = 'SP1';

    public string $letterNumber = '';

    public string $issuedDate = '';

    public string $reason = '';

    public string $description = '';

    public function mount(EmployeeWarningLetter $warningLetter): void
    {
        $this->warningLetter = $warningLetter;

        $this->loadFromModel();
    }

    public function open(): void
    {
        abort_unless(
            Auth::user()->can('edit-warning-letter'),
            403
        );

        $this->warningLetter->refresh();

        if ($this->warningLetter->status !== 'draft') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat diedit',
                message: 'Surat Peringatan yang sudah diterbitkan atau dibatalkan tidak dapat diedit.'
            );

            return;
        }

        $this->loadFromModel();

        $this->dispatch(
            'wirekit-modal-show',
            name: 'edit-warning-letter-' . $this->warningLetter->id
        );
    }

    private function loadFromModel(): void
    {
        $this->employeeId = (string) $this->warningLetter->employee_id;
        $this->warningLevel = $this->warningLetter->warning_level;
        $this->letterNumber = $this->warningLetter->letter_number ?? '';
        $this->issuedDate = $this->warningLetter->issued_date?->toDateString() ?? '';
        $this->reason = $this->warningLetter->reason;
        $this->description = $this->warningLetter->description ?? '';
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

    public function save(): void
    {
        abort_unless(
            Auth::user()->can('edit-warning-letter'),
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

        try {
            $this->warningLetter = app(WarningLetterService::class)
                ->updateDraft(
                    warningLetter: $this->warningLetter,
                    attributes: [
                        'employee_id' => (int) $validated['employeeId'],
                        'warning_level' => $validated['warningLevel'],
                        'issued_date' => $validated['issuedDate'],
                        'reason' => $validated['reason'],
                        'description' => $validated['description'] ?? null,
                    ],
                );
        } catch (\RuntimeException $e) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'warning',
                title: 'Tidak dapat diedit',
                message: $e->getMessage()
            );

            return;
        }

        $this->loadFromModel();

        $this->dispatch('warning-letter-updated');

        $this->dispatch(
            'wirekit-modal-close',
            name: 'edit-warning-letter-' . $this->warningLetter->id
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Surat Peringatan berhasil diperbarui.'
        );
    }

    public function render()
    {
        return view(
            'livewire.components.main.dicipline.modal-edit-warning-letter'
        );
    }
}
