<?php

namespace App\Livewire\Page\Main\Team;

use App\Models\Team as ModelsTeam;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.main', ['title' => 'Halaman Team'])]
class Team extends Component
{
    use WithPagination;
    public string $search = '';
    public function mount()
    {
        $this->authorize('viewAny', ModelsTeam::class);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('create-team')]
    public function refreshPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $teams = ModelsTeam::with(['divisi','employees'])->when($this->search, fn($q) => $q->where('name', 'LIKE', '%' . $this->search . '%'))->latest()->paginate(5);
        return view('livewire.page.main.team.team', compact('teams'));
    }
}
