<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Benefit;
use App\Models\Attendances;
use App\Models\ContractLeaveEntitlements;
use App\Models\EmployeeBankAccount;
use App\Models\EmployeeContract;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeProfileAddress;
use App\Models\Employee_profile;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkTime;
use App\Service\PayrollCalculationService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class HRISDevelopmentSeeder extends Seeder
{
    /**
     * Seed a populated HRIS demo environment, including production when requested.
     *
     * Dataset:
     * - 50 karyawan dengan jabatan, tanpa divisi atau team
     * - HR, administrator, and super-admin accounts
     * - profiles, addresses, bank accounts, status history, contracts,
     *   benefits, leave entitlements and activity, attendance, warnings,
     *   exit cases, and three previous paid payroll periods
     */
    public function run(): void
    {
        // Validate credentials before creating any demo records.
        $demoPassword = $this->resolveDemoAccountPassword();

        $this->seedWilayah();
        $this->seedPositions();

        $users = $this->seedUsers($demoPassword);
        $employees = $this->seedEmployees($users);
        $this->seedProfilesAndBankAccounts($employees);
        $this->seedStatusHistory($employees);
        $this->seedContracts($employees);
        $this->seedBenefits($employees);
        $this->seedLeaveEntitlement($employees);

        $activity = app(HRISDemoActivityData::class);
        $activity->beforeAttendance($employees, $users);
        $this->seedAttendances($employees);
        $this->seedTodayAttendances($employees);
        $this->seedPayrollHistory($employees);
        $activity->afterPayroll($employees, $users);

        $count = Employees::query()
            ->where('employee_code', 'like', 'DEV-%')
            ->count();

        if ($count !== 50) {
            throw new \RuntimeException(
                "Development seed harus menghasilkan tepat 50 employee. Saat ini: {$count}.",
            );
        }

        $this->command?->info("HRIS demo data seeded: {$count} employees.");
        $this->command?->line($demoPassword === 'nugitea123'
            ? 'Local/testing demo accounts use password: nugitea123'
            : 'Demo employee/HR/admin password: configured via HRIS_DEMO_PASSWORD');
        $this->command?->line('GM: gm.hrwork@gmail.com');
        $this->command?->line('HR: nugiekurniawan03@gmail.com');
        $this->command?->line('Administrator: nugiekurniawan02@gmail.com');
        $this->command?->line('Super Admin (uses SUPERADMIN_PASSWORD): ' . env('SUPERADMIN_EMAIL', 'superadmin@gmail.com'));
    }

    private function resolveDemoAccountPassword(): string
    {
        $configuredPassword = (string) config('hrwork.demo_user_password', '');

        if (app()->environment(['local', 'testing']) && trim($configuredPassword) === '') {
            return 'nugitea123';
        }

        if (trim($configuredPassword) === '') {
            throw new \RuntimeException(
                'HRIS_DEMO_PASSWORD wajib diisi di production/staging sebelum menjalankan seeder data demo.',
            );
        }

        if (! app()->environment(['local', 'testing'])
            && ($configuredPassword === 'nugitea123' || strlen($configuredPassword) < 12)) {
            throw new \RuntimeException(
                'HRIS_DEMO_PASSWORD untuk production/staging harus minimal 12 karakter dan bukan password demo default.',
            );
        }

        return $configuredPassword;
    }

    private function seedPositions(): void
    {
        $positions = [
            ['name' => 'General Manager', 'description' => 'Penanggung jawab operasional perusahaan.', 'min_salary_daily' => 500000],
            ['name' => 'Manager', 'description' => 'Jabatan manajerial yang tercantum pada kontrak kerja.', 'min_salary_daily' => 350000],
            ['name' => 'Supervisor', 'description' => 'Jabatan supervisi yang tercantum pada kontrak kerja.', 'min_salary_daily' => 275000],
            ['name' => 'Software Engineer', 'description' => 'Pengembangan perangkat lunak umum.', 'min_salary_daily' => 225000],
            ['name' => 'Backend Developer', 'description' => 'Pengembangan layanan backend dan API.', 'min_salary_daily' => 240000],
            ['name' => 'Frontend Developer', 'description' => 'Pengembangan antarmuka aplikasi web.', 'min_salary_daily' => 235000],
            ['name' => 'Mobile Developer', 'description' => 'Pengembangan aplikasi mobile.', 'min_salary_daily' => 230000],
            ['name' => 'UI/UX Designer', 'description' => 'Perancangan pengalaman dan antarmuka pengguna.', 'min_salary_daily' => 220000],
            ['name' => 'QA Engineer', 'description' => 'Quality assurance dan pengujian aplikasi.', 'min_salary_daily' => 210000],
            ['name' => 'DevOps Engineer', 'description' => 'Infrastruktur, deployment, dan reliability.', 'min_salary_daily' => 250000],
            ['name' => 'IT Support Specialist', 'description' => 'Dukungan perangkat dan aplikasi internal.', 'min_salary_daily' => 190000],
            ['name' => 'Network Support Specialist', 'description' => 'Dukungan jaringan dan konektivitas.', 'min_salary_daily' => 205000],
            ['name' => 'Data Analyst', 'description' => 'Analisis data dan pelaporan.', 'min_salary_daily' => 215000],
            ['name' => 'Product Specialist', 'description' => 'Analisis kebutuhan dan pengembangan produk.', 'min_salary_daily' => 230000],
            ['name' => 'Project Coordinator', 'description' => 'Koordinasi aktivitas dan dokumentasi project.', 'min_salary_daily' => 220000],
            ['name' => 'Finance Officer', 'description' => 'Pengelolaan operasional keuangan.', 'min_salary_daily' => 210000],
            ['name' => 'Accounting Staff', 'description' => 'Pencatatan dan rekonsiliasi akuntansi.', 'min_salary_daily' => 190000],
            ['name' => 'Administrator', 'description' => 'Pengelola administrasi sistem dan operasional internal.', 'min_salary_daily' => 250000],
            ['name' => 'Administrative Officer', 'description' => 'Operasional administrasi internal.', 'min_salary_daily' => 185000],
            ['name' => 'HR Officer', 'description' => 'Pengelolaan operasional sumber daya manusia.', 'min_salary_daily' => 275000],
            ['name' => 'Recruitment Staff', 'description' => 'Rekrutmen dan administrasi kandidat.', 'min_salary_daily' => 195000],
            ['name' => 'People Operations Staff', 'description' => 'Operasional layanan dan administrasi karyawan.', 'min_salary_daily' => 200000],
            ['name' => 'Marketing Specialist', 'description' => 'Pemasaran dan komunikasi produk.', 'min_salary_daily' => 200000],
            ['name' => 'Content Specialist', 'description' => 'Konten dan dokumentasi komunikasi.', 'min_salary_daily' => 190000],
            ['name' => 'Security Analyst', 'description' => 'Keamanan aplikasi dan infrastruktur.', 'min_salary_daily' => 240000],
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

    /**
     * @return array<string, User>
     */
    private function seedUsers(string $demoPassword): array
    {
        $accounts = $this->accountDefinitions();
        $users = [];

        foreach ($accounts as $key => $account) {
            // UserSeeder already created the privileged account using the
            // password supplied by the developer. Never overwrite it with a
            // publicly documented demo password.
            $user = $key === 'superadmin'
                ? User::query()->where('email', $account['email'])->firstOrFail()
                : User::updateOrCreate(
                    ['email' => $account['email']],
                    [
                        'name' => $account['name'],
                        'password' => Hash::make($demoPassword),
                        'status' => 'active',
                    ],
                );

            foreach ($account['roles'] as $roleName) {
                Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
            }

            $user->syncRoles($account['roles']);
            $users[$key] = $user;
        }

        return $users;
    }

    /**
     * @return array<string, array{name:string,email:string,roles:array<int,string>,position:string,code:string}>
     */
    private function accountDefinitions(): array
    {
        $accounts = [
            'gm' => [
                'name' => 'GM HRWork',
                'email' => 'gm.hrwork@gmail.com',
                'roles' => ['employee'],
                'position' => 'General Manager',
                'code' => 'DEV-GM-001',
            ],
            'manager-development' => [
                'name' => 'Manager Development',
                'email' => 'manager.development.hrwork@gmail.com',
                'roles' => ['employee'],
                'position' => 'Manager',
                'code' => 'DEV-MGR-001',
            ],
            'supervisor-backend' => [
                'name' => 'Supervisor Backend Development',
                'email' => 'supervisor.backend.hrwork@gmail.com',
                'roles' => ['employee'],
                'position' => 'Supervisor',
                'code' => 'DEV-SPV-001',
            ],
            'worker-backend-1' => [
                'name' => 'Worker Backend Development',
                'email' => 'worker.backend.001.hrwork@gmail.com',
                'roles' => ['employee'],
                'position' => 'Backend Developer',
                'code' => 'DEV-WKR-001',
            ],
        ];

        $accounts['hr'] = [
            'name' => 'HR HRWork',
            'email' => 'nugiekurniawan03@gmail.com',
            'roles' => ['employee', 'hr'],
            'position' => 'HR Officer',
            'code' => 'DEV-HR-001',
        ];

        $accounts['admin'] = [
            'name' => 'Administrator HRWork',
            'email' => 'nugiekurniawan02@gmail.com',
            'roles' => ['employee', 'administrator'],
            'position' => 'Administrator',
            'code' => 'DEV-ADM-001',
        ];

        $accounts['superadmin'] = [
            'name' => 'Super Admin',
            'email' => env('SUPERADMIN_EMAIL', 'superadmin@gmail.com'),
            'roles' => ['employee', 'super-admin'],
            'position' => 'Administrator',
            'code' => 'DEV-SADM-001',
        ];

        $extraManagers = [
            ['key' => 'manager-product', 'name' => 'Rian Setiawan', 'email' => 'rian.setiawan.hrwork@gmail.com', 'code' => 'DEV-MGR-002'],
            ['key' => 'manager-infrastructure', 'name' => 'Fajar Nugroho', 'email' => 'fajar.nugroho.hrwork@gmail.com', 'code' => 'DEV-MGR-003'],
            ['key' => 'manager-finance', 'name' => 'Siti Rahmawati', 'email' => 'siti.rahmawati.hrwork@gmail.com', 'code' => 'DEV-MGR-004'],
            ['key' => 'manager-people', 'name' => 'Dimas Prakoso', 'email' => 'dimas.prakoso.hrwork@gmail.com', 'code' => 'DEV-MGR-005'],
        ];

        foreach ($extraManagers as $manager) {
            $accounts[$manager['key']] = [
                'name' => $manager['name'],
                'email' => $manager['email'],
                'roles' => ['employee'],
                'position' => 'Manager',
                'code' => $manager['code'],
            ];
        }

        $extraSupervisors = [
            ['key' => 'supervisor-frontend', 'name' => 'Nanda Putri', 'email' => 'nanda.putri.hrwork@gmail.com', 'code' => 'DEV-SPV-002'],
            ['key' => 'supervisor-uiux', 'name' => 'Aldi Saputra', 'email' => 'aldi.saputra.hrwork@gmail.com', 'code' => 'DEV-SPV-003'],
            ['key' => 'supervisor-product', 'name' => 'Raka Maulana', 'email' => 'raka.maulana.hrwork@gmail.com', 'code' => 'DEV-SPV-004'],
            ['key' => 'supervisor-devops', 'name' => 'Intan Permata', 'email' => 'intan.permata.hrwork@gmail.com', 'code' => 'DEV-SPV-005'],
            ['key' => 'supervisor-it-support', 'name' => 'Yusuf Kurnia', 'email' => 'yusuf.kurnia.hrwork@gmail.com', 'code' => 'DEV-SPV-006'],
            ['key' => 'supervisor-finance', 'name' => 'Bima Ramadhan', 'email' => 'bima.ramadhan.hrwork@gmail.com', 'code' => 'DEV-SPV-007'],
            ['key' => 'supervisor-admin', 'name' => 'Nadia Lestari', 'email' => 'nadia.lestari.hrwork@gmail.com', 'code' => 'DEV-SPV-008'],
            ['key' => 'supervisor-hr', 'name' => 'Reza Firmansyah', 'email' => 'reza.firmansyah.hrwork@gmail.com', 'code' => 'DEV-SPV-009'],
            ['key' => 'supervisor-people', 'name' => 'Tiara Anjani', 'email' => 'tiara.anjani.hrwork@gmail.com', 'code' => 'DEV-SPV-010'],
        ];

        foreach ($extraSupervisors as $supervisor) {
            $accounts[$supervisor['key']] = [
                'name' => $supervisor['name'],
                'email' => $supervisor['email'],
                'roles' => ['employee'],
                'position' => 'Supervisor',
                'code' => $supervisor['code'],
            ];
        }

        $workers = [
            ['key' => 'worker-backend-2', 'name' => 'Andi Pratama', 'email' => 'andi.pratama.hrwork@gmail.com', 'position' => 'Backend Developer'],
            ['key' => 'worker-backend-3', 'name' => 'Budi Santoso', 'email' => 'budi.santoso.hrwork@gmail.com', 'position' => 'Software Engineer'],
            ['key' => 'worker-backend-4', 'name' => 'Citra Lestari', 'email' => 'citra.lestari.hrwork@gmail.com', 'position' => 'Security Analyst'],
            ['key' => 'worker-frontend-1', 'name' => 'Deni Kurniawan', 'email' => 'deni.kurniawan.hrwork@gmail.com', 'position' => 'Frontend Developer'],
            ['key' => 'worker-frontend-2', 'name' => 'Eka Putri', 'email' => 'eka.putri.hrwork@gmail.com', 'position' => 'Frontend Developer'],
            ['key' => 'worker-frontend-3', 'name' => 'Farhan Akbar', 'email' => 'farhan.akbar.hrwork@gmail.com', 'position' => 'Mobile Developer'],

            ['key' => 'worker-uiux-1', 'name' => 'Gina Maharani', 'email' => 'gina.maharani.hrwork@gmail.com', 'position' => 'UI/UX Designer'],
            ['key' => 'worker-uiux-2', 'name' => 'Hadi Wijaya', 'email' => 'hadi.wijaya.hrwork@gmail.com', 'position' => 'UI/UX Designer'],
            ['key' => 'worker-uiux-3', 'name' => 'Indra Gunawan', 'email' => 'indra.gunawan.hrwork@gmail.com', 'position' => 'QA Engineer'],
            ['key' => 'worker-product-1', 'name' => 'Joko Firmansyah', 'email' => 'joko.firmansyah.hrwork@gmail.com', 'position' => 'Product Specialist'],
            ['key' => 'worker-product-2', 'name' => 'Karina Sari', 'email' => 'karina.sari.hrwork@gmail.com', 'position' => 'Project Coordinator'],
            ['key' => 'worker-product-3', 'name' => 'Lukman Hakim', 'email' => 'lukman.hakim.hrwork@gmail.com', 'position' => 'Data Analyst'],

            ['key' => 'worker-devops-1', 'name' => 'Maya Anggraini', 'email' => 'maya.anggraini.hrwork@gmail.com', 'position' => 'DevOps Engineer'],
            ['key' => 'worker-devops-2', 'name' => 'Niko Ramadhan', 'email' => 'niko.ramadhan.hrwork@gmail.com', 'position' => 'DevOps Engineer'],
            ['key' => 'worker-devops-3', 'name' => 'Oki Setiawan', 'email' => 'oki.setiawan.hrwork@gmail.com', 'position' => 'Security Analyst'],
            ['key' => 'worker-it-1', 'name' => 'Putri Amelia', 'email' => 'putri.amelia.hrwork@gmail.com', 'position' => 'IT Support Specialist'],
            ['key' => 'worker-it-2', 'name' => 'Qori Ananda', 'email' => 'qori.ananda.hrwork@gmail.com', 'position' => 'Network Support Specialist'],
            ['key' => 'worker-it-3', 'name' => 'Salsa Nurfadila', 'email' => 'salsa.nurfadila.hrwork@gmail.com', 'position' => 'IT Support Specialist'],

            ['key' => 'worker-finance-1', 'name' => 'Taufik Hidayat', 'email' => 'taufik.hidayat.hrwork@gmail.com', 'position' => 'Finance Officer'],
            ['key' => 'worker-finance-2', 'name' => 'Uli Permata', 'email' => 'uli.permata.hrwork@gmail.com', 'position' => 'Accounting Staff'],
            ['key' => 'worker-finance-3', 'name' => 'Vina Oktaviani', 'email' => 'vina.oktaviani.hrwork@gmail.com', 'position' => 'Data Analyst'],
            ['key' => 'worker-admin-1', 'name' => 'Wahyu Saputra', 'email' => 'wahyu.saputra.hrwork@gmail.com', 'position' => 'Administrative Officer'],
            ['key' => 'worker-admin-2', 'name' => 'Xena Maharani', 'email' => 'xena.maharani.hrwork@gmail.com', 'position' => 'Administrative Officer'],
            ['key' => 'worker-admin-3', 'name' => 'Yudi Pratama', 'email' => 'yudi.pratama.hrwork@gmail.com', 'position' => 'Content Specialist'],

            ['key' => 'worker-hr-1', 'name' => 'Zahra Fadillah', 'email' => 'zahra.fadillah.hrwork@gmail.com', 'position' => 'Recruitment Staff'],
            ['key' => 'worker-hr-2', 'name' => 'Bagas Aditya', 'email' => 'bagas.aditya.hrwork@gmail.com', 'position' => 'People Operations Staff'],
            ['key' => 'worker-hr-3', 'name' => 'Chandra Wijaya', 'email' => 'chandra.wijaya.hrwork@gmail.com', 'position' => 'Marketing Specialist'],
            ['key' => 'worker-people-1', 'name' => 'Daffa Maulana', 'email' => 'daffa.maulana.hrwork@gmail.com', 'position' => 'People Operations Staff'],
            ['key' => 'worker-people-2', 'name' => 'Elsya Nuraini', 'email' => 'elsya.nuraini.hrwork@gmail.com', 'position' => 'HR Officer'],
            ['key' => 'worker-people-3', 'name' => 'Galih Prakoso', 'email' => 'galih.prakoso.hrwork@gmail.com', 'position' => 'Project Coordinator'],
        ];

        foreach ($workers as $index => $worker) {
            $accounts[$worker['key']] = [
                'name' => $worker['name'],
                'email' => $worker['email'],
                'roles' => ['employee'],
                'position' => $worker['position'],
                'code' => 'DEV-WKR-' . str_pad((string) ($index + 2), 3, '0', STR_PAD_LEFT),
            ];
        }

        return $accounts;
    }

    /**
     * @param array<string, User> $users
     * @return array<string, Employees>
     */
    private function seedEmployees(array $users): array
    {
        $accounts = $this->accountDefinitions();

        $positions = Position::query()
            ->whereIn('name', array_values(array_unique(array_column($accounts, 'position'))))
            ->get()
            ->keyBy('name');

        $employees = [];

        foreach ($accounts as $key => $account) {
            $employees[$key] = Employees::updateOrCreate(
                ['employee_code' => $account['code']],
                [
                    'user_id' => $users[$key]->id,
                    'status_employee' => 'active',
                    'position_id' => $positions[$account['position']]->id,
                ],
            );
        }

        return $employees;
    }

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

        $employees = array_values($employees);

        $villages = DB::table('villages')
            ->where('code', 'like', '32.%')
            ->whereNotNull('postal_code')
            ->orderBy('id')
            ->limit(count($employees))
            ->get(['code', 'name', 'postal_code']);

        if ($villages->count() < count($employees)) {
            throw new \RuntimeException(
                'Data desa/kelurahan Jawa Barat belum cukup untuk development seed 50 employee.',
            );
        }

        $firstNames = [
            'Muhammad', 'Ahmad', 'Andi', 'Budi', 'Citra', 'Deni', 'Eka', 'Farhan',
            'Gina', 'Hadi', 'Indra', 'Joko', 'Karina', 'Lukman', 'Maya', 'Niko',
            'Oki', 'Putri', 'Qori', 'Raka', 'Salsa', 'Taufik', 'Uli', 'Vina',
            'Wahyu', 'Xena', 'Yudi', 'Zahra', 'Bagas', 'Chandra', 'Daffa', 'Elsya',
            'Galih', 'Intan', 'Nanda', 'Reza', 'Tiara', 'Yusuf', 'Bima', 'Nadia',
            'Dimas', 'Siti', 'Fajar', 'Rian', 'Nugraha', 'Pratama', 'Rahma',
            'Permata', 'Setiawan', 'Wijaya',
        ];

        $addresses = [
            'Bandung', 'Cimahi', 'Garut', 'Sumedang', 'Subang', 'Purwakarta',
            'Karawang', 'Tasikmalaya', 'Cianjur', 'Sukabumi', 'Bogor', 'Bekasi',
        ];

        foreach (array_values($employees) as $index => $employee) {
            $gender = $index % 3 === 0 ? 'female' : 'male';
            $nameSeed = $firstNames[$index] ?? 'Employee';
            $birthAddress = $addresses[$index % count($addresses)];

            $profile = Employee_profile::updateOrCreate(
                ['employee_id' => $employee->id],
                [
                    'gender' => $gender,
                    'phone_number' => '0812' . str_pad((string) (10000000 + $index), 8, '0', STR_PAD_LEFT),
                    'nik' => '3273' . str_pad((string) (900000000000 + $index), 12, '0', STR_PAD_LEFT),
                    'birth_date' => now()->subYears(23 + ($index % 15))->subDays($index)->toDateString(),
                    'birth_address' => $birthAddress . ', Jawa Barat',
                ],
            );

            EmployeeProfileAddress::updateOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'full_address' => 'Jl. HRWork Development No. ' . ($index + 1) . ', ' . $birthAddress . ', Jawa Barat ' . $villages[$index]->postal_code,
                    'village_code' => $villages[$index]->code,
                ],
            );

            EmployeeBankAccount::updateOrCreate(
                ['employee_profile_id' => $profile->id],
                [
                    'bank_id' => $bank->id,
                    'account_number' => '12345000' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'account_holder' => $employee->user?->name ?? ($nameSeed . ' HRWork'),
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
        $positions = Position::query()
            ->get()
            ->keyBy('name');

        foreach ($employees as $index => $employee) {
            $positionName = $employee->position?->name;
            $position = $positions[$positionName] ?? null;

            if (! $position) {
                throw new \RuntimeException(
                    "Position untuk employee {$employee->employee_code} tidak ditemukan.",
                );
            }

            EmployeeContract::updateOrCreate(
                ['contract_number' => 'DEV-' . str_replace('DEV-', '', $employee->employee_code) . '-2026-001'],
                [
                    'employee_id' => $employee->id,
                    'position_name' => $position->name,
                    'employement_type' => 'pkwtt',
                    'start_date' => '2026-01-01',
                    'end_date' => null,
                    'salary_daily' => $position->min_salary_daily,
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
     * Seed historical attendance so Daily Status and attendance history have
     * realistic development data immediately after migrate:fresh --seed.
     *
     * The seed covers from the start of the third previous month through
     * yesterday and only creates records on configured working days. Every
     * employee receives a completed check-in/check-out record, with
     * deterministic late variations.
     *
     * @param array<string, Employees> $employees
     */
    private function seedAttendances(array $employees): void
    {
        $workTimes = WorkTime::query()
            ->get()
            ->keyBy(fn (WorkTime $workTime) => strtolower(trim($workTime->day_of_week)));

        $dayNames = [
            1 => 'senin',
            2 => 'selasa',
            3 => 'rabu',
            4 => 'kamis',
            5 => 'jumat',
            6 => 'sabtu',
            7 => 'minggu',
        ];

        $endDate = today()->subDay()->startOfDay();
        $startDate = today()->subMonths(3)->startOfMonth();
        $seeded = 0;

        $holidays = Holidays::query()
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $timeOffDates = [];
        foreach (LeaveRequest::query()->where('status', 'approved')->get() as $leave) {
            foreach (CarbonPeriod::create($leave->start_date, $leave->end_date) as $leaveDate) {
                $timeOffDates[$leave->employee_id][Carbon::parse($leaveDate)->toDateString()] = true;
            }
        }

        foreach (EmployeeAbsenceRequest::query()->where('status', 'approved')->get() as $absence) {
            $timeOffDates[$absence->employee_id][Carbon::parse($absence->date)->toDateString()] = true;
        }

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $date = Carbon::instance($date)->startOfDay();
            $workTime = $workTimes->get($dayNames[$date->dayOfWeekIso]);

            if (! $workTime || ! $workTime->is_working_day || $holidays->has($date->toDateString())) {
                continue;
            }

            $workStart = $date->copy()->setTimeFromTimeString($workTime->start_time);
            $workEnd = $date->copy()->setTimeFromTimeString($workTime->end_time);
            $workDayNumber = $date->dayOfYear;

            foreach (array_values($employees) as $employeeIndex => $employee) {
                if (isset($timeOffDates[$employee->id][$date->toDateString()])) {
                    continue;
                }

                // Roughly 1 in 9 records is late, with deterministic minutes.
                $isLate = (($employeeIndex + $workDayNumber) % 9) === 0;
                $checkInOffset = $isLate
                    ? 7 + (($employeeIndex * 3 + $workDayNumber) % 14)
                    : (($employeeIndex + $workDayNumber) % 5);

                $checkIn = $workStart->copy()->addMinutes($checkInOffset);

                // Keep checkout slightly varied while remaining inside a realistic range.
                $checkOutOffset = $date->isSaturday()
                    ? 3 + (($employeeIndex + $workDayNumber) % 16)
                    : 5 + (($employeeIndex * 2 + $workDayNumber) % 26);

                $checkOut = $workEnd->copy()->addMinutes($checkOutOffset);
                $lateMinutes = $isLate
                    ? $workStart->diffInMinutes($checkIn)
                    : 0;

                Attendances::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date' => $date->toDateString(),
                    ],
                    [
                        'check_in_at' => $checkIn,
                        'check_in_latitude' => -6.914744 + (($employeeIndex % 7) * 0.0012),
                        'check_in_longitude' => 107.609810 + (($employeeIndex % 7) * 0.0012),
                        'check_out_at' => $checkOut,
                        'check_out_latitude' => -6.914744 + (($employeeIndex % 7) * 0.0012),
                        'check_out_longitude' => 107.609810 + (($employeeIndex % 7) * 0.0012),
                        'status' => $isLate ? 'late' : 'present',
                        'late_minutes' => $lateMinutes,
                        'work_duration' => $checkIn->diffInMinutes($checkOut),
                        'notes' => 'Development seed attendance.',
                    ],
                );

                $seeded++;
            }
        }

        $this->command?->info("Historical attendance seeded: {$seeded} records ({$startDate->toDateString()} s/d {$endDate->toDateString()}).");
    }

    /**
     * Populate today's dashboard only after the real shift has started.
     * Today's records are excluded from the completed historical payrolls.
     *
     * @param array<string, Employees> $employees
     */
    private function seedTodayAttendances(array $employees): void
    {
        $dayName = [
            1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis',
            5 => 'jumat', 6 => 'sabtu', 7 => 'minggu',
        ][today()->dayOfWeekIso];

        $workTime = WorkTime::query()->where('day_of_week', $dayName)->first();
        if (! $workTime?->is_working_day || Holidays::query()->whereDate('date', today())->exists()) {
            return;
        }

        $start = today()->setTimeFromTimeString($workTime->start_time);
        $end = today()->setTimeFromTimeString($workTime->end_time);
        if (now()->lt($start)) {
            return;
        }

        foreach (array_slice(array_values($employees), 0, 36) as $index => $employee) {
            if ($index % 11 === 0) {
                continue; // Some employees have not checked in yet.
            }

            $late = $index % 8 === 0;
            $checkIn = $start->copy()->addMinutes($late ? 13 : ($index % 5));
            if ($checkIn->isFuture()) {
                continue;
            }

            $checkOut = now()->gte($end->copy()->addMinutes(15))
                ? $end->copy()->addMinutes($index % 15)
                : null;

            Attendances::query()->updateOrCreate(
                ['employee_id' => $employee->id, 'date' => today()->toDateString()],
                [
                    'check_in_at' => $checkIn,
                    'check_in_latitude' => -6.914744,
                    'check_in_longitude' => 107.609810,
                    'check_out_at' => $checkOut,
                    'check_out_latitude' => $checkOut ? -6.914744 : null,
                    'check_out_longitude' => $checkOut ? 107.609810 : null,
                    'status' => $late ? 'late' : 'present',
                    'late_minutes' => $late ? 13 : 0,
                    'work_duration' => $checkOut ? (int) $checkIn->diffInMinutes($checkOut) : null,
                    'notes' => 'Simulasi presensi hari ini.',
                ],
            );
        }
    }

    /**
     * Seed three previous completed payroll periods with generated payroll
     * snapshots and system payroll items. Historical periods are marked paid
     * to represent the manual payment workflow after HR has completed payment.
     *
     * @param array<string, Employees> $employees
     */
    private function seedPayrollHistory(array $employees): void
    {
        $creator = User::query()
            ->where('email', 'nugiekurniawan03@gmail.com')
            ->firstOrFail();

        $payrollCalculationService = app(PayrollCalculationService::class);
        $currentMonth = today()->startOfMonth();

        for ($monthOffset = 3; $monthOffset >= 1; $monthOffset--) {
            $startDate = $currentMonth->copy()->subMonths($monthOffset)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $paymentDate = $endDate->copy()->addDay();
            $processedAt = $paymentDate->copy()->addDays(1)->setTime(10, 0);
            $paidAt = $paymentDate->copy()->addDays(4)->setTime(10, 0);

            $generatedEmployeeCount = 0;

            DB::transaction(function () use (
                $creator,
                $employees,
                $payrollCalculationService,
                $startDate,
                $endDate,
                $paymentDate,
                $processedAt,
                $paidAt,
                &$generatedEmployeeCount,
            ): void {
                $period = PayrollPeriod::updateOrCreate(
                    [
                        'start_date' => $startDate->toDateString(),
                        'end_date' => $endDate->toDateString(),
                    ],
                    [
                        'name' => 'Payroll ' . $startDate->translatedFormat('F Y'),
                        'payment_date' => $paymentDate->toDateString(),
                        'status' => 'paid',
                        'created_by' => $creator->id,
                        'processed_by' => $creator->id,
                        'processed_at' => $processedAt,
                        'paid_at' => $paidAt,
                    ],
                );

                // Rebuild historical payroll rows so the seed remains idempotent.
                $period->payrolls()->delete();

                foreach (array_values($employees) as $employee) {
                    $contract = $employee->employeeContract
                        ->filter(function ($contract) use ($startDate, $endDate) {
                            return $contract->status === 'active'
                                && $contract->start_date->lte($endDate)
                                && (!$contract->end_date || $contract->end_date->gte($startDate));
                        })
                        ->sortByDesc('start_date')
                        ->first();

                    if (! $contract) {
                        continue;
                    }

                    $calculation = $payrollCalculationService->calculate(
                        employee: $employee,
                        contract: $contract,
                        period: $period,
                    );

                    $grossAmount = $calculation['salary_amount'] + $calculation['benefit_total'];
                    $deductionAmount = $calculation['late_deduction_total'];
                    $netAmount = $grossAmount - $deductionAmount;

                    $payroll = Payroll::create([
                        'payroll_period_id' => $period->id,
                        'employee_id' => $employee->id,
                        'employee_contract_id' => $contract->id,
                        'position_name' => $contract->position_name,
                        'salary_daily' => $calculation['salary_daily'],
                        'working_days' => $calculation['working_days'],
                        'present_days' => $calculation['present_days'],
                        'late_days' => $calculation['late_days'],
                        'absent_days' => $calculation['absent_days'],
                        'paid_leave_days' => $calculation['paid_leave_days'],
                        'unpaid_leave_days' => 0,
                        'paid_days' => $calculation['paid_days'],
                        'gross_amount' => $grossAmount,
                        'deduction_amount' => $deductionAmount,
                        'net_amount' => $netAmount,
                        'status' => 'paid',
                        'notes' => 'Historical development payroll seed.',
                        'processed_at' => $processedAt,
                        'paid_at' => $paidAt,
                    ]);

                    $generatedEmployeeCount++;

                    $sortOrder = 1;

                    foreach ($calculation['salary_items'] as $salaryItem) {
                        PayrollItem::create([
                            'payroll_id' => $payroll->id,
                            'name' => $salaryItem['name'],
                            'type' => 'earning',
                            'category' => 'salary',
                            'amount' => $salaryItem['amount'],
                            'quantity' => $salaryItem['quantity'],
                            'rate' => $salaryItem['rate'],
                            'source' => 'system',
                            'description' => 'Historical payroll seed: ' . $salaryItem['quantity'] . ' hari × Rp' . number_format($salaryItem['rate'], 0, ',', '.'),
                            'sort_order' => $sortOrder++,
                        ]);
                    }

                    foreach ($calculation['benefit_items'] as $benefitItem) {
                        PayrollItem::create([
                            'payroll_id' => $payroll->id,
                            'name' => $benefitItem['benefit']->name,
                            'type' => 'earning',
                            'category' => 'benefit',
                            'amount' => $benefitItem['amount'],
                            'quantity' => $benefitItem['quantity'],
                            'rate' => $benefitItem['rate'],
                            'source' => 'system',
                            'description' => 'Historical payroll seed: tunjangan ' . $benefitItem['quantity'] . ' hari × Rp' . number_format($benefitItem['rate'], 0, ',', '.'),
                            'sort_order' => $sortOrder++,
                        ]);
                    }

                    foreach ($calculation['late_deduction_items'] as $lateDeduction) {
                        $rate = $lateDeduction['deduction_units'] > 0
                            ? $lateDeduction['amount'] / $lateDeduction['deduction_units']
                            : 0;

                        PayrollItem::create([
                            'payroll_id' => $payroll->id,
                            'name' => 'Potongan Keterlambatan',
                            'type' => 'deduction',
                            'category' => 'late',
                            'amount' => $lateDeduction['amount'],
                            'quantity' => $lateDeduction['deduction_units'],
                            'rate' => $rate,
                            'source' => 'system',
                            'description' => 'Historical payroll seed: ' . $lateDeduction['late_count'] . ' keterlambatan pada ' . $lateDeduction['month'],
                            'sort_order' => $sortOrder++,
                        ]);
                    }
                }
            });

            $this->command?->line(
                'Payroll history: ' . $startDate->translatedFormat('F Y') . ' → paid (' . $generatedEmployeeCount . ' employees).'
            );
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
