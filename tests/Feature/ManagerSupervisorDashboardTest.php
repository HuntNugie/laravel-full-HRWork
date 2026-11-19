<?php

namespace Tests\Feature;

use App\Models\Attendances;
use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManagerSupervisorDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_dashboard_only_shows_employees_from_their_division(): void
    {
        $manager = $this->makeEmployee('MANAGER', 'Manager One', 'manager');
        $division = Divisi::create(['name' => 'Development', 'is_active' => 'active', 'manager_id' => $manager->id]);
        $team = Team::create(['name' => 'Backend', 'divisi_id' => $division->id, 'is_active' => 'active']);

        $inside = $this->makeEmployee('INSIDE', 'Inside Employee');
        $inside->update(['team_id' => $team->id]);
        $outside = $this->makeEmployee('OUTSIDE', 'Outside Employee');

        Attendances::create([
            'employee_id' => $inside->id,
            'date' => today(),
            'check_in_at' => now()->setTime(9, 0),
            'status' => 'present',
            'late_minutes' => 0,
        ]);

        $this->actingAs($manager->user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Presensi Divisi Hari Ini')
            ->assertSee('Inside Employee')
            ->assertDontSee($outside->user->name);
    }

    public function test_supervisor_dashboard_only_shows_members_of_their_team(): void
    {
        $supervisor = $this->makeEmployee('SUPERVISOR', 'Supervisor One', 'supervisor');
        $division = Divisi::create(['name' => 'Engineering', 'is_active' => 'active']);
        $team = Team::create([
            'name' => 'Backend', 'divisi_id' => $division->id,
            'is_active' => 'active', 'supervisor_id' => $supervisor->id,
        ]);
        $supervisor->update(['team_id' => $team->id]);

        $member = $this->makeEmployee('MEMBER', 'Team Member');
        $member->update(['team_id' => $team->id]);
        $outside = $this->makeEmployee('OUTSIDE', 'Outside Worker');

        $this->actingAs($supervisor->user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Presensi Team Hari Ini')
            ->assertSee($member->user->name)
            ->assertDontSee($outside->user->name);
    }

    private function makeEmployee(string $code, string $name, ?string $roleName = null): Employees
    {
        $user = User::factory()->create(['name' => $name, 'status' => 'active']);
        $user->assignRole(Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']));

        if ($roleName) {
            $user->assignRole(Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']));
        }

        return Employees::create([
            'employee_code' => 'EMP-' . $code,
            'user_id' => $user->id,
            'status_employee' => 'active',
        ]);
    }
}
