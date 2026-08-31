<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User as ModelsUser;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman manajemen User'])]
class User extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function totalPending()
    {
        return ModelsUser::where('status', 'pending')->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->count();
    }
    #[Computed]
    public function totalUser()
    {
        return ModelsUser::query()->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->count();
    }
    #[Computed]
    public function totalActive()
    {
        return ModelsUser::where('status', 'active')->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->count();
    }
    #[Computed]
    public function totalInactive()
    {
        return ModelsUser::where('status', 'inactive')->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->count();
    }

    public function render()
    {
        $users = ModelsUser::query()->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->when($this->search, function ($q) {
            $q->where(function ($qe) {
                $qe->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('employees', function ($q) {
                        $q->where('employee_code', 'like', '%' . $this->search . '%');
                    });
            });
        })->latest()->paginate(5);

        return view('livewire.page.main.user.user', compact('users'));
    }
}
