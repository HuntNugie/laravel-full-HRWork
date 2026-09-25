<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\DivisionProject;
use App\Models\Employees;
use App\Models\Task;
use App\Models\Team;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Create Task'])]
class CreateTask extends Component
{
    public DivisionProject $divisionProject;
    public Team $team;
    public Collection $assignees;

    public ?int $assignee_id = null;
    public string $title = '';
    public string $description = '';
    public ?string $due_date = null;

    public function mount(DivisionProject $divisionProject, Team $team): void
    {
        $this->authorize('create', Task::class);

        $this->divisionProject = $divisionProject->load('teams');

        if (! $this->divisionProject->teams->contains('id', $team->id)) {
            abort(404);
        }

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        if ((int) $team->supervisor_id !== (int) $employee->id) {
            abort(403);
        }

        $this->team = $team->load('supervisor.user');
        $this->loadAssignees();
    }

    protected function rules(): array
    {
        return [
            'assignee_id' => ['required', 'integer', 'exists:employees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function save(WorkManagementService $service): void
    {
        $this->authorize('create', Task::class);

        $validated = $this->validate();
        $creator = Auth::user()?->employees;

        if (! $creator) {
            abort(403);
        }

        $service->createTask(
            $this->divisionProject,
            $this->team,
            Employees::findOrFail($validated['assignee_id']),
            $creator,
            $validated['title'],
            $validated['description'] ?: null,
            $validated['due_date'] ?: null,
        );

        session()->flash('success', 'Task berhasil dibuat.');
        $this->redirectRoute('work-management.division-projects.teams.show', [
            'divisionProject' => $this->divisionProject,
            'team' => $this->team,
        ], navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.create-task');
    }

    private function loadAssignees(): void
    {
        $this->assignees = Employees::query()
            ->where('team_id', $this->team->id)
            ->where('id', '!=', $this->team->supervisor_id)
            ->where('status_employee', 'active')
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
            ->with('user')
            ->orderBy('id')
            ->get();
    }
}
