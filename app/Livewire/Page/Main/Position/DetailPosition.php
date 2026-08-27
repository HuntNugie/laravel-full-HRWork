<?php

namespace App\Livewire\Page\Main\Position;

use App\Models\Position;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail Jabatan'])]
class DetailPosition extends Component
{
    public Position $position;

    public function mount(Position $position)
    {
        $this->authorize("view", $position);
        $this->position = $position->load(['employees', 'jobdesk']);
        $this->dispatch('open-edit', id: $position->id);
    }

    #[On('updated-position')]
    public function updatedPage()
    {
        $this->mount($this->position);
    }
    public function render()
    {
        return view('livewire.page.main.position.detail-position');
    }
}
