<?php

namespace App\Ai\Tools;

use App\Models\Employees;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetEmployeeDetails extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Get a detailed HRWork employee profile after an employee has been identified. Includes job, organization, employment status, contract summary, and exit history summary. Use this for employee-specific details instead of guessing.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-employee')) {
            return $denied;
        }

        $employeeId = $request['employee_id'] ?? null;
        $query = $this->value($request['query'] ?? '');

        $employee = Employees::query()
            ->with([
                'user:id,name,email,status',
                'profile:id,employee_id,gender,phone_number,birth_date',
                'position:id,name',
                'team:id,name,divisi_id,supervisor_id',
                'team.divisi:id,name,manager_id',
                'team.supervisor:id,user_id,employee_code',
                'team.supervisor.user:id,name',
                'managedDivisi:id,name',
                'latestEmployeeContract' => function ($contractQuery) {
                    $contractQuery->select([
                        'employee_contracts.id',
                        'employee_contracts.employee_id',
                        'employee_contracts.contract_numnber',
                        'employee_contracts.employement_type',
                        'employee_contracts.start_date',
                        'employee_contracts.end_date',
                        'employee_contracts.salary_daily',
                        'employee_contracts.status',
                        'employee_contracts.position_id',
                    ]);
                },
                'latestEmployeeContract.position:id,name',
            ])
            ->withCount([
                'employeeContract as contract_count',
                'leaveRequest as leave_request_count',
                'resignations as resignation_count',
                'terminations as termination_count',
            ])
            ->when($employeeId, fn ($q) => $q->whereKey((int) $employeeId))
            ->when(! $employeeId && $query !== '', function ($q) use ($query) {
                $q->where(function ($builder) use ($query) {
                    $builder
                        ->where('employee_code', 'like', "%{$query}%")
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery
                                ->where('name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                        });
                });
            })
            ->first();

        if (! $employee) {
            return $this->json([
                'success' => true,
                'found' => false,
                'message' => 'Karyawan tidak ditemukan.',
            ]);
        }

        return $this->json([
            'success' => true,
            'found' => true,
            'employee' => [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'name' => $employee->user?->name,
                'email' => $employee->user?->email,
                'user_status' => $employee->user?->status,
                'employment_status' => $employee->status_employee,
                'join_date' => $employee->JoinDate?->toDateString(),
                'resign_date' => $employee->ResignDate?->toDateString(),
                'termination_date' => $employee->TerminationDate?->toDateString(),
                'profile' => [
                    'gender' => $employee->profile?->gender,
                    'phone_number' => $employee->profile?->phone_number,
                    'birth_date' => $employee->profile?->birth_date?->toDateString(),
                ],
                'organization' => [
                    'position' => $employee->position?->name,
                    'division' => $employee->team?->divisi?->name ?? $employee->managedDivisi?->name,
                    'team' => $employee->team?->name,
                    'supervisor' => $employee->team?->supervisor?->user?->name,
                ],
                'latest_contract' => $employee->latestEmployeeContract ? [
                    'number' => $employee->latestEmployeeContract->contract_numnber,
                    'employment_type' => $employee->latestEmployeeContract->employement_type,
                    'position' => $employee->latestEmployeeContract->position?->name,
                    'start_date' => $employee->latestEmployeeContract->start_date?->toDateString(),
                    'end_date' => $employee->latestEmployeeContract->end_date?->toDateString(),
                    'salary_daily' => (string) $employee->latestEmployeeContract->salary_daily,
                    'status' => $employee->latestEmployeeContract->status,
                ] : null,
                'counts' => [
                    'contracts' => $employee->contract_count,
                    'leave_requests' => $employee->leave_request_count,
                    'resignations' => $employee->resignation_count,
                    'terminations' => $employee->termination_count,
                ],
            ],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'query' => $schema->string(),
        ];
    }
}
