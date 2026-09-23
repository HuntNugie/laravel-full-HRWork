<?php

namespace App\Service;

use App\Models\Divisi;
use App\Models\EmployeeResignationClearance;
use App\Models\EmployeeResignationHandoverItem;
use App\Models\EmployeeTerminationClearance;
use App\Models\EmployeeTerminationHandoverItem;
use App\Models\Employees;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeExitActionService
{
    public function applyClearance(
        EmployeeResignationClearance|EmployeeTerminationClearance $clearance,
        User $actor,
    ): void {
        $state = $clearance->action_state;

        if (($state['applied'] ?? false) === true) {
            return;
        }

        $category = $clearance->category;

        if ($category === 'access') {
            $employee = $this->employeeForClearance($clearance);
            $user = $employee->user;

            if (!$user) {
                $this->storeState($clearance, [
                    'action' => 'manual_verification',
                    'applied' => false,
                    'message' => 'Employee tidak memiliki user account.',
                    'actor_id' => $actor->id,
                    'recorded_at' => now()->toISOString(),
                ]);

                return;
            }

            $previousStatus = $user->status;

            $user->update([
                'status' => 'inactive',
            ]);

            $this->storeState($clearance, [
                'action' => 'revoke_access',
                'applied' => true,
                'before' => [
                    'user_id' => $user->id,
                    'status' => $previousStatus,
                ],
                'after' => [
                    'user_id' => $user->id,
                    'status' => 'inactive',
                ],
                'actor_id' => $actor->id,
                'applied_at' => now()->toISOString(),
            ]);

            return;
        }

        if ($category === 'organization') {
            $employee = $this->employeeForClearance($clearance);

            $team = Team::query()
                ->where('supervisor_id', $employee->id)
                ->lockForUpdate()
                ->first();

            $division = Divisi::query()
                ->where('manager_id', $employee->id)
                ->lockForUpdate()
                ->first();

            $state = [
                'action' => 'release_organization',
                'applied' => true,
                'before' => [
                    'employee_team_id' => $employee->team_id,
                    'supervisor_team_id' => $team?->id,
                    'managed_division_id' => $division?->id,
                ],
                'after' => [
                    'employee_team_id' => null,
                    'supervisor_team_id' => $team?->id,
                    'managed_division_id' => $division?->id,
                ],
                'actor_id' => $actor->id,
                'applied_at' => now()->toISOString(),
            ];

            if ($team) {
                $team->update(['supervisor_id' => null]);
            }

            if ($division) {
                $division->update(['manager_id' => null]);
            }

            $employee->update([
                'team_id' => null,
            ]);

            $this->storeState($clearance, $state);

            return;
        }

        $this->storeState($clearance, [
            'action' => 'manual_verification',
            'applied' => false,
            'actor_id' => $actor->id,
            'recorded_at' => now()->toISOString(),
        ]);
    }

    public function rollbackClearance(
        EmployeeResignationClearance|EmployeeTerminationClearance $clearance,
    ): void {
        $state = $clearance->action_state;

        if (!($state['applied'] ?? false)) {
            return;
        }

        $before = $state['before'] ?? [];
        $after = $state['after'] ?? [];
        $employee = $this->employeeForClearance($clearance);

        if (($state['action'] ?? null) === 'revoke_access') {
            $user = $employee->user;

            if ($user && $user->status === ($after['status'] ?? 'inactive')) {
                $user->update([
                    'status' => $before['status'] ?? 'active',
                ]);
            }
        }

        if (($state['action'] ?? null) === 'release_organization') {
            if ($employee->team_id === ($after['employee_team_id'] ?? null)) {
                $employee->update([
                    'team_id' => $before['employee_team_id'] ?? null,
                ]);
            }

            if (!empty($before['supervisor_team_id'])) {
                Team::query()
                    ->whereKey($before['supervisor_team_id'])
                    ->whereNull('supervisor_id')
                    ->update([
                        'supervisor_id' => $employee->id,
                    ]);
            }

            if (!empty($before['managed_division_id'])) {
                Divisi::query()
                    ->whereKey($before['managed_division_id'])
                    ->whereNull('manager_id')
                    ->update([
                        'manager_id' => $employee->id,
                    ]);
            }
        }

        $this->storeState($clearance, array_merge($state, [
            'applied' => false,
            'rolled_back_at' => now()->toISOString(),
        ]));
    }

    public function applyHandover(
        EmployeeResignationHandoverItem|EmployeeTerminationHandoverItem $item,
        User $actor,
    ): void {
        if ($item->task_id === null || $item->handover_to_employee_id === null) {
            return;
        }

        $state = $item->action_state;

        if (($state['applied'] ?? false) === true) {
            return;
        }

        $task = Task::query()
            ->lockForUpdate()
            ->find($item->task_id);

        if (!$task) {
            return;
        }

        $previousAssigneeId = $task->assignee_id;

        $task->update([
            'assignee_id' => $item->handover_to_employee_id,
        ]);

        $this->storeState($item, [
            'action' => 'reassign_task',
            'applied' => true,
            'before' => [
                'task_id' => $task->id,
                'assignee_id' => $previousAssigneeId,
            ],
            'after' => [
                'task_id' => $task->id,
                'assignee_id' => $item->handover_to_employee_id,
            ],
            'actor_id' => $actor->id,
            'applied_at' => now()->toISOString(),
        ]);
    }

    public function rollbackHandover(
        EmployeeResignationHandoverItem|EmployeeTerminationHandoverItem $item,
    ): void {
        $state = $item->action_state;

        if (!($state['applied'] ?? false) || ($state['action'] ?? null) !== 'reassign_task') {
            return;
        }

        $before = $state['before'] ?? [];
        $after = $state['after'] ?? [];

        if (empty($before['task_id'])) {
            return;
        }

        $task = Task::query()
            ->lockForUpdate()
            ->find($before['task_id']);

        if ($task && $task->assignee_id === ($after['assignee_id'] ?? null)) {
            $task->update([
                'assignee_id' => $before['assignee_id'] ?? null,
            ]);
        }

        $this->storeState($item, array_merge($state, [
            'applied' => false,
            'rolled_back_at' => now()->toISOString(),
        ]));
    }

    public function applyCompletedClearanceActions(
        EmployeeResignationClearance|EmployeeTerminationClearance $clearance,
        User $actor,
    ): void {
        if ($clearance->status === 'completed') {
            $this->applyClearance($clearance, $actor);
        }
    }

    public function applyCompletedHandoverAction(
        EmployeeResignationHandoverItem|EmployeeTerminationHandoverItem $item,
        User $actor,
    ): void {
        if ($item->status === 'completed') {
            $this->applyHandover($item, $actor);
        }
    }

    private function employeeForClearance(
        EmployeeResignationClearance|EmployeeTerminationClearance $clearance,
    ): Employees {
        $relation = $clearance instanceof EmployeeResignationClearance
            ? 'resignation.employee.user'
            : 'termination.employee.user';

        $exit = $clearance->loadMissing($relation);

        $employee = $clearance instanceof EmployeeResignationClearance
            ? $exit->resignation?->employee
            : $exit->termination?->employee;

        return $employee
            ?? throw new \LogicException('Employee untuk clearance tidak ditemukan.');
    }

    private function storeState(object $model, array $state): void
    {
        $model->update([
            'action_state' => $state,
        ]);
    }
}
