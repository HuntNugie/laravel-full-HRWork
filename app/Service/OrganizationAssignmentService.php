<?php

namespace App\Service;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrganizationAssignmentService
{
    public function assignSupervisor(Team $team, Employees $newSupervisor): Team
    {
        return DB::transaction(function () use ($team, $newSupervisor) {
            $team = Team::query()->lockForUpdate()->findOrFail($team->id);
            $newSupervisor = Employees::query()
                ->with('user')
                ->lockForUpdate()
                ->findOrFail($newSupervisor->id);

            $this->ensureActiveEmployee($newSupervisor, 'employeeId');

            if (! $newSupervisor->user || $newSupervisor->user->status !== 'active') {
                throw ValidationException::withMessages([
                    'employeeId' => 'Supervisor harus memiliki akun aktif.',
                ]);
            }

            if ((int) $team->supervisor_id === (int) $newSupervisor->id) {
                $newSupervisor->user->assignRole('employee');
                $newSupervisor->user->assignRole('supervisor');

                return $team->refresh();
            }

            $existingSupervisorTeam = $newSupervisor->supervisorTeam()->first();

            if ($existingSupervisorTeam && (int) $existingSupervisorTeam->id !== (int) $team->id) {
                throw ValidationException::withMessages([
                    'employeeId' => 'Employee tersebut sudah menjadi supervisor pada Team lain.',
                ]);
            }

            if ($newSupervisor->team_id !== null && (int) $newSupervisor->team_id !== (int) $team->id) {
                throw ValidationException::withMessages([
                    'employeeId' => 'Supervisor baru hanya dapat berasal dari Team ini atau employee yang belum memiliki Team.',
                ]);
            }

            $oldSupervisor = $team->supervisor()->with('user')->first();

            if ($oldSupervisor && (int) $oldSupervisor->id !== (int) $newSupervisor->id) {
                $oldSupervisor->user?->removeRole('supervisor');
                $oldSupervisor->user?->assignRole('employee');
            }

            $newSupervisor->update([
                'team_id' => $team->id,
            ]);

            $newSupervisor->user->assignRole('employee');
            $newSupervisor->user->assignRole('supervisor');

            $team->update([
                'supervisor_id' => $newSupervisor->id,
            ]);

            return $team->refresh();
        });
    }

    public function assignDivisionManager(Divisi $division, ?Employees $newManager): Divisi
    {
        return DB::transaction(function () use ($division, $newManager) {
            $division = Divisi::query()->lockForUpdate()->findOrFail($division->id);

            $oldManager = $division->manager()
                ->with('user')
                ->lockForUpdate()
                ->first();

            if ($newManager) {
                $newManager = Employees::query()
                    ->with('user')
                    ->lockForUpdate()
                    ->findOrFail($newManager->id);

                $this->ensureActiveEmployee($newManager, 'managerId');

                if (! $newManager->user || $newManager->user->status !== 'active') {
                    throw ValidationException::withMessages([
                        'managerId' => 'Manager harus memiliki akun aktif.',
                    ]);
                }

                if ((int) $division->manager_id !== (int) $newManager->id) {
                    if ($newManager->team_id !== null) {
                        throw ValidationException::withMessages([
                            'managerId' => 'Manager baru harus merupakan employee yang belum memiliki Team.',
                        ]);
                    }

                    $managedDivision = $newManager->managedDivisi()->first();

                    if ($managedDivision && (int) $managedDivision->id !== (int) $division->id) {
                        throw ValidationException::withMessages([
                            'managerId' => 'Employee tersebut sudah menjadi manager pada Divisi lain.',
                        ]);
                    }

                    if ($newManager->supervisorTeam()->exists()) {
                        throw ValidationException::withMessages([
                            'managerId' => 'Employee tersebut masih menjadi supervisor Team.',
                        ]);
                    }
                }
            }

            if ($oldManager && (! $newManager || (int) $oldManager->id !== (int) $newManager->id)) {
                $oldManager->user?->removeRole('manager');
                $oldManager->user?->assignRole('employee');
            }

            if (! $newManager) {
                $division->update(['manager_id' => null]);

                return $division->refresh();
            }

            $newManager->user->assignRole('employee');
            $newManager->user->assignRole('manager');

            $division->update([
                'manager_id' => $newManager->id,
            ]);

            return $division->refresh();
        });
    }

    private function ensureActiveEmployee(Employees $employee, string $field): void
    {
        if ($employee->status_employee !== 'active') {
            throw ValidationException::withMessages([
                $field => 'Karyawan harus aktif.',
            ]);
        }
    }
}
