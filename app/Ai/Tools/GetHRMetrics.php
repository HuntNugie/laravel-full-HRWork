<?php
namespace App\Ai\Tools;
use App\Models\EmployeeContract;
use App\Models\Employees;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetHRMetrics extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Read exact workforce and contract status aggregates, active employees by position and scheduled contract dates, subject to permissions.';
    }
    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-ai-assistant')) { return $denied; }
        $user = auth()->user();
        $from = $this->optionalDate($request['from_date'] ?? null) ?? today()->startOfMonth()->toDateString();
        $to = $this->optionalDate($request['to_date'] ?? null) ?? today()->endOfMonth()->toDateString();
        if (! $this->validDate($from) || ! $this->validDate($to) || $from > $to) {
            return $this->json(['success' => false, 'message' => 'Rentang tanggal tidak valid.']);
        }
        $metrics = [];
        if ($user->can('view-employee')) {
            $counts = Employees::query()->selectRaw('status_employee, count(*) as total')->groupBy('status_employee')->pluck('total', 'status_employee');
            $metrics['workforce_current'] = ['total_employees' => (int) $counts->sum(), 'by_status' => $counts->toArray()];
            if ($user->can('view-position')) {
                $metrics['active_by_position'] = Employees::query()->where('employees.status_employee', 'active')
                    ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
                    ->selectRaw('positions.name as position, count(*) as total')
                    ->groupBy('positions.id', 'positions.name')->orderBy('positions.name')->get()->toArray();
            }
        }
        if ($user->can('view-contract')) {
            $counts = EmployeeContract::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
            $metrics['contracts_current'] = ['total' => (int) $counts->sum(), 'by_status' => $counts->toArray(),
                'expiring_in_30_days' => EmployeeContract::where('status', 'active')->whereBetween('end_date', [today(), today()->addDays(30)])->count()];
            $metrics['contract_period'] = ['from' => $from, 'to' => $to,
                'started_in_period' => EmployeeContract::whereBetween('start_date', [$from, $to])->count(),
                'scheduled_end_in_period' => EmployeeContract::whereBetween('end_date', [$from, $to])->count()];
        }
        return $metrics ? $this->json(['success' => true, 'metrics' => $metrics])
            : $this->json(['success' => false, 'message' => 'Tidak memiliki izin melihat ringkasan karyawan atau kontrak.']);
    }
    public function schema(JsonSchema $schema): array
    {
        return ['from_date' => $schema->string(), 'to_date' => $schema->string()];
    }
}
