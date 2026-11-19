<?php

namespace Tests\Feature;

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HRISWorkManagementRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_retired_work_management_tables_and_legacy_task_columns_are_removed(): void
    {
        foreach ([
            'master_projects', 'division_projects', 'project_teams', 'tasks',
            'task_reviews', 'project_progress_updates', 'project_reviews',
            'project_reports', 'work_management_audits',
        ] as $table) {
            $this->assertFalse(Schema::hasTable($table), "Table {$table} should be retired.");
            $this->assertFalse(Schema::hasTable('archive_' . $table), "Archive for {$table} should be removed.");
        }

        foreach (['employee_resignation_handover_items', 'employee_termination_handover_items'] as $table) {
            $this->assertFalse(Schema::hasColumn($table, 'task_id'));
            $this->assertFalse(Schema::hasColumn($table, 'legacy_task_id'));
        }

        $this->assertTrue(Schema::hasTable('employee_resignation_handover_items'));
        $this->assertTrue(Schema::hasTable('employee_termination_handover_items'));
        $this->assertTrue(Schema::hasTable('work_times'));
    }

    public function test_hris_permissions_and_super_admin_access_remain_available(): void
    {
        $this->seed([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class]);

        $this->assertSame(0, Role::query()->whereIn('name', [
            'general-manager', 'task-worker', 'manager', 'supervisor',
        ])->count());

        $superAdmin = Role::findByName('super-admin', 'web');
        $this->assertTrue($superAdmin->hasPermissionTo('view-employee'));
        $this->assertTrue($superAdmin->hasPermissionTo('complete-termination'));
        $this->assertTrue($superAdmin->hasPermissionTo('view-ai-assistant'));
        $this->assertTrue($superAdmin->hasPermissionTo('reset-user-password'));
    }
}
