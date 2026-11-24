<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\User\DetailRolePermission;
use App\Livewire\Page\Main\User\DetailUser;
use App\Livewire\Page\Main\User\User as AccountPage;
use App\Models\User;
use App\Support\HrAccess;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HrAccountUiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class]);
    }

    private function admin(): User
    {
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('administrator');
        return $admin;
    }

    public function test_create_account_closes_modal_and_dispatches_success(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(AccountPage::class)
            ->set('name', 'HR Example')
            ->set('email', 'hr@example.test')
            ->set('password', 'TestOnlyPassword123!')
            ->set('password_confirmation', 'TestOnlyPassword123!')
            ->call('createAccount')
            ->assertHasNoErrors()
            ->assertDispatched('wirekit-modal-close', name: 'create-hr-account')
            ->assertDispatched('wirekit-toast')
            ->assertSet('password', '')
            ->assertSee('HR Example');

        $hr = User::where('email', 'hr@example.test')->firstOrFail();
        $this->assertTrue($hr->hasRole('hr'));
        $this->assertCount(0, $hr->getAllPermissions());
    }

    public function test_edit_account_closes_modal_and_dispatches_success(): void
    {
        $this->actingAs($this->admin());
        $hr = User::factory()->create(['status' => 'active']);
        $hr->assignRole('hr');

        Livewire::test(DetailUser::class, ['user' => $hr])
            ->set('name', 'Updated HR')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('wirekit-modal-close', name: 'edit-hr-account')
            ->assertDispatched('wirekit-toast');

        $this->assertSame('Updated HR', $hr->fresh()->name);
    }

    public function test_permission_group_selection_and_save_dispatch_success(): void
    {
        $this->actingAs($this->admin());
        $hr = User::factory()->create(['status' => 'active']);
        $hr->assignRole('hr');

        Livewire::test(DetailRolePermission::class, ['user' => $hr])
            ->assertSeeHtml('wire:click="selectGroup(0)"')
            ->assertSeeHtml('wire:click="selectGroup(4)"')
            ->set('permissions', ['view-benefit'])
            ->call('selectGroup', 0)
            ->assertSet('permissions', array_merge(['view-benefit'], HrAccess::GROUPS['Karyawan']))
            ->call('selectGroup', 0)
            ->assertSet('permissions', array_merge(['view-benefit'], HrAccess::GROUPS['Karyawan']))
            ->call('selectGroup', 4)
            ->assertSet('permissions', array_merge(['view-benefit'], HrAccess::GROUPS['Karyawan'], HrAccess::GROUPS['Jenis cuti']))
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('wirekit-toast');

        $this->assertEqualsCanonicalizing(array_merge(['view-benefit'], HrAccess::GROUPS['Karyawan'], HrAccess::GROUPS['Jenis cuti']), $hr->fresh()->getDirectPermissions()->pluck('name')->all());

        Livewire::test(DetailRolePermission::class, ['user' => $hr])
            ->call('selectAllPermissions')
            ->assertSet('permissions', HrAccess::hr())
            ->call('clearPermissions')
            ->assertSet('permissions', [])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertCount(0, $hr->fresh()->getDirectPermissions());
    }
}
