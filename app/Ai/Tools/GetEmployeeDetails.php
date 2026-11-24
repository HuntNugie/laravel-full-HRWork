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
        return 'Read an identified employee employment status, job position and recent status history. No private profile or contract data.';
    }
    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('show-employee')) { return $denied; }
        $resolved = $this->resolveEmployee($request['employee_id'] ?? null, $this->value($request['employee_code'] ?? ''), $this->value($request['query'] ?? ''));
        if ($resolved['error'] || ! $resolved['employee_id']) {
            return $this->json(['success' => false, 'message' => $resolved['message'] ?? 'Tentukan identitas karyawan terlebih dahulu.']);
        }
        $employee = Employees::with('position:id,name')->find($resolved['employee_id']);
        if (! $employee) { return $this->json(['success' => false, 'message' => 'Karyawan tidak ditemukan.']); }
        return $this->json(['success' => true, 'employee' => [
            'id' => $employee->id,
            'employee_code' => $employee->employee_code,
            'name' => $employee->name,
            'employment_status' => $employee->status_employee,
            'position' => $employee->position?->name,
            'status_history' => $employee->statusHistory()->latest('effective_date')->latest('id')->limit(10)->get(['old_status','new_status','effective_date'])->toArray(),
        ]]);
    }
    public function schema(JsonSchema $schema): array
    {
        return ['employee_id' => $schema->integer(), 'employee_code' => $schema->string(), 'query' => $schema->string()];
    }
}
