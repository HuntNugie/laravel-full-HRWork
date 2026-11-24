<?php

namespace App\Ai\Tools;

use App\Models\Employees;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchEmployees extends HRTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search employee records in HRWork by name, employee code, position, or status. Return employee ID and code to use in GetEmployeeDetails. Read-only.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-employee')) {
            return $denied;
        }

        $query = trim((string) ($request['query'] ?? ''));
        $position = trim((string) ($request['position'] ?? ''));
        $status = trim((string) ($request['status'] ?? ''));
        $limit = min(max((int) ($request['limit'] ?? 10), 1), 20);

        $employees = Employees::query()
            ->with([
                'position:id,name',
            ])
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($queryBuilder) use ($query) {
                    $queryBuilder
                        ->where('employee_code', 'like', "%{$query}%")
                        ->orWhere('name', 'like', "%{$query}%")
                        ->orWhereHas('position', function ($positionQuery) use ($query) {
                            $positionQuery->where('name', 'like', "%{$query}%");
                        });
                });
            })
            ->when($position !== '', function ($q) use ($position) {
                $q->whereHas('position', function ($positionQuery) use ($position) {
                    $positionQuery->where('name', 'like', "%{$position}%");
                });
            })
            ->when($status !== '', function ($q) use ($status) {
                $q->where('status_employee', $status);
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
                    'name' => $employee->name,
                    'position' => $employee->position?->name,
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
            'status' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(20),
        ];
    }
}
