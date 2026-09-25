<?php

namespace App\Ai\Tools;

use App\Models\Employees;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchEmployees implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search employee records in HRWork by name, email, employee code, position, team, division, or status. Use this tool whenever the user asks about employees or employee lists. Always return the employee ID and employee code so the result can be passed to GetEmployeeDetails or another employee-specific tool. Only read employee data; never modify records.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $user = auth()->user();

        if (! $user || ! $user->can('view-employee')) {
            return json_encode([
                'success' => false,
                'message' => 'Pengguna tidak memiliki izin untuk melihat data karyawan.',
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $query = trim((string) ($request['query'] ?? ''));
        $position = trim((string) ($request['position'] ?? ''));
        $team = trim((string) ($request['team'] ?? ''));
        $division = trim((string) ($request['division'] ?? ''));
        $status = trim((string) ($request['status'] ?? ''));
        $limit = min(max((int) ($request['limit'] ?? 10), 1), 20);

        $employees = Employees::query()
            ->with([
                'user:id,name,email',
                'position:id,name',
                'team:id,name,divisi_id',
                'team.divisi:id,name',
                'managedDivisi:id,name',
            ])
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder
                        ->where('employee_code', 'like', "%{$query}%")
                        ->orWhereHas('user', function ($userQuery) use ($query) {
                            $userQuery
                                ->where('name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                        })
                        ->orWhereHas('position', function ($positionQuery) use ($query) {
                            $positionQuery->where('name', 'like', "%{$query}%");
                        })
                        ->orWhereHas('team', function ($teamQuery) use ($query) {
                            $teamQuery->where('name', 'like', "%{$query}%");
                        })
                        ->orWhereHas('team.divisi', function ($divisionQuery) use ($query) {
                            $divisionQuery->where('name', 'like', "%{$query}%");
                        })
                        ->orWhereHas('managedDivisi', function ($divisionQuery) use ($query) {
                            $divisionQuery->where('name', 'like', "%{$query}%");
                        });
                });
            })
            ->when($position !== '', function ($q) use ($position) {
                $q->whereHas('position', function ($positionQuery) use ($position) {
                    $positionQuery->where('name', 'like', "%{$position}%");
                });
            })
            ->when($team !== '', function ($q) use ($team) {
                $q->whereHas('team', function ($teamQuery) use ($team) {
                    $teamQuery->where('name', 'like', "%{$team}%");
                });
            })
            ->when($division !== '', function ($q) use ($division) {
                $q->where(function ($query) use ($division) {
                    $query
                        ->whereHas('team.divisi', function ($divisionQuery) use ($division) {
                            $divisionQuery->where('name', 'like', "%{$division}%");
                        })
                        ->orWhereHas('managedDivisi', function ($divisionQuery) use ($division) {
                            $divisionQuery->where('name', 'like', "%{$division}%");
                        });
                });
            })
            ->when($status !== '', function ($q) use ($status) {
                $q->where('status_employee', 'like', "%{$status}%");
            })
            ->latest()
            ->limit($limit)
            ->get();

        return json_encode([
            'success' => true,
            'count' => $employees->count(),
            'employees' => $employees->map(function (Employees $employee) {
                return [
                    'employee_id' => $employee->id,
                    'employee_code' => $employee->employee_code,
                    'name' => $employee->user?->name,
                    'email' => $employee->user?->email,
                    'position' => $employee->position?->name,
                    'division' => $employee->team?->divisi?->name
                        ?? $employee->managedDivisi?->name,
                    'team' => $employee->team?->name,
                    'status' => $employee->status_employee,
                ];
            })->values()->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string(),
            'position' => $schema->string(),
            'team' => $schema->string(),
            'division' => $schema->string(),
            'status' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(20),
        ];
    }
}
