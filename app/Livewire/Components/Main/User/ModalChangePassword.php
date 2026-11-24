<?php

namespace App\Livewire\Components\Main\User;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ModalChangePassword extends Component
{
    public User $user;

    #[Validate([
        'required',
        'string',
        'min:12',
    ], message: [
        'newPassword.required' => 'password wajib di isi',
        'newPassword.min' => 'password minimal 12 karakter'
    ])]
    public string $newPassword = '';


    #[Validate(['required', 'string', 'same:newPassword'], message: [
        'password_confirmation.required' => 'konfirmasi password wajib di isi',
        'password_confirmation.same' => 'Konfirmasi password tidak sama dengan password',
    ])]
    public string $password_confirmation = '';

    public function canSubmit()
    {
        return filled($this->newPassword) && filled($this->password_confirmation);
    }

    public function changePassword()
    {
        abort_unless(auth()->user()->can('reset-user-password'), 403);
        abort_unless($this->user->hasRole('hr') && ! $this->user->hasRole('administrator'), 403);

        $this->validate();

        $this->user->update([
            'password' => $this->newPassword,
            'remember_token' => null,
        ]);

        \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $this->user->id)->delete();

        $this->dispatch('wirekit-modal-close', name: 'change-password');
        $this->dispatch('change-user',);
        $this->dispatch('wirekit-toast', variant: "success", title: "Berhasil ubah password", message: "Selamat anda berhasil merubah password {$this->user->name}");

        $this->reset([
            'newPassword',
            'password_confirmation',
        ]);
    }
    public function render()
    {
        return view('livewire.components.main.user.modal-change-password');
    }
}
