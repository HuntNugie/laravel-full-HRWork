<?php

namespace App\Service;

use App\Models\EmployeeResignationClearance;
use App\Models\EmployeeTerminationClearance;
use App\Models\Employees;
use App\Models\User;

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
            // Clearance historis tetap bisa dibaca, tanpa mutasi struktur yang dipensiunkan.
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

        $this->storeState($clearance, array_merge($state, [
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
