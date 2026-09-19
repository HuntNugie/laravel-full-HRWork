<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
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

        $this->employeeId = (string) $warningLetter->employee_id;
        $this->warningLevel = $warningLetter->warning_level;
        $this->letterNumber = $warningLetter->letter_number ?? '';
        $this->issuedDate = $warningLetter->issued_date?->toDateString() ?? '';
        $this->reason = $warningLetter->reason;
        $this->description = $warningLetter->description ?? '';
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
                'toast',
                type: 'error',
                message: 'Surat Peringatan yang sudah diterbitkan atau dibatalkan tidak dapat diedit.'
            );

            return;
        }

        $this->employeeId = (string) $this->warningLetter->employee_id;
        $this->warningLevel = $this->warningLetter->warning_level;
        $this->letterNumber = $this->warningLetter->letter_number ?? '';
        $this->issuedDate = $this->warningLetter->issued_date?->toDateString() ?? '';
        $this->reason = $this->warningLetter->reason;
        $this->description = $this->warningLetter->description ?? '';

        $this->dispatch(
            'wirekit-modal-show',
            name: 'edit-warning-letter-' . $this->warningLetter->id
        );
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

        $this->warningLetter->refresh();

        if ($this->warningLetter->status !== 'draft') {
            abort(422, 'Surat Peringatan sudah tidak dapat diedit.');
        }

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

        $this->warningLetter->update([
            'employee_id' => $validated['employeeId'],
            'warning_level' => $validated['warningLevel'],
            'issued_date' => $validated['issuedDate'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
        ]);

        $this->warningLetter->refresh();

        $this->dispatch('warning-letter-updated');

        $this->dispatch(
            'wirekit-modal-close',
            name: 'edit-warning-letter-' . $this->warningLetter->id
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Berhasil mengedit surat peringatan',
        );
    }

    public function render()
    {
        return view(
            'livewire.components.main.dicipline.modal-edit-warning-letter'
        );
    }
}
