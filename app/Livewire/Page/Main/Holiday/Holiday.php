<?php

namespace App\Livewire\Page\Main\Holiday;

use App\Models\Holidays;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman hari libur'])]
class Holiday extends Component
{
    public string $search = '';
    public function mount()
    {
        $this->authorize('viewAny', Holidays::class);
    }

    #[On('refreshPage')]
    public function refreshPage()
    {
        $this->mount();
    }

    public function render()
    {
        $holidays = Holidays::query()->when($this->search, function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%');
        })->orderBy('date', 'asc')->get();
        return view('livewire.page.main.holiday.holiday', compact('holidays'));
    }
}
