<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Attendances\HistoryAttendanceManage;
use App\Models\Attendances;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\User;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class HistoryAttendanceManageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-23 10:00:00');

        foreach ([
            'senin',
            'selasa',
            'rabu',
            'kamis',
            'jumat',
        ] as $day) {
            WorkTime::create([
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_working_day' => true,
            ]);
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_attendance_history_does_not_query_attendance_per_detail_modal(): void
    {
        $firstEmployee = $this->createEmployee('EMP-001');
        $secondEmployee = $this->createEmployee('EMP-002');

        $this->createAttendance($firstEmployee, '2026-09-21');
        $this->createAttendance($secondEmployee, '2026-09-22');

        DB::flushQueryLog();
        DB::enableQueryLog();

        Livewire::test(HistoryAttendanceManage::class)
            ->set('startDate', '2026-09-21')
            ->set('endDate', '2026-09-23');

        $attendanceQueries = collect(DB::getQueryLog())
            ->filter(function (array $query) {
                return str_contains(
                    strtolower($query['query']),
                    "from \`attendances\`"
                );
            });

        DB::disableQueryLog();

        $this->assertCount(1, $attendanceQueries);
    }

    private function createEmployee(string $employeeCode): Employees
    {
        $user = User::factory()->create();

        $employee = Employees::create([
            'employee_code' => $employeeCode,
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
        ]);

        EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => 'CTR-' . $employeeCode,
            'employement_type' => 'pkwtt',
            'start_date' => '2026-09-01',
            'end_date' => null,
            'salary_daily' => 100000,
            'status' => 'active',
            'position_name' => 'Developer',
            'notes' => null,
        ]);

        return $employee;
    }

    private function createAttendance(
        Employees $employee,
        string $date
    ): Attendances {
        return Attendances::create([
            'employee_id' => $employee->id,
            'date' => $date,
            'check_in_at' => $date . ' 09:00:00',
            'check_out_at' => $date . ' 17:00:00',
            'status' => 'present',
            'late_minutes' => 0,
            'work_duration' => 480,
        ]);
    }
}
