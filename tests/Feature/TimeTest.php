<?php

namespace Tests\Feature;

use App\Livewire\Page\Main\Time\Time;
use App\Models\User;
use App\Models\WorkTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class TimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_working_day_setting_is_persisted(): void
    {
        $user = User::factory()->create();

        $permission = Permission::create([
            'name' => 'update-work-time',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo($permission);

        $workTime = WorkTime::create([
            'day_of_week' => 'senin',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => true,
        ]);

        Livewire::actingAs($user)
            ->test(Time::class)
            ->set("updateWorkTime.{$workTime->id}.is_working_day", false)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('work_times', [
            'id' => $workTime->id,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => 0,
        ]);
    }

    public function test_working_day_setting_can_be_enabled_again(): void
    {
        $user = User::factory()->create();

        $permission = Permission::create([
            'name' => 'update-work-time',
            'guard_name' => 'web',
        ]);

        $user->givePermissionTo($permission);

        $workTime = WorkTime::create([
            'day_of_week' => 'senin',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_working_day' => false,
        ]);

        Livewire::actingAs($user)
            ->test(Time::class)
            ->set("updateWorkTime.{$workTime->id}.is_working_day", true)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('work_times', [
            'id' => $workTime->id,
            'is_working_day' => 1,
        ]);
    }
}
