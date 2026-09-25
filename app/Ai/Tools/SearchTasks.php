<?php

namespace App\Ai\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchTasks extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search HRWork tasks by title, assignee, team, project, division, status, or due date. Returns progress, blocker, review status, and assignment data. Read-only and respects work-management permissions.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-task')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $employeeId = $request['employee_id'] ?? null;
        $team = $this->value($request['team'] ?? '');
        $division = $this->value($request['division'] ?? '');
        $project = $this->value($request['project'] ?? '');
        $status = $this->value($request['status'] ?? '');
        $overdueOnly = filter_var($request['overdue_only'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $from = $this->optionalDate($request['from_date'] ?? null);
        $to = $this->optionalDate($request['to_date'] ?? null);
        $limit = min(max((int) ($request['limit'] ?? 30), 1), 50);

        $tasks = Task::query()
            ->with([
                'divisionProject.masterProject:id,name,status',
                'divisionProject.division:id,name',
                'team:id,name,divisi_id,supervisor_id',
                'assignee.user:id,name',
                'creator.user:id,name',
            ])
            ->when($employeeId, fn ($q) => $q->where('assignee_id', (int) $employeeId))
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($builder) use ($query) {
                    $builder
                        ->where('title', 'like', "%{$query}%")
                        ->orWhereHas('assignee.user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('divisionProject', fn ($projectQuery) => $projectQuery->where('name', 'like', "%{$query}%"));
                });
            })
            ->when($team !== '', fn ($q) => $q->whereHas('team', fn ($teamQuery) => $teamQuery->where('name', 'like', "%{$team}%")))
            ->when($division !== '', fn ($q) => $q->whereHas('divisionProject.division', fn ($divisionQuery) => $divisionQuery->where('name', 'like', "%{$division}%")))
            ->when($project !== '', fn ($q) => $q->whereHas('divisionProject.masterProject', fn ($projectQuery) => $projectQuery->where('name', 'like', "%{$project}%")))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($overdueOnly, fn ($q) => $q->whereNotNull('due_date')->whereDate('due_date', '<', today())->whereNotIn('status', [Task::STATUS_DONE, Task::STATUS_CANCELLED]))
            ->when($from, fn ($q) => $q->whereDate('due_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('due_date', '<=', $to))
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->limit($limit)
            ->get();

        return $this->json([
            'success' => true,
            'count' => $tasks->count(),
            'tasks' => $tasks->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'project' => $task->divisionProject?->masterProject?->name,
                'division_project' => $task->divisionProject?->name,
                'division' => $task->divisionProject?->division?->name,
                'team' => $task->team?->name,
                'assignee' => $task->assignee?->user?->name,
                'created_by' => $task->creator?->user?->name,
                'progress' => (int) $task->progress,
                'status' => $task->status,
                'due_date' => $task->due_date?->toDateString(),
                'blocked_reason' => $task->blocked_reason,
                'submitted_at' => $task->submitted_at?->toDateTimeString(),
                'completed_at' => $task->completed_at?->toDateTimeString(),
            ])->values()->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'employee_id' => $schema->integer(),
            'query' => $schema->string(),
            'team' => $schema->string(),
            'division' => $schema->string(),
            'project' => $schema->string(),
            'status' => $schema->string(),
            'overdue_only' => $schema->boolean(),
            'from_date' => $schema->string(),
            'to_date' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(50),
        ];
    }
}
