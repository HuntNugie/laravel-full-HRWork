<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Seed the application's complete permission catalog.
     */
    public function run(): void
    {
        $permissions = [
            'assign-role',
            'cancel-leave',
            'cancel-warning-letter',
            'check-in-attendance',
            'check-out-attendance',
            'create-absence',
            'create-benefit',
            'create-contract',
            'create-employee',
            'create-holiday',
            'create-leave',
            'create-period-payroll',
            'create-position',
            'create-role',
            'create-user',
            'create-warning-letter',
            'delete-benefit',
            'delete-holiday',
            'delete-period-payroll',
            'delete-role',
            'download-employee-contract',
            'edit-late-discipline-rule',
            'edit-period-payroll',
            'edit-unpresent-discipline-rule',
            'edit-warning-letter',
            'history-attendance',
            'issue-warning-letter',
            'mark-paid-payroll',
            'process-leave',
            'process-payroll',
            'show-attendance',
            'show-benefit',
            'show-contract',
            'show-contract-my',
            'show-employee',
            'show-leave',
            'show-management-leave',
            'show-payroll',
            'show-payroll-my',
            'show-position',
            'show-role',
            'show-type-leave',
            'show-user',
            'show-warning-letter',
            'update-attendance',
            'update-benefit',
            'update-contract',
            'update-employee',
            'update-holiday',
            'update-position',
            'update-role',
            'update-user',
            'update-work-time',
            'view-absence',
            'view-ai-assistant',
            'view-cv-analyzer',
            'view-attendance',
            'view-benefit',
            'view-contract',
            'view-contract-my',
            'view-data-my',
            'view-employee',
            'view-holiday',
            'view-late-discipline-rule',
            'view-leave',
            'view-manage-absence',
            'view-management-leave',
            'view-monitor-attendance',
            'view-payroll',
            'view-payroll-my',
            'view-position',
            'view-role',
            'view-status-daily',
            'view-type-leave',
            'view-unpresent-discipline-rule',
            'view-user',
            'view-warning-letter',
            'view-warning-letter-my',
            'show-warning-letter-my',
            'view-resignation',
            'show-resignation',
            'approve-resignation',
            'reject-resignation',
            'cancel-resignation',
            'manage-resignation-clearance',
            'complete-resignation',
            'view-resignation-my',
            'show-resignation-my',
            'create-resignation-my',
            'cancel-resignation-my',
            'view-termination',
            'show-termination',
            'create-termination',
            'cancel-termination',
            'manage-termination-clearance',
            'complete-termination',
            'view-work-time',
            'reset-user-password',
            'change-user-status',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Remove retired permissions from existing installations as well.
        Permission::query()->where('guard_name', 'web')->whereIn('name', [
            'approve-master-project', 'assign-project-team', 'assign-task',
            'create-division-project', 'create-master-project', 'create-task',
            'report-project-progress', 'review-division-project', 'review-project', 'review-task',
            'submit-project-review', 'submit-division-project-report',
            'submit-division-project-to-gm', 'submit-task',
            'update-division-project', 'update-master-project', 'update-own-task', 'update-task',
            'view-division-project', 'view-master-project', 'view-task',
            'assign-team-employee', 'create-divisi', 'create-team',
            'delete-divisi', 'remove-team-employee', 'show-divisi',
            'show-team', 'update-divisi', 'update-team',
            'view-divisi', 'view-team',
        ])->get()->each(fn (Permission $permission) => $permission->delete());
    }
}
