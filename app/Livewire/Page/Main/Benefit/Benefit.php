<?php

namespace App\Livewire\Page\Main\Benefit;

use App\Models\Benefit as ModelsBenefit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman Benefit Management'])]
class Benefit extends Component
{
    public string $search = '';

    use WithPagination;

    public function updateSearch()
    {
        $this->resetPage();
    }

    #[On('create-benefit')]
    public function refreshPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $benefits = ModelsBenefit::query()->when($this->search, function ($q) {
            return $q->where('name', 'like', '%' . $this->search . '%');
        })->withCount('contracts')->latest()->paginate(5);
        return view('livewire.page.main.benefit.benefit', compact('benefits'));
    }
}
