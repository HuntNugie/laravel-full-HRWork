<?php

namespace App\Livewire\Components\Main\MyProfile;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ModalChangePassword extends Component
{
    public User $user;

    public string $oldPassword = "";
    public string $newPassword = "";
    public string $newPassword_confirmation = "";

    public function save()
    {
        $this->validate([
            'oldPassword' => [
                'required',
                'string',
                'current_password'
            ],
            'newPassword' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],

        ], [
            'oldPassword.required' => 'Password lama wajib di isi',
            'oldPassword.current_password' => 'Password lama tidak sesuai',
            'newPassword.required' => 'Password baru wajib di isi',
            'newPassword.min' => 'Password minimal 8 karakter',
            'newPassword.confirmed' => 'konfirmasi password tidak sama dengan password',
        ]);

        $this->user->update([
            'password' => bcrypt($this->newPassword),
        ]);

        $this->dispatch('wirekit-modal-close', name: 'change-password');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Ubah Password', message: 'Anda berhasil merubah Anda');
        $this->dispatch('change-profile');
    }
    public function render()
    {
        return view('livewire.components.main.my-profile.modal-change-password');
    }
}
