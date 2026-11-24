<?php

namespace App\Livewire\Page\Main\User;

use App\Models\User;
use App\Support\HrAccess;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Hak Akses HRD'])]
class DetailRolePermission extends Component
{
    public User $user;
    public array $permissions = [];

    private function guardTarget(): void
    {
        abort_unless(auth()->user()->can('manage-hr-access'), 403);
        abort_unless($this->user->hasRole('hr') && ! $this->user->hasRole('administrator'), 404);
    }

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->guardTarget();
        $this->permissions = $user->getDirectPermissions()->pluck('name')->intersect(HrAccess::hr())->values()->all();
    }

    public function selectAllPermissions(): void
    {
        $this->guardTarget();
        $this->permissions = HrAccess::hr();
    }

    public function clearPermissions(): void
    {
        $this->guardTarget();
        $this->permissions = [];
    }

    public function selectGroup(string $group): void
    {
        $this->guardTarget();
        abort_unless(array_key_exists($group, HrAccess::GROUPS), 422);
        $this->permissions = array_values(array_unique(array_merge($this->permissions, HrAccess::GROUPS[$group])));
    }

    public function save(): void
    {
        $this->guardTarget();
        $this->validate(['permissions' => 'array', 'permissions.*' => ['string', 'distinct', Rule::in(HrAccess::hr())]]);
        $this->user->syncPermissions($this->permissions);
        $this->user->refresh();
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil', message: 'Hak akses HRD diperbarui.');
    }

    public function render()
    {
        $this->guardTarget();
        return view('livewire.page.main.user.detail-role-permission', ['groups' => HrAccess::GROUPS]);
    }
}
