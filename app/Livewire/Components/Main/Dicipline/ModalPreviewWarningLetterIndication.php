<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Service\UnpresentDisciplineService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ModalPreviewWarningLetterIndication extends Component
{
    public int $employeeId;

    public function mount(int $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    #[Computed]
    public function indication(): ?array
    {
        return app(UnpresentDisciplineService::class)
            ->getCandidates()
            ->firstWhere('employee.id', $this->employeeId);
    }

    public function setAsWarningLetter(): void
    {
        $indication = $this->indication;

        if (!$indication) {
            return;
        }

        $this->dispatch(
            'prepare-warning-letter-from-indication',
            employeeId: $indication['employee']->id,
            reason: sprintf(
                'Ketidakhadiran tanpa keterangan sebanyak %d kali pada periode %s sampai %s.',
                $indication['unpresent_count'],
                \Carbon\Carbon::parse($indication['period_start'])->translatedFormat('d F Y'),
                \Carbon\Carbon::parse($indication['period_end'])->translatedFormat('d F Y'),
            ),
            description: sprintf(
                'Terdapat %d hari unpresent yang memenuhi batas indikasi sebanyak %d kali.',
                $indication['unpresent_count'],
                $indication['threshold'],
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
