<?php

namespace Tests\Feature;

use App\Models\Employees;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HRISDevelopmentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_database_seeder_creates_connected_demo_hris_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(50, Employees::query()->where('employee_code', 'like', 'DEV-%')->count());
        $this->assertSame(50, Employees::query()->whereDate('JoinDate', '2026-01-01')->count());
        $this->assertSame(50, DB::table('employee_profiles')->count());
        $this->assertSame(50, DB::table('employee_bank_accounts')->count());
        $this->assertSame(50, DB::table('employee_contracts')->count());
        $this->assertSame(50, DB::table('position_jobdesks')->count());
        $this->assertGreaterThan(0, DB::table('contract_leave_entitlements')->count());

        foreach (['provinces', 'regencies', 'districts', 'villages'] as $table) {
            $this->assertGreaterThan(0, DB::table($table)->count(), "Wilayah {$table} harus terisi.");
        }

        foreach (['leave_requests', 'employee_absence_requests', 'attendances',
            'holidays', 'employee_warning_letters', 'payrolls', 'payroll_items',
            'employee_resignation_clearances', 'employee_termination_clearances',
            'employee_resignation_handover_items', 'employee_termination_handover_items',
        ] as $table) {
            $this->assertGreaterThan(0, DB::table($table)->count(), "Demo {$table} harus terisi.");
        }

        $this->assertSame(24, DB::table('leave_requests')->count());
        $this->assertSame(12, DB::table('employee_absence_requests')->count());
        $this->assertSame(12, DB::table('employee_warning_letters')->count());
        $this->assertSame(3, DB::table('payroll_periods')->where('status', 'paid')->count());
        $this->assertSame(150, DB::table('payrolls')->where('status', 'paid')->count());
        $this->assertDatabaseHas('leave_requests', ['status' => 'approved']);
        $this->assertDatabaseHas('leave_requests', ['status' => 'pending']);
        $this->assertDatabaseHas('leave_requests', ['status' => 'rejected']);
        $this->assertDatabaseHas('employee_absence_requests', ['status' => 'approved']);
        $this->assertDatabaseHas('employee_absence_requests', ['status' => 'pending']);
        $this->assertDatabaseHas('employee_resignations', ['status' => 'completed']);
        $this->assertDatabaseHas('employee_resignations', ['status' => 'approved']);
        $this->assertDatabaseHas('employee_terminations', ['status' => 'completed']);
        $this->assertDatabaseHas('employee_terminations', ['status' => 'in_progress']);

        $this->assertSame(0, DB::table('attendances')
            ->join('leave_requests', 'leave_requests.employee_id', '=', 'attendances.employee_id')
            ->where('leave_requests.status', 'approved')
            ->whereColumn('attendances.date', '>=', 'leave_requests.start_date')
            ->whereColumn('attendances.date', '<=', 'leave_requests.end_date')
            ->count());
        $this->assertSame(0, DB::table('attendances')
            ->join('employee_absence_requests', 'employee_absence_requests.employee_id', '=', 'attendances.employee_id')
            ->where('employee_absence_requests.status', 'approved')
            ->whereColumn('attendances.date', '=', 'employee_absence_requests.date')
            ->count());

        $superadmin = User::query()->where('email', env('SUPERADMIN_EMAIL', 'superadmin@gmail.com'))->firstOrFail();
        $this->assertTrue(Hash::check((string) env('SUPERADMIN_PASSWORD'), $superadmin->password));
        $this->assertFalse(Hash::check('nugitea123', $superadmin->password));
    }
}
