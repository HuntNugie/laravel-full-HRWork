<?php

namespace App\Livewire\Page\Main\Hr;

use App\Models\Divisi as ModelsDivisi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main',['title'=>'Halaman divisi'])]
class Divisi extends Component
{
    use WithPagination;

    #[On('create-divisi')]
    public function refreshDivisi(){
        $this->resetPage();
    }
    public function render()
    {
        return view('livewire.page.main.hr.divisi',[
            'divisis' => ModelsDivisi::latest()->paginate(5)
        ]);
    }
}
