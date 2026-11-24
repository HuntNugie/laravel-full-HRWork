<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Manajemen Akun HRD'])]
class User extends Component
{
    use WithPagination;

    public string $search = '';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $showCreate = false;

    public function updatedSearch(): void { $this->resetPage(); }

    public function createAccount(): void
    {
        abort_unless(auth()->user()->can('create-user'), 403);
        $data = $this->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:255|unique:users,email', 'password' => 'required|string|min:12|confirmed']);
        $user = DB::transaction(function () use ($data) {
            $user = ModelsUser::create([...$data, 'status' => 'active']);
            $user->assignRole('hr');
            return $user;
        });
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'showCreate']);
        $this->redirectRoute('user.role.show', ['user' => $user->id], navigate: true);
    }

    public function render()
    {
        abort_unless(auth()->user()->can('view-user'), 403);
        $users = ModelsUser::role('hr')->whereDoesntHave('roles', fn ($q) => $q->where('name', 'administrator'))
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%')))
            ->latest()->paginate(10);
        return view('livewire.page.main.user.user', compact('users'));
    }
}
