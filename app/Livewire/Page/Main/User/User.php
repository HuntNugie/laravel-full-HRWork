<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
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
    public string $statusFilter = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function resetFilters(): void { $this->reset(['search', 'statusFilter']); $this->resetPage(); }

    private function accounts()
    {
        return ModelsUser::role('hr')->whereDoesntHave('roles', fn ($q) => $q->where('name', 'administrator'));
    }

    #[Computed] public function totalUser(): int { return $this->accounts()->count(); }
    #[Computed] public function totalActive(): int { return $this->accounts()->where('status', 'active')->count(); }
    #[Computed] public function totalPending(): int { return $this->accounts()->where('status', 'pending')->count(); }
    #[Computed] public function totalInactive(): int { return $this->accounts()->where('status', 'inactive')->count(); }


    public function createAccount(): void
    {
        abort_unless(auth()->user()->can('create-user'), 403);
        $data = $this->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:255|unique:users,email', 'password' => 'required|string|min:12|confirmed']);
        DB::transaction(function () use ($data) {
            $user = ModelsUser::create([...$data, 'status' => 'active']);
            $user->assignRole('hr');
            return $user;
        });
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'search', 'statusFilter']);
        $this->resetValidation();
        $this->resetPage();
        unset($this->totalUser, $this->totalActive, $this->totalPending, $this->totalInactive);
        $this->dispatch('wirekit-modal-close', name: 'create-hr-account');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil tambah akun', message: 'Akun HRD berhasil dibuat. Atur hak akses melalui halaman detail akun.');
    }

    public function render()
    {
        abort_unless(auth()->user()->can('view-user'), 403);
        $users = $this->accounts()->with(['roles', 'media'])
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%')))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()->paginate(10);
        return view('livewire.page.main.user.user', compact('users'));
    }
}
