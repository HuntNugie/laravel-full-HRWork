<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Team;
use App\Models\User;
use App\Service\OrganizationAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrganizationAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_replacing_supervisor_with_member_of_same_team_swaps_supervisor_role(): void
    {
        [$team, $oldSupervisor, $newSupervisor] = $this->makeTeamWithSupervisor();
        $newSupervisor->update(['team_id' => $team->id]);

        app(OrganizationAssignmentService::class)->assignSupervisor($team, $newSupervisor);

        $this->assertSame($newSupervisor->id, $team->refresh()->supervisor_id);
        $this->assertTrue($newSupervisor->user->fresh()->hasRole('supervisor'));
        $this->assertFalse($oldSupervisor->user->fresh()->hasRole('supervisor'));
        $this->assertTrue($oldSupervisor->user->fresh()->hasRole('employee'));
        $this->assertSame($team->id, $newSupervisor->fresh()->team_id);
        $this->assertSame($team->id, $oldSupervisor->fresh()->team_id);
    }

    public function test_assigning_supervisor_from_employee_without_team_moves_employee_into_team_and_adds_role(): void
    {
        [$team, $oldSupervisor, $newSupervisor] = $this->makeTeamWithSupervisor();

        app(OrganizationAssignmentService::class)->assignSupervisor($team, $newSupervisor);

        $this->assertSame($newSupervisor->id, $team->refresh()->supervisor_id);
        $this->assertSame($team->id, $newSupervisor->fresh()->team_id);
        $this->assertTrue($newSupervisor->user->fresh()->hasRole('supervisor'));
        $this->assertTrue($newSupervisor->user->fresh()->hasRole('employee'));
        $this->assertFalse($oldSupervisor->user->fresh()->hasRole('supervisor'));
    }

    public function test_replacing_division_manager_with_employee_without_team_swaps_manager_role(): void
    {
        [$division, $oldManager, $newManager] = $this->makeDivisionWithManager();

        app(OrganizationAssignmentService::class)->assignDivisionManager($division, $newManager);

        $this->assertSame($newManager->id, $division->refresh()->manager_id);
        $this->assertTrue($newManager->user->fresh()->hasRole('manager'));
        $this->assertFalse($oldManager->user->fresh()->hasRole('manager'));
        $this->assertTrue($oldManager->user->fresh()->hasRole('employee'));
        $this->assertTrue($newManager->user->fresh()->hasRole('employee'));
    }

    public function test_clearing_division_manager_removes_manager_role_from_old_manager(): void
    {
        [$division, $oldManager] = $this->makeDivisionWithManager();

        app(OrganizationAssignmentService::class)->assignDivisionManager($division, null);

        $this->assertNull($division->refresh()->manager_id);
        $this->assertFalse($oldManager->user->fresh()->hasRole('manager'));
        $this->assertTrue($oldManager->user->fresh()->hasRole('employee'));
    }

    private function makeTeamWithSupervisor(): array
    {
        $oldSupervisorUser = $this->makeUser('Old Supervisor');
        $oldSupervisor = $this->makeEmployee('OLD-SUP', $oldSupervisorUser);

        $newSupervisorUser = $this->makeUser('New Supervisor');
        $newSupervisor = $this->makeEmployee('NEW-SUP', $newSupervisorUser);

        $team = Team::create([
            'name' => 'Backend',
            'description' => 'Backend',
            'is_active' => 'active',
            'divisi_id' => null,
            'supervisor_id' => $oldSupervisor->id,
        ]);

        $oldSupervisor->update(['team_id' => $team->id]);

        return [$team, $oldSupervisor, $newSupervisor];
    }

    private function makeDivisionWithManager(): array
    {
        $oldManagerUser = $this->makeUser('Old Manager');
        $oldManager = $this->makeEmployee('OLD-MAN', $oldManagerUser);

        $newManagerUser = $this->makeUser('New Manager');
        $newManager = $this->makeEmployee('NEW-MAN', $newManagerUser);

        $division = Divisi::create([
            'name' => 'Technology',
            'description' => 'Technology',
            'is_active' => 'active',
            'manager_id' => $oldManager->id,
        ]);

        return [$division, $oldManager, $newManager];
    }

    private function makeUser(string $name): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'status' => 'active',
        ]);

        $role = Role::firstOrCreate([
            'name' => 'employee',
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        return $user;
    }

    private function makeEmployee(string $code, User $user): Employees
    {
        return Employees::create([
            'employee_code' => $code,
            'user_id' => $user->id,
            'team_id' => null,
            'position_id' => null,
            'status_employee' => 'active',
        ]);
    }
}
