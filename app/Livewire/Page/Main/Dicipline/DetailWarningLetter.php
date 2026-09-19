<?php

namespace App\Livewire\Page\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Surat Peringatan'])]
class DetailWarningLetter extends Component
{
    public EmployeeWarningLetter $warningLetter;

    public function mount(EmployeeWarningLetter $warningLetter): void
    {
        $this->warningLetter = $warningLetter->load([
            'employee.user',
            'creator',
            'issuer',
            'canceller',
        ]);
    }

    public function statusLabel(): string
    {
        return match ($this->warningLetter->status) {
            'draft' => 'Draft',
            'issued' => 'Diterbitkan',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->warningLetter->status),
        };
    }

    public function statusIntent(): string
    {
        return match ($this->warningLetter->status) {
            'draft' => 'warning',
            'issued' => 'success',
            'cancelled' => 'danger',
            default => 'neutral',
        };
    }

    #[On('warning-letter-updated')]
    #[On('warning-letter-cancelled')]
    public function refreshWarningLetter(): void
    {
        $this->warningLetter->refresh();

        $this->warningLetter->load([
            'employee.user',
            'creator',
            'issuer',
            'canceller',
        ]);
    }

    public function render(): View
    {
        return view(
            'livewire.page.main.dicipline.detail-warning-letter'
        );
    }
}
