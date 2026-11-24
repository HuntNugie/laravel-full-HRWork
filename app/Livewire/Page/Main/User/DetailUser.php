<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Detail Akun HRD'])]
class DetailUser extends Component
{
    public User $user;
    public string $name = '';
    public string $email = '';

    private function guardTarget(string $ability): void
    {
        abort_unless(auth()->user()->can($ability), 403);
        abort_unless($this->user->hasRole('hr') && ! $this->user->hasRole('administrator'), 404);
    }

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->guardTarget('show-user');
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function save(): void
    {
        $this->guardTarget('update-user');
        $this->user->update($this->validate(['name' => 'required|string|max:100', 'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)]]));
        $this->user->refresh();
        $this->resetValidation();
        $this->dispatch('wirekit-modal-close', name: 'edit-hr-account');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil', message: 'Akun HRD berhasil diperbarui.');
    }

    public function actionStatusAccount(): void
    {
        $this->guardTarget('change-user-status');
        $this->user->update(['status' => $this->user->status === 'active' ? 'inactive' : 'active', 'remember_token' => null]);
        \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $this->user->id)->delete();
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil', message: 'Status akun diperbarui.');
    }

    public function render()
    {
        $this->guardTarget('show-user');
        return view('livewire.page.main.user.detail-user');
    }
}
