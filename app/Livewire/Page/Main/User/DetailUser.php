<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail User'])]
class DetailUser extends Component
{
    public User $user;
    public function mount(User $user)
    {
        $this->user = $user->load(['employees']);
    }

    public function actionStatusAccount()
    {
        if (!$this->user?->employees?->latestEmployeeContract && $this->user?->employees?->latestEmployeeContract?->status !== 'active') {
            $this->dispatch('wirekit-toast', variant: 'danger', title: 'Gagal aktifasi', message: "Akun contract nya tidak aktif");
            return;
        }
        $user = $this->user->update([
            'status' => $this->user->status === 'active'
                ? 'inactive'
                : 'active',
        ]);

        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil aktifasi', message: "anda berhasil mengaktifkan akun");
    }

    #[On('change-user')]
    public function changeUser()
    {
        $this->user->load(['employees']);
    }

    public function render()
    {
        return view('livewire.page.main.user.detail-user');
    }
}
