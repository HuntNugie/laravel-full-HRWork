<?php

namespace App\Livewire\Page\Main\Hr;

use App\Models\divisi as ModelsDivisi;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main',['title'=>'Halaman divisi'])]
class Divisi extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.page.main.hr.divisi',[
            'divisis' => ModelsDivisi::paginate(5)
        ]);
    }
}
