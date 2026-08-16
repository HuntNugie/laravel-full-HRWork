<?php

namespace App\Livewire\Page\Main\Hr;

use App\Models\Divisi as ModelsDivisi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman divisi'])]
class Divisi extends Component
{
    use WithPagination;

    public string $search = '';

    public function updateSearch(){
        $this->resetPage();
    }

    #[On('create-divisi')]
    public function refreshDivisi()
    {
        $this->resetPage();
    }
    public function render()
    {
        $divisis = ModelsDivisi::query()
            ->when($this->search, fn($q) => $q->where('name', 'LIKE', '%' . $this->search . '%'))
            ->latest()
            ->paginate(5);
        return view('livewire.page.main.hr.divisi', [
            'divisis' => $divisis
        ]);
    }
}
