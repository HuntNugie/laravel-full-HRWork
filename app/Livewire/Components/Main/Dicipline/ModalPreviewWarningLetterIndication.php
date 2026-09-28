<?php

namespace App\Livewire\Components\Main\Dicipline;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModalPreviewWarningLetterIndication extends Component
{
    public int $employeeId;

    public string $employeeName;

    public string $employeeCode;

    public int $unpresentCount;

    public int $threshold;

    public string $periodStart;

    public string $periodEnd;

    /** @var array<int, string> */
    public array $dates = [];

    public function mount(
        int $employeeId,
        string $employeeName,
        string $employeeCode,
        int $unpresentCount,
        int $threshold,
        string $periodStart,
        string $periodEnd,
        array $dates,
    ): void {
        $this->employeeId = $employeeId;
        $this->employeeName = $employeeName;
        $this->employeeCode = $employeeCode;
        $this->unpresentCount = $unpresentCount;
        $this->threshold = $threshold;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
        $this->dates = $dates;
    }

    public function setAsWarningLetter(): void
    {
        $this->dispatch(
            'prepare-warning-letter-from-indication',
            employeeId: $this->employeeId,
            reason: sprintf(
                'Ketidakhadiran tanpa keterangan sebanyak %d kali pada periode %s sampai %s.',
                $this->unpresentCount,
                \Carbon\Carbon::parse($this->periodStart)->translatedFormat('d F Y'),
                \Carbon\Carbon::parse($this->periodEnd)->translatedFormat('d F Y'),
            ),
            description: sprintf(
                'Terdapat %d hari unpresent yang memenuhi batas indikasi sebanyak %d kali.',
                $this->unpresentCount,
                $this->threshold,
            ),
        );
    }

    public function render(): View
    {
        return view(
            'livewire.components.main.dicipline.modal-preview-warning-letter-indication'
        );
    }
}
