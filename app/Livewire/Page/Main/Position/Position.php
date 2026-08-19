<?php

namespace App\Livewire\Page\Main\Position;

use App\Models\Position as ModelsPosition;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main',['title' => 'Halaman Manajemen Jabatan'])]
class Position extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(){
        $this->resetPage();
    }

    #[On('create-position')]
    public function render()
    {
        $positions = ModelsPosition::with('employees')->when($this->search,function($q){
            $q->where('name','LIKE','%'.$this->search.'%');
        })->latest()->paginate(5);
        return view('livewire.page.main.position.position',compact('positions'));
    }
}
