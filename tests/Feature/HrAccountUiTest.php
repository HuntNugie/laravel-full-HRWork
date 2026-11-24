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
            ->call('selectGroup', 'Karyawan')
            ->assertSet('permissions', HrAccess::GROUPS['Karyawan'])
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('wirekit-toast');

        $this->assertEqualsCanonicalizing(HrAccess::GROUPS['Karyawan'], $hr->fresh()->getDirectPermissions()->pluck('name')->all());
    }
}
