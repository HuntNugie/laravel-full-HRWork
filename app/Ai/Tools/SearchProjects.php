<?php

namespace App\Ai\Tools;

use App\Models\DivisionProject;
use App\Models\MasterProject;
use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchProjects extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search HRWork master projects and division projects. Can filter project name, status, division, manager, and date range. Returns project progress and task counts when available. Read-only and respects work-management permissions.';
    }

    public function handle(Request $request): Stringable|string
    {
        $user = auth()->user();

        if (! $user || (! $user->can('view-master-project') && ! $user->can('view-division-project'))) {
            return $this->json([
                'success' => false,
                'message' => 'Pengguna tidak memiliki izin untuk mengakses data project.',
            ]);
        }

        $query = $this->value($request['query'] ?? '');
        $scope = $this->value($request['scope'] ?? 'all');
        $status = $this->value($request['status'] ?? '');
        $division = $this->value($request['division'] ?? '');
        $managerId = $request['manager_id'] ?? null;
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 50);

        $result = [
            'success' => true,
            'master_projects' => [],
            'division_projects' => [],
        ];

        if (($scope === 'all' || $scope === 'master') && $user->can('view-master-project')) {
            $masters = MasterProject::query()
                ->with('creator.user:id,name')
                ->withCount(['divisionProjects', 'tasks'])
                ->when($query !== '', fn ($q) => $q->where('name', 'like', "%{$query}%"))
                ->when($status !== '', fn ($q) => $q->where('status', $status))
                ->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('start_date', '<=', $to))
                ->orderByDesc('id')
                ->limit($limit)
                ->get();

            $result['master_projects'] = $masters->map(fn (MasterProject $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'start_date' => $project->start_date?->toDateString(),
                'due_date' => $project->due_date?->toDateString(),
                'created_by' => $project->creator?->user?->name,
                'division_projects_count' => (int) $project->division_projects_count,
                'tasks_count' => (int) $project->tasks_count,
            ])->values()->all();
        }

        if (($scope === 'all' || $scope === 'division') && $user->can('view-division-project')) {
            $divisions = DivisionProject::query()
                ->with([
                    'masterProject:id,name,status',
                    'division:id,name',
                    'manager.user:id,name',
                    'tasks:id,division_project_id,progress,status',
                ])
                ->withCount(['tasks', 'progressUpdates'])
                ->when($query !== '', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhereHas('division', fn ($divisionQuery) => $divisionQuery->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('manager.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$query}%"));
                })
                ->when($status !== '', fn ($q) => $q->where('status', $status))
                ->when($division !== '', fn ($q) => $q->whereHas('division', fn ($divisionQuery) => $divisionQuery->where('name', 'like', "%{$division}%")))
                ->when($managerId, fn ($q) => $q->where('manager_id', (int) $managerId))
                ->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))
                ->when($to, fn ($q) => $q->whereDate('start_date', '<=', $to))
                ->orderByDesc('id')
                ->limit($limit)
                ->get();

            $result['division_projects'] = $divisions->map(fn (DivisionProject $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'master_project' => $project->masterProject?->name,
                'division' => $project->division?->name,
                'manager' => $project->manager?->user?->name,
                'status' => $project->status,
                'start_date' => $project->start_date?->toDateString(),
                'due_date' => $project->due_date?->toDateString(),
                'is_required' => (bool) $project->is_required,
                'manual_progress' => (int) $project->manual_progress,
                'automatic_progress' => $project->automaticProgress(),
                'task_count' => (int) $project->tasks_count,
                'progress_update_count' => (int) $project->progress_updates_count,
            ])->values()->all();
        }

        return $this->json($result);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string(),
            'scope' => $schema->string(),
            'status' => $schema->string(),
            'division' => $schema->string(),
            'manager_id' => $schema->integer(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
