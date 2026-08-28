<?php

namespace App\Livewire\Page\Main\Team;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail Team'])]
class DetailTeam extends Component
{
    public Team $team;
    public function mount(Team $team)
    {
        $this->team = $team->load(['employees', 'divisi', 'supervisor']);
        $this->dispatch('refresh-edit', id: $team->id);
    }

    #[On('update-team')]
    public function refresh()
    {
        $this->mount($this->team);
    }
    public function render()
    {
        return view('livewire.page.main.team.detail-team');
    }
}
