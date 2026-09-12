<?php

namespace App\Livewire\Page\Main\Base;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'My Profile'])]
class MyProfile extends Component
{
    public User $user;

    public function mount()
    {

        $this->user = Auth::user();
    }
    #[On('change-profile')]
    public function fresh()
    {
        $this->user = Auth::user()->fresh();
    }

    public function render()
    {
        return view('livewire.page.main.base.my-profile');
    }
}
