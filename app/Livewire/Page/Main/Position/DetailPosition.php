<?php

namespace App\Livewire\Page\Main\Position;

use App\Models\Position;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main',['title'=>'Halaman Detail Jabatan'])]
class DetailPosition extends Component
{
    public Position $position;
    public function mount(Position $position){
        $this->position = $position->load(['employees']);
    }

    public function render()
    {
        return view('livewire.page.main.position.detail-position');
    }
}
