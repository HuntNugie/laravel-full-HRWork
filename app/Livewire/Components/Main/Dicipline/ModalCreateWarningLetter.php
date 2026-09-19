<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use App\Service\WarningLetterService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalCreateWarningLetter extends Component
{
    public string $employeeId = '';

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

    /**
     * Dibuka ketika HR membuat SP secara manual.
     */
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

    /**
     * Dibuka dari Preview Indikasi Pelanggaran.
     */
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

        $this->employeeId = (string) $employeeId;
        $this->reason = $reason;
        $this->description = $description;

        $this->loadPreviewNumber();

        $this->dispatch(
            'wirekit-modal-show',
            name: 'create-warning-letter'
        );
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
            'reason',
            'description',
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

        $warningLetterService = app(
            WarningLetterService::class
        );

        $letterNumber = $warningLetterService
            ->generateWarningLetterNumber();

        EmployeeWarningLetter::create([
            'employee_id' => $validated['employeeId'],
            'warning_level' => $validated['warningLevel'],
            'letter_number' => $letterNumber,
            'issued_date' => $validated['issuedDate'],
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

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
            'livewire.components.main.dicipline.modal-create-warning-letter'
        );
    }
}
