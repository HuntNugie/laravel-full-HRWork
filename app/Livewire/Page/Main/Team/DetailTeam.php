<?php

namespace App\Livewire\Page\Main\Team;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail Team'])]
class DetailTeam extends Component
{
    public Team $team;
    public function mount(Team $team)
    {
        $this->team = $team->load(['employees', 'divisi', 'supervisor']);
    }
    public function render()
    {
        return view('livewire.page.main.team.detail-team');
    }
}
