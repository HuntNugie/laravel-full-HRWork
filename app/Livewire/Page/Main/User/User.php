<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User as ModelsUser;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.main', ['title' => 'Halaman manajemen User'])]
class User extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'roleFilter']);
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
        $users = ModelsUser::query()
            ->with(['employees.position', 'roles'])
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super-admin');
            })
            ->when($this->search, function ($q) {
                $q->where(function ($qe) {
                    $qe->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhereHas('employees', function ($q) {
                            $q->where('employee_code', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->roleFilter, function ($q) {
                $q->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', $this->roleFilter);
                });
            })
            ->latest()
            ->paginate(5);

        $roles = Role::query()
            ->where('name', '!=', 'super-admin')
            ->orderBy('name')
            ->pluck('name', 'name')
            ->mapWithKeys(fn ($name) => [$name => str($name)->headline()])
            ->all();

        return view('livewire.page.main.user.user', compact('users', 'roles'));
    }
}
