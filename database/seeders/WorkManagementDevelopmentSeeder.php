<?php

namespace Database\Seeders;

use App\Models\Benefit;
use App\Models\Bank;
use App\Models\EmployeeBankAccount;
use App\Models\EmployeeProfileAddress;
use App\Models\Employee_profile;
use App\Models\ContractLeaveEntitlements;
use App\Models\Divisi;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
        $this->seedWilayah();
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
        $employees = $this->seedEmployees($users);

        $this->assignDivisionManager($developmentDivision, $employees['manager']);

        $team = Team::updateOrCreate(
            ['name' => 'Development Team'],
            [
                'description' => 'Team development untuk kebutuhan development/testing HRWork.',
                'is_active' => 'active',
                'supervisor_id' => $employees['supervisor']->id,
            ],
        );

        $employees['supervisor']->update(['team_id' => $team->id]);
        $employees['worker']->update(['team_id' => $team->id]);

        $this->seedProfilesAndBankAccounts($employees);
        $this->seedStatusHistory($employees);
        $this->seedContracts($employees);
        $this->seedBenefits($employees);
        $this->seedLeaveEntitlement($employees);

        $this->command?->info('Development HR/Work Management data seeded.');
        $this->command?->line('GM: gm.hrwork@gmail.com / password');
        $this->command?->line('Manager: manager.hrwork@gmail.com / password');
        $this->command?->line('Supervisor: supervisor.hrwork@gmail.com / password');
        $this->command?->line('Task Worker: worker.hrwork@gmail.com / password');
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
                'email' => 'gm.hrwork@gmail.com',
                'role' => 'general-manager',
            ],
            'manager' => [
                'name' => 'Manager Development',
                'email' => 'manager.hrwork@gmail.com',
                'role' => 'manager',
            ],
            'supervisor' => [
                'name' => 'Supervisor Development',
                'email' => 'supervisor.hrwork@gmail.com',
                'role' => 'supervisor',
            ],
            'worker' => [
                'name' => 'Worker Development',
                'email' => 'worker.hrwork@gmail.com',
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

            $roles = ['employee', $account['role']];

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
        $superAdmin = User::where('email', env('SUPERADMIN_EMAIL', 'superadmin@gmail.com'))->firstOrFail();

        Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        $hr->syncRoles(['employee', 'hr']);
        $admin->syncRoles(['employee', 'administrator']);
        $superAdmin->syncRoles(['employee', 'super-admin']);

        $users['hr'] = $hr;
        $users['admin'] = $admin;
        $users['superadmin'] = $superAdmin;

        return $users;
    }

    /**
     * @param array<string, User> $users
     * @return array<string, Employees>
     */
    private function seedEmployees(array $users): array {
        $positions = Position::query()
            ->whereIn('name', [
                'General Manager',
                'Manager',
                'Supervisor',
                'Software Engineer',
                'HR Officer',
                'administrator',
            ])
            ->get()
            ->keyBy('name');

        $definitions = [
            'gm' => [
                'code' => 'DEV-GM-001',
                'position' => 'General Manager',
                'team_id' => null,
            ],
            'manager' => [
                'code' => 'DEV-MGR-001',
                'position' => 'Manager',
                'team_id' => null,
            ],
            'supervisor' => [
                'code' => 'DEV-SPV-001',
                'position' => 'Supervisor',
                'team_id' => null,
            ],
            'worker' => [
                'code' => 'DEV-WKR-001',
                'position' => 'Software Engineer',
                'team_id' => null,
            ],
            'hr' => [
                'code' => 'DEV-HR-001',
                'position' => 'HR Officer',
                'team_id' => null,
            ],
            'admin' => [
                'code' => 'DEV-ADM-001',
                'position' => 'Administrator',
                'team_id' => null,
            ],
            'superadmin' => [
                'code' => 'DEV-SADM-001',
                'position' => 'Administrator',
                'team_id' => null,
            ],
        ];

        $employees = [];

        foreach ($definitions as $key => $definition) {
            $employee = Employees::updateOrCreate(
                ['employee_code' => $definition['code']],
                [
                    'user_id' => $users[$key]->id,
                    'status_employee' => 'active',
                    'team_id' => $definition['team_id'],
                    'position_id' => $positions[$definition['position']]->id,
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
    private function seedWilayah(): void
    {
        if (! DB::table('villages')->exists()) {
            Artisan::call('wilayah:seed');
        }

        if (! DB::table('villages')->exists()) {
            throw new \RuntimeException(
                'Data wilayah belum tersedia. Pastikan package aliziodev/laravel-wilayah terpasang dan wilayah:seed berhasil.',
            );
        }
    }

    /**
     * @param array<string, Employees> $employees
     */
    private function seedProfilesAndBankAccounts(array $employees): void
    {
        $bank = Bank::query()
            ->where('short_name', 'BCA')
            ->firstOrFail();

        $villages = DB::table('villages')
            ->where('code', 'like', '32.%')
            ->whereNotNull('postal_code')
            ->orderBy('id')
            ->limit(count($employees))
            ->get(['code', 'name', 'postal_code']);

        if ($villages->count() < count($employees)) {
            throw new \RuntimeException(
                'Data desa/kelurahan Jawa Barat belum cukup untuk development seed.',
            );
        }

        $profiles = [
            'gm' => ['gender' => 'male', 'phone' => 'TESTPHONE-001', 'nik' => 'TEST-NIK-001', 'birth_date' => '1985-01-15', 'birth_address' => 'Bandung, Jawa Barat'],
            'manager' => ['gender' => 'male', 'phone' => 'TESTPHONE-002', 'nik' => 'TEST-NIK-002', 'birth_date' => '1990-04-22', 'birth_address' => 'Cimahi, Jawa Barat'],
            'supervisor' => ['gender' => 'female', 'phone' => 'TESTPHONE-003', 'nik' => 'TEST-NIK-003', 'birth_date' => '1995-07-10', 'birth_address' => 'Bandung, Jawa Barat'],
            'worker' => ['gender' => 'male', 'phone' => 'TESTPHONE-004', 'nik' => 'TEST-NIK-004', 'birth_date' => '1998-11-03', 'birth_address' => 'Cimahi, Jawa Barat'],
            'hr' => ['gender' => 'female', 'phone' => 'TESTPHONE-005', 'nik' => 'TEST-NIK-005', 'birth_date' => '1992-02-18', 'birth_address' => 'Bandung, Jawa Barat'],
            'admin' => ['gender' => 'male', 'phone' => 'TESTPHONE-006', 'nik' => 'TEST-NIK-006', 'birth_date' => '1991-08-27', 'birth_address' => 'Garut, Jawa Barat'],
            'superadmin' => ['gender' => 'male', 'phone' => 'TESTPHONE-007', 'nik' => 'TEST-NIK-007', 'birth_date' => '1988-12-09', 'birth_address' => 'Bandung, Jawa Barat'],
        ];

        foreach (array_keys($employees) as $index => $key) {
            $employee = $employees[$key];
            $profileData = $profiles[$key];
            $village = $villages[$index];

            $profile = Employee_profile::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'gender' => $profileData['gender'],
                    'phone_number' => $profileData['phone'],
                    'nik' => $profileData['nik'],
                    'birth_date' => $profileData['birth_date'],
                    'birth_address' => $profileData['birth_address'],
                ],
            );

            EmployeeProfileAddress::updateOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'full_address' => 'Jl. Development Test No. ' . ($index + 1) . ', ' . $village->name . ', Jawa Barat ' . $village->postal_code,
                    'village_code' => $village->code,
                ],
            );

            EmployeeBankAccount::updateOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'bank_id' => $bank->id,
                    'account_number' => 'TEST-BANK-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'account_holder' => $employee->user?->name ?? 'HRWork Development',
                ],
            );
        }
    }

    /**
     * @param array<string, Employees> $employees
     */
    private function seedStatusHistory(array $employees): void
    {
        foreach ($employees as $employee) {
            DB::table('employee_status_histories')->updateOrInsert(
                [
                    'employee_id' => $employee->id,
                    'new_status' => 'active',
                    'effective_date' => '2026-01-01',
                ],
                [
                    'old_status' => null,
                    'reason' => 'Employee development seed.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
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
            'superadmin' => [
                'number' => 'DEV-SADM-2026-001',
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
