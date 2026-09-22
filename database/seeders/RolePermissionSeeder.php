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
            ],

            'hr' => [
                // Organization
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

                // Employee & user
                'view-employee',
                'show-employee',
                'create-employee',
                'update-employee',
                'create-user',
                'view-user',
                'show-user',

                // Contract
                'view-contract',
                'show-contract',
                'create-contract',
                'update-contract',
                'download-employee-contract',

                // Benefit
                'view-benefit',
                'show-benefit',
                'create-benefit',
                'update-benefit',
                'delete-benefit',

                // Payroll
                'view-payroll',
                'show-payroll',
                'create-period-payroll',
                'delete-period-payroll',
                'edit-period-payroll',
                'process-payroll',
                'mark-paid-payroll',

                // Leave & absence
                'view-management-leave',
                'show-management-leave',
                'process-leave',
                'view-manage-absence',
                'view-absence',
                'view-type-leave',
                'show-type-leave',

                // Attendance management
                'show-attendance',
                'update-attendance',
                'view-monitor-attendance',
                'history-attendance',
                'view-status-daily',

                // Work time & holiday
                'view-work-time',
                'update-work-time',
                'view-holiday',
                'create-holiday',
                'update-holiday',
                'delete-holiday',

                // Discipline
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
            ],

            'administrator' => [
                // User management
                'view-user',
                'show-user',
                'create-user',
                'update-user',

                // Role management
                'view-role',
                'show-role',
                'create-role',
                'update-role',
                'delete-role',
                'assign-role',

                // System monitoring
                'view-status-daily',
            ],

            'general-manager' => [
                // Master project
                'view-master-project',
                'create-master-project',
                'update-master-project',
                'approve-master-project',

                // Division project
                'view-division-project',
                'create-division-project',
                'update-division-project',
                'assign-project-team',
                'report-project-progress',
                'review-division-project',

                // Task
                'view-task',
                'create-task',
                'assign-task',
                'update-task',
                'update-own-task',
                'submit-task',
                'review-task',
            ],

            'manager' => [
                // Master project visibility
                'view-master-project',

                // Division project
                'view-division-project',
                'update-division-project',
                'assign-project-team',
                'report-project-progress',
                'review-division-project',

                // Task management
                'view-task',
                'assign-task',
                'update-task',
            ],

            'supervisor' => [
                // Project visibility
                'view-master-project',
                'view-division-project',
                'report-project-progress',
                'submit-division-project-report',

                // Team task management
                'view-task',
                'create-task',
                'assign-task',
                'update-task',
                'review-task',
            ],

            'task-worker' => [
                'view-master-project',
                'view-division-project',
                'view-task',
                'update-own-task',
                'submit-task',
            ],
        ];

        // Ensure every operational role exists before syncing permissions.
        foreach (array_keys($rolePermissions) as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // Two legacy permissions are intentionally not assigned to operational roles.
        // They remain available to super-admin through the full-permission grant below.
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
