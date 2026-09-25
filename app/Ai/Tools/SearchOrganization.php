<?php

namespace App\Ai\Tools;

use App\Models\Divisi;
use App\Models\Position;
use App\Models\Team;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchOrganization extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search HRWork organization structure: divisions, teams, positions, managers, supervisors, and employee counts. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-employee')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $entity = $this->value($request['entity'] ?? 'all');
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 30);

        $result = [
            'success' => true,
            'divisions' => [],
            'teams' => [],
            'positions' => [],
        ];

        if ($entity === 'all' || $entity === 'division') {
            $result['divisions'] = Divisi::query()
                ->with(['manager.user:id,name'])
                ->withCount('team')
                ->when($query !== '', fn ($q) => $q->where('name', 'like', "%{$query}%"))
                ->orderBy('name')
                ->limit($limit)
                ->get()
                ->map(fn (Divisi $division) => [
                    'id' => $division->id,
                    'name' => $division->name,
                    'description' => $division->description,
                    'status' => $division->is_active,
                    'manager' => $division->manager?->user?->name,
                    'team_count' => (int) $division->team_count,
                ])->values()->all();
        }

        if ($entity === 'all' || $entity === 'team') {
            $result['teams'] = Team::query()
                ->with(['divisi:id,name', 'supervisor.user:id,name'])
                ->withCount('employees')
                ->when($query !== '', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhereHas('divisi', fn ($divisionQuery) => $divisionQuery->where('name', 'like', "%{$query}%"));
                })
                ->orderBy('name')
                ->limit($limit)
                ->get()
                ->map(fn (Team $team) => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'division' => $team->divisi?->name,
                    'supervisor' => $team->supervisor?->user?->name,
                    'employee_count' => (int) $team->employees_count,
                ])->values()->all();
        }

        if ($entity === 'all' || $entity === 'position') {
            $result['positions'] = Position::query()
                ->withCount('employees')
                ->when($query !== '', fn ($q) => $q->where('name', 'like', "%{$query}%"))
                ->orderBy('name')
                ->limit($limit)
                ->get()
                ->map(fn (Position $position) => [
                    'id' => $position->id,
                    'name' => $position->name,
                    'employee_count' => (int) $position->employees_count,
                ])->values()->all();
        }

        return $this->json($result);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string(),
            'entity' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(30),
        ];
    }
}
