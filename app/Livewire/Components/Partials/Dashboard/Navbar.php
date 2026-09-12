<?php

namespace App\Livewire\Components\Partials\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public User $user;
    public function mount()
    {
        $this->user = Auth::user();
    }

    #[On('change-profile')]
    public function fresh()
    {
        $this->js('window.location.reload()');
    }
    public function render()
    {
        return view('livewire.components.partials.dashboard.navbar');
    }
}
