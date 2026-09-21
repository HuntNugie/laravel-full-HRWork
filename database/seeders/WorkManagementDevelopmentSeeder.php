<?php

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\ContractLeaveEntitlements;
use App\Models\Divisi;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class WorkManagementDevelopmentSeeder extends Seeder
{
    /**
     * Seed a small, coherent HRWork environment for local development/testing.
     *
     * The seeder is intentionally idempotent for development data.
     */
    public function run(): void
    {
        $this->seedPositions();

        $developmentDivision = $this->seedDivision(
            'Development',
            'Divisi pengembangan aplikasi.',
        );

        $managementDivision = $this->seedDivision(
            'Management',
            'Divisi manajemen untuk akun pengelola HRWork.',
        );

        $users = $this->seedUsers();
        $employees = $this->seedEmployees(
            $users,
            $developmentDivision,
            $managementDivision,
        );

        $this->assignDivisionManager($developmentDivision, $employees['manager']);

        $team = Team::updateOrCreate(
            ['name' => 'Development Team'],
            [
                'divisi_id' => $developmentDivision->id,
                'description' => 'Team development untuk kebutuhan development/testing HRWork.',
                'is_active' => 'active',
                'supervisor_id' => $employees['supervisor']->id,
            ],
        );

        $employees['supervisor']->update(['team_id' => $team->id]);
        $employees['worker']->update(['team_id' => $team->id]);

        $this->seedContracts($employees);
        $this->seedBenefits($employees);
        $this->seedLeaveEntitlement($employees);

        $this->command?->info('Development HR/Work Management data seeded.');
        $this->command?->line('GM: gm@hrwork.test / password');
        $this->command?->line('Manager: manager@hrwork.test / password');
        $this->command?->line('Supervisor: supervisor@hrwork.test / password');
        $this->command?->line('Task Worker: worker@hrwork.test / password');
    }

    private function seedPositions(): void
    {
        $positions = [
            [
                'name' => 'General Manager',
                'description' => 'Penanggung jawab final Work Management.',
                'min_salary_daily' => 500000,
            ],
            [
                'name' => 'Manager',
                'description' => 'Penanggung jawab Division Project.',
                'min_salary_daily' => 350000,
            ],
            [
                'name' => 'Supervisor',
                'description' => 'Penanggung jawab distribusi task dalam team.',
                'min_salary_daily' => 275000,
            ],
            [
                'name' => 'Software Engineer',
                'description' => 'Task worker untuk pekerjaan development.',
                'min_salary_daily' => 225000,
            ],
            [
                'name' => 'HR Officer',
                'description' => 'Pengelola modul HR.',
                'min_salary_daily' => 275000,
            ],
            [
                'name' => 'Administrator',
                'description' => 'Pengelola administrasi aplikasi.',
                'min_salary_daily' => 250000,
            ],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['name' => $position['name']],
                [
                    'description' => $position['description'],
                    'min_salary_daily' => $position['min_salary_daily'],
                    'is_active' => 'active',
                ],
            );
        }
    }

    private function seedDivision(string $name, string $description): Divisi
    {
        return Divisi::updateOrCreate(
            ['name' => $name],
            [
                'description' => $description,
                'is_active' => 'active',
            ],
        );
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $accounts = [
            'gm' => [
                'name' => 'GM HRWork',
                'email' => 'gm@hrwork.test',
                'role' => 'general-manager',
            ],
            'manager' => [
                'name' => 'Manager Development',
                'email' => 'manager@hrwork.test',
                'role' => 'manager',
            ],
            'supervisor' => [
                'name' => 'Supervisor Development',
                'email' => 'supervisor@hrwork.test',
                'role' => 'supervisor',
            ],
            'worker' => [
                'name' => 'Worker Development',
                'email' => 'worker@hrwork.test',
                'role' => 'task-worker',
            ],
        ];

        $users = [];

        foreach ($accounts as $key => $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('password'),
                    'status' => 'active',
                ],
            );

            $roles = ['Employee', $account['role']];

            foreach ($roles as $roleName) {
                Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
            }

            $user->syncRoles($roles);
            $users[$key] = $user;
        }

        $hr = User::where('email', 'nugiekurniawan03@gmail.com')->firstOrFail();
        $admin = User::where('email', 'nugiekurniawan02@gmail.com')->firstOrFail();

        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);

        $hr->syncRoles(['Employee', 'HR']);
        $admin->syncRoles(['Employee', 'Administrator']);

        $users['hr'] = $hr;
        $users['admin'] = $admin;

        return $users;
    }

    /**
     * @param array<string, User> $users
     * @return array<string, Employees>
     */
    private function seedEmployees(
        array $users,
        Divisi $developmentDivision,
        Divisi $managementDivision,
    ): array {
        $positions = Position::query()
            ->whereIn('name', [
                'General Manager',
                'Manager',
                'Supervisor',
                'Software Engineer',
                'HR Officer',
                'Administrator',
            ])
            ->get()
            ->keyBy('name');

        $definitions = [
            'gm' => [
                'code' => 'DEV-GM-001',
                'position' => 'General Manager',
                'team_id' => null,
                'divisi_id' => $managementDivision->id,
            ],
            'manager' => [
                'code' => 'DEV-MGR-001',
                'position' => 'Manager',
                'team_id' => null,
                'divisi_id' => $developmentDivision->id,
            ],
            'supervisor' => [
                'code' => 'DEV-SPV-001',
                'position' => 'Supervisor',
                'team_id' => null,
                'divisi_id' => $developmentDivision->id,
            ],
            'worker' => [
                'code' => 'DEV-WKR-001',
                'position' => 'Software Engineer',
                'team_id' => null,
                'divisi_id' => $developmentDivision->id,
            ],
            'hr' => [
                'code' => 'DEV-HR-001',
                'position' => 'HR Officer',
                'team_id' => null,
                'divisi_id' => $managementDivision->id,
            ],
            'admin' => [
                'code' => 'DEV-ADM-001',
                'position' => 'Administrator',
                'team_id' => null,
                'divisi_id' => $managementDivision->id,
            ],
        ];

        $employees = [];

        foreach ($definitions as $key => $definition) {
            $employee = Employees::updateOrCreate(
                ['employee_code' => $definition['code']],
                [
                    'user_id' => $users[$key]->id,
                    'divisi_id' => $definition['divisi_id'],
                    'status_employee' => 'active',
                    'team_id' => $definition['team_id'],
                    'position_id' => $positions[$definition['position']]->id,
                    'ResignDate' => null,
                ],
            );

            $employees[$key] = $employee;
        }

        return $employees;
    }

    private function assignDivisionManager(Divisi $divisi, Employees $manager): void
    {
        $divisi->update([
            'manager_id' => $manager->id,
            'is_active' => 'active',
        ]);
    }

    /**
     * @param array<string, Employees> $employees
     */
    private function seedContracts(array $employees): void
    {
        $contracts = [
            'gm' => [
                'number' => 'DEV-GM-2026-001',
                'salary' => 500000,
                'position' => 'General Manager',
            ],
            'manager' => [
                'number' => 'DEV-MGR-2026-001',
                'salary' => 350000,
                'position' => 'Manager',
            ],
            'supervisor' => [
                'number' => 'DEV-SPV-2026-001',
                'salary' => 275000,
                'position' => 'Supervisor',
            ],
            'worker' => [
                'number' => 'DEV-WKR-2026-001',
                'salary' => 225000,
                'position' => 'Software Engineer',
            ],
            'hr' => [
                'number' => 'DEV-HR-2026-001',
                'salary' => 275000,
                'position' => 'HR Officer',
            ],
            'admin' => [
                'number' => 'DEV-ADM-2026-001',
                'salary' => 250000,
                'position' => 'Administrator',
            ],
        ];

        foreach ($contracts as $key => $contract) {
            EmployeeContract::updateOrCreate(
                ['contract_number' => $contract['number']],
                [
                    'employee_id' => $employees[$key]->id,
                    'position_name' => $contract['position'],
                    'employement_type' => 'pkwtt',
                    'start_date' => '2026-01-01',
                    'end_date' => null,
                    'salary_daily' => $contract['salary'],
                    'status' => 'active',
                    'notes' => 'Development seed contract.',
                ],
            );
        }
    }

    /**
     * @param array<string, Employees> $employees
     */
    private function seedBenefits(array $employees): void
    {
        $transport = Benefit::updateOrCreate(
            ['name' => 'Tunjangan Transport'],
            [
                'description' => 'Benefit transport untuk development seed.',
                'status' => 'active',
            ],
        );

        $meal = Benefit::updateOrCreate(
            ['name' => 'Tunjangan Makan'],
            [
                'description' => 'Benefit makan untuk development seed.',
                'status' => 'active',
            ],
        );

        foreach ($employees as $employee) {
            $contract = EmployeeContract::query()
                ->where('employee_id', $employee->id)
                ->where('status', 'active')
                ->latest('id')
                ->first();

            if (! $contract) {
                continue;
            }

            $contract->benefits()->syncWithoutDetaching([
                $transport->id => ['amount' => 50000],
                $meal->id => ['amount' => 25000],
            ]);
        }
    }

    /**
     * @param array<string, Employees> $employees
     */
    private function seedLeaveEntitlement(array $employees): void
    {
        $leaveType = LeaveType::updateOrCreate(
            ['name' => 'Cuti Tahunan'],
            [
                'default_days' => 12,
                'gender' => 'all',
                'description' => 'Cuti tahunan untuk development seed.',
                'status' => 'active',
            ],
        );

        foreach ($employees as $employee) {
            $contract = EmployeeContract::query()
                ->where('employee_id', $employee->id)
                ->where('status', 'active')
                ->latest('id')
                ->first();

            if (! $contract) {
                continue;
            }

            ContractLeaveEntitlements::updateOrCreate(
                [
                    'employee_contract_id' => $contract->id,
                    'leave_type_id' => $leaveType->id,
                ],
                ['days' => 12],
            );
        }
    }
}
