<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Dashboard\Dashboard;
use App\Models\Employees;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HrDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class]);
    }

    public function test_hr_uses_employee_style_dashboard_with_permission_scoped_data(): void
    {
        $hr = User::factory()->create(['status' => 'active']);
        $hr->assignRole('hr');
        $hr->givePermissionTo('view-employee');

        Employees::create(['name' => 'Employee Example', 'email' => 'employee@example.test', 'status_employee' => 'active']);

        $this->actingAs($hr);
        Livewire::test(Dashboard::class)
            ->assertSee('Dashboard HRD')
            ->assertSee('Total Karyawan')
            ->assertSee('Status Kepegawaian')
            ->assertDontSee('Kontrak Mendekati Akhir')
            ->assertDontSee('System Administration');
    }

    public function test_hr_with_contract_access_sees_contract_summary(): void
    {
        $hr = User::factory()->create(['status' => 'active']);
        $hr->assignRole('hr');
        $hr->givePermissionTo('view-contract');

        $this->actingAs($hr);
        Livewire::test(Dashboard::class)
            ->assertSee('Dashboard HRD')
            ->assertSee('Kontrak Mendekati Akhir')
            ->assertSee('Ringkasan Kontrak')
            ->assertDontSee('Status Kepegawaian');
    }

    public function test_administrator_keeps_separate_dashboard(): void
    {
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('administrator');

        $this->actingAs($admin);
        Livewire::test(Dashboard::class)
            ->assertSee('System Administration')
            ->assertDontSee('Dashboard HRD');
    }
}
