<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Leave\DetailLeaveType;
use App\Models\ContractLeaveEntitlements;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DetailLeaveTypeSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_by_employee_name_code_and_contract_number(): void
    {
        $leaveType = LeaveType::create(['name' => 'Cuti Tahunan', 'default_days' => 12]);

        $this->createEntitlement($leaveType, 'Alice Rahma', 'EMP-ALICE', 'CTR-ALICE');
        $this->createEntitlement($leaveType, 'Budi Santoso', 'EMP-BUDI', 'CTR-BUDI');

        Livewire::test(DetailLeaveType::class, ['leavetype' => $leaveType])
            ->assertSee('Alice Rahma')
            ->assertSee('Budi Santoso')
            ->set('search', 'alice')
            ->assertSee('Alice Rahma')
            ->assertDontSee('Budi Santoso')
            ->set('search', 'EMP-BUDI')
            ->assertSee('Budi Santoso')
            ->assertDontSee('Alice Rahma')
            ->set('search', '  ctr-alice  ')
            ->assertSee('CTR-ALICE')
            ->assertDontSee('Budi Santoso')
            ->set('search', 'tidak-ada')
            ->assertSee('Tidak ada hasil pencarian')
            ->assertDontSee('Alice Rahma')
            ->assertDontSee('Budi Santoso')
            ->set('search', '')
            ->assertSee('Alice Rahma')
            ->assertSee('Budi Santoso');
    }

    private function createEntitlement(
        LeaveType $leaveType,
        string $name,
        string $employeeCode,
        string $contractNumber,
    ): void {
        $user = User::factory()->create(['name' => $name, 'status' => 'active']);

        $employee = Employees::create([
            'employee_code' => $employeeCode,
            'user_id' => $user->id,
            'status_employee' => 'active',
        ]);

        $contract = EmployeeContract::create([
            'employee_id' => $employee->id,
            'contract_number' => $contractNumber,
            'position_name' => 'Staff',
            'employement_type' => 'pkwtt',
            'start_date' => '2026-01-01',
            'salary_daily' => 100000,
            'status' => 'active',
        ]);

        ContractLeaveEntitlements::create([
            'employee_contract_id' => $contract->id,
            'leave_type_id' => $leaveType->id,
            'days' => 12,
        ]);
    }
}
