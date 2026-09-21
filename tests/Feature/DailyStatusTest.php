<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Attendances\DailyStatus;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DailyStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-23 10:00:00');

        WorkTime::create([
            'day_of_week' => 'rabu',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_daily_status_page_renders_status_from_daily_status_service(): void
    {
        $employee = Employees::create([
            'employee_code' => 'EMP-001',
            'user_id' => null,
            'team_id' => null,
            'position_id' => null,
        ]);

        EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR-001',
            'employement_type' => 'pkwtt',
            'start_date' => '2026-09-01',
            'end_date' => null,
            'salary_daily' => 100000,
            'status' => 'active',
            'position_name' => 'Developer',
            'notes' => null,
        ]);

        Livewire::test(DailyStatus::class)
            ->set('date', '2026-09-23')
            ->assertSee('EMP-001')
            ->assertSee('Menunggu')
            ->assertSee('Perhitungan')
            ->assertSee('1 karyawan');
    }
}
