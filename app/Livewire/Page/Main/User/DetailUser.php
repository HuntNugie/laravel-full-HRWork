<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail User'])]
class DetailUser extends Component
{
    public User $user;
    public function mount(User $user)
    {
        $this->user = $user->load(['employees']);
    }
    public function render()
    {
        return view('livewire.page.main.user.detail-user');
    }
}
