<?php

namespace Tests\Feature;

use App\Ai\Agents\HRAssistant;
use App\Ai\Tools\SearchEmployees;
use App\Ai\Tools\GetEmployeeDetails;
use App\Ai\Tools\SearchContracts;
use App\Ai\Tools\SearchPositions;
use App\Ai\Tools\GetHRMetrics;
use App\Livewire\Page\Main\AI\Assistant;
use App\Models\Employees;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;
use Livewire\Livewire;
use Tests\TestCase;

class HrAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed([PermissionSeeder::class, RoleSeeder::class, RolePermissionSeeder::class]);
        config(['ai.hrwork_enabled' => false]);
    }

    private function hr(array $permissions = []): User
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('hr');
        $user->givePermissionTo($permissions);
        return $user;
    }

    public function test_assistant_exposes_only_five_supported_tools(): void
    {
        $this->assertSame([SearchEmployees::class, GetEmployeeDetails::class, SearchContracts::class, SearchPositions::class, GetHRMetrics::class], array_map('get_class', (new HRAssistant)->tools()));
    }

    public function test_ai_permission_does_not_grant_data_access(): void
    {
        $this->actingAs($this->hr(['view-ai-assistant']));
        foreach ([new SearchEmployees, new GetEmployeeDetails, new SearchContracts, new SearchPositions, new GetHRMetrics] as $tool) {
            $result = json_decode((string) $tool->handle(new Request), true);
            $this->assertFalse($result['success']);
        }
    }

    public function test_employee_search_and_metrics_follow_permissions(): void
    {
        Employees::create(['name' => 'Example Staff', 'email' => 'private@example.test', 'status_employee' => 'active']);
        $this->actingAs($this->hr(['view-ai-assistant', 'view-employee']));
        $search = json_decode((string) (new SearchEmployees)->handle(new Request(['query' => 'Example'])), true);
        $this->assertSame('Example Staff', $search['employees'][0]['name']);
        $this->assertArrayNotHasKey('email', $search['employees'][0]);
        $metrics = json_decode((string) (new GetHRMetrics)->handle(new Request), true);
        $this->assertSame(1, $metrics['metrics']['workforce_current']['total_employees']);
        $this->assertArrayNotHasKey('contracts_current', $metrics['metrics']);
    }

    public function test_assistant_denies_unauthorized_hr_and_admin(): void
    {
        $this->actingAs($this->hr())->get(route('ai.assistant'))->assertForbidden();
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('administrator');
        $this->actingAs($admin)->get(route('ai.assistant'))->assertForbidden();
    }

    public function test_disabled_provider_never_sends_prompt(): void
    {
        $this->actingAs($this->hr(['view-ai-assistant']));
        Livewire::test(Assistant::class)->call('send', 'Test')->assertSet('messages', [])
            ->assertSee('AI belum diaktifkan');
    }
}
