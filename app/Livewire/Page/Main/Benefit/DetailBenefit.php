<?php

namespace App\Livewire\Page\Main\Benefit;

use App\Models\Benefit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail Benefit'])]
class DetailBenefit extends Component
{
    public Benefit $benefit;
    public function mount(Benefit $benefit)
    {
        $this->benefit = $benefit->load('contracts');
        $this->dispatch('refresh-edit', id: $benefit->id);
    }

    #[On('update-benefit')]
    public function refreshPage()
    {
        $this->mount($this->benefit);
    }
    public function render()
    {
        return view('livewire.page.main.benefit.detail-benefit');
    }
}
