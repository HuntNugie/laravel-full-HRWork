<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the final role -> permission mapping.
     *
     * PermissionSeeder owns the permission catalog.
     * This seeder owns which permissions belong to each role.
     */
    public function run(): void
    {
        $rolePermissions = [
            'employee' => [
                'check-in-attendance',
                'check-out-attendance',
                'create-absence',
                'create-leave',
                'cancel-leave',
                'show-leave',
                'view-attendance',
                'view-contract-my',
                'show-contract-my',
                'view-data-my',
                'view-holiday',
                'view-payroll-my',
                'show-payroll-my',
                'view-leave',
                'view-work-time',
                'view-warning-letter-my',
                'show-warning-letter-my',
                'view-resignation-my',
                'show-resignation-my',
                'create-resignation-my',
                'cancel-resignation-my',
            ],

            'hr' => [
                'view-ai-assistant',
                'view-cv-analyzer',
                'view-divisi',
                'show-divisi',
                'create-divisi',
                'update-divisi',
                'delete-divisi',
                'view-team',
                'show-team',
                'create-team',
                'update-team',
                'assign-team-employee',
                'remove-team-employee',
                'view-position',
                'show-position',
                'create-position',
                'update-position',
                'view-employee',
                'show-employee',
                'create-employee',
                'update-employee',
                'create-user',
                'view-user',
                'show-user',
                'view-contract',
                'show-contract',
                'create-contract',
                'update-contract',
                'download-employee-contract',
                'view-benefit',
                'show-benefit',
                'create-benefit',
                'update-benefit',
                'delete-benefit',
                'view-payroll',
                'show-payroll',
                'create-period-payroll',
                'delete-period-payroll',
                'edit-period-payroll',
                'process-payroll',
                'mark-paid-payroll',
                'view-management-leave',
                'show-management-leave',
                'process-leave',
                'view-manage-absence',
                'view-absence',
                'view-type-leave',
                'show-type-leave',
                'show-attendance',
                'update-attendance',
                'view-monitor-attendance',
                'history-attendance',
                'view-status-daily',
                'view-work-time',
                'update-work-time',
                'view-holiday',
                'create-holiday',
                'update-holiday',
                'delete-holiday',
                'view-late-discipline-rule',
                'edit-late-discipline-rule',
                'view-unpresent-discipline-rule',
                'edit-unpresent-discipline-rule',
                'view-warning-letter',
                'show-warning-letter',
                'create-warning-letter',
                'edit-warning-letter',
                'issue-warning-letter',
                'cancel-warning-letter',
                'view-resignation',
                'show-resignation',
                'approve-resignation',
                'reject-resignation',
                'cancel-resignation',
                'manage-resignation-clearance',
                'complete-resignation',
                'view-termination',
                'show-termination',
                'create-termination',
                'cancel-termination',
                'manage-termination-clearance',
                'complete-termination',
            ],

            'administrator' => [
                'view-user',
                'show-user',
                'create-user',
                'update-user',
                'view-role',
                'show-role',
                'create-role',
                'update-role',
                'delete-role',
                'assign-role',
                'view-status-daily',
            ],

            'general-manager' => [
                'view-master-project',
                'create-master-project',
                'approve-master-project',
                'view-division-project',
                'create-division-project',
                'assign-project-team',
                'review-division-project',
                'view-task',
                'view-termination',
                'show-termination',
            ],

            'manager' => [
                'view-master-project',
                'view-division-project',
                'assign-project-team',
                'view-task',
                'submit-division-project-to-gm',
            ],

            'supervisor' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'create-task',
                'update-task',
            ],

            'task-worker' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'update-own-task',
            ],
        ];

        foreach (array_keys($rolePermissions) as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(
            Permission::query()
                ->where('guard_name', 'web')
                ->get()
        );

        foreach ($rolePermissions as $roleName => $permissionNames) {
            Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->firstOrFail()
                ->syncPermissions(
                    Permission::query()
                        ->where('guard_name', 'web')
                        ->whereIn('name', $permissionNames)
                        ->get()
                );
        }
    }
}
