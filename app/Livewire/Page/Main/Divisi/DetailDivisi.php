<?php

namespace App\Livewire\Page\Main\Divisi;

use App\Models\Divisi;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main',['title' => 'Detail Divisi'])]
class DetailDivisi extends Component
{
    public Divisi $divisi;

    public function mount(Divisi $divisi){
        $this->authorize('view',$divisi);
        $this->divisi = $divisi->load('team');
    }

    #[On('update-divisi')]
    public function refreshDivisi(){
        $this->divisi->load('team');
    }
    public function render()
    {
        return view('livewire.page.main.divisi.detail-divisi');
    }
}
