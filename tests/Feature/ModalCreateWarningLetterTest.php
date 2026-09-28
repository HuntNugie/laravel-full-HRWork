<?php

namespace Tests\Feature;

use App\Livewire\Components\Main\Dicipline\ModalCreateWarningLetter;
use App\Models\Employees;
use App\Models\User;
use App\Models\WarningLetterSequence;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ModalCreateWarningLetterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-28 10:00:00');

        Permission::create([
            'name' => 'create-warning-letter',
            'guard_name' => 'web',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_employee_picker_searches_by_name_or_employee_code(): void
    {
        $hr = User::factory()->create();
        $hr->givePermissionTo('create-warning-letter');

        $aliceUser = User::factory()->create([
            'name' => 'Alice Rahma',
            'email' => 'alice@example.com',
        ]);

        $bobUser = User::factory()->create([
            'name' => 'Bob Santoso',
            'email' => 'bob@example.com',
        ]);

        $alice = Employees::create([
            'employee_code' => 'EMP-ALICE',
            'user_id' => $aliceUser->id,
            'team_id' => null,
            'position_id' => null,
        ]);

        Employees::create([
            'employee_code' => 'EMP-BOB',
            'user_id' => $bobUser->id,
            'team_id' => null,
            'position_id' => null,
        ]);

        WarningLetterSequence::create([
            'year' => 2026,
            'month' => 9,
            'last_number' => 0,
        ]);

        Livewire::actingAs($hr)
            ->test(ModalCreateWarningLetter::class)
            ->set('employeeSearch', 'alice')
            ->assertSet('employeeId', '')
            ->assertSee('Alice Rahma')
            ->assertSee('EMP-ALICE')
            ->assertDontSee('EMP-BOB');

        $this->assertTrue($alice->exists);
    }

    public function test_employee_can_be_selected_and_cleared(): void
    {
        $hr = User::factory()->create();
        $hr->givePermissionTo('create-warning-letter');

        $employeeUser = User::factory()->create([
            'name' => 'Alice Rahma',
        ]);

        $employee = Employees::create([
            'employee_code' => 'EMP-ALICE',
            'user_id' => $employeeUser->id,
            'team_id' => null,
            'position_id' => null,
        ]);

        WarningLetterSequence::create([
            'year' => 2026,
            'month' => 9,
            'last_number' => 0,
        ]);

        Livewire::actingAs($hr)
            ->test(ModalCreateWarningLetter::class)
            ->call('selectEmployee', $employee->id)
            ->assertSet('employeeId', (string) $employee->id)
            ->assertSet('selectedEmployeeName', 'Alice Rahma · EMP-ALICE')
            ->assertSet('employeeSearch', 'Alice Rahma · EMP-ALICE')
            ->assertSet('employeeDropdownOpen', false)
            ->call('clearSelectedEmployee')
            ->assertSet('employeeId', '')
            ->assertSet('employeeSearch', '')
            ->assertSet('selectedEmployeeName', '')
            ->assertSet('employeeDropdownOpen', true);
    }
}
