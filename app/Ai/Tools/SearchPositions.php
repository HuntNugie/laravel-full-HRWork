<?php

namespace App\Ai\Tools;

use App\Models\Position;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchPositions extends HRTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Search HRWork job positions and employee counts when authorized. Read-only.';
    }

    public function handle(Request $request): Stringable|string
    {
        if ($denied = $this->denied('view-position')) {
            return $denied;
        }

        $query = $this->value($request['query'] ?? '');
        $limit = min(max((int) ($request['limit'] ?? 20), 1), 30);

        $result = [
            'success' => true,
            'positions' => [],
        ];

        $result['positions'] = Position::query()
            ->when(auth()->user()->can('view-employee'), fn ($q) => $q->withCount('employees'))
            ->when($query !== '', fn ($q) => $q->where('name', 'like', "%{$query}%"))
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn (Position $position) => [
                'id' => $position->id,
                'name' => $position->name,
                'status' => $position->is_active,
                ...(auth()->user()->can('view-employee') ? ['employee_count' => (int) $position->employees_count] : []),
            ])->values()->all();

        return $this->json($result);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string(),
            'limit' => $schema->integer()->min(1)->max(30),
        ];
    }
}
