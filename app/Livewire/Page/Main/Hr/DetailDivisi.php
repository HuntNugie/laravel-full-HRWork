<?php

namespace App\Livewire\Page\Main\Hr;

use App\Models\Divisi;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main',['title' => 'Detail Divisi'])]
class DetailDivisi extends Component
{
    public Divisi $divisi;

    public function mount(Divisi $divisi){
        $this->authorize('view',$divisi);
        $this->divisi = $divisi->load('team');
    }
    public function render()
    {
        return view('livewire.page.main.hr.detail-divisi');
    }
}
