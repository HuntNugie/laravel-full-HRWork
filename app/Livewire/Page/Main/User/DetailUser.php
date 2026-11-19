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
        abort_unless(auth()->user()->can('change-user-status'), 403);
        abort_if($this->user->hasRole('super-admin') && ! auth()->user()->hasRole('super-admin'), 403);
        abort_if($this->user->is(auth()->user()), 403, 'Tidak dapat mengubah status akun sendiri.');

        if ($this->user->status !== 'active' && $this->user->employees && $this->user->employees->latestEmployeeContract?->status !== 'active') {
            $this->dispatch('wirekit-toast', variant: 'danger', title: 'Gagal aktifasi', message: "Akun contract nya tidak aktif");
            return;
        }
        $this->user->update([
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
