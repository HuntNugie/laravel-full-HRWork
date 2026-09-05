<?php

namespace App\Livewire\Page\Main\Roles;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.main', ['title' => 'Halaman Roles'])]
class Roles extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }
    #[On('delete-role')]
    public function notifDelete()
    {
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil menghapus Role',
            message: 'Role berhasil dihapus.'
        );
    }

    public function render()
    {
        $roles = Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(5);

        return view('livewire.page.main.roles.roles', compact('roles'));
    }
}
