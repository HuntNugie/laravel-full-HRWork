<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\DivisionProject;
use App\Models\Divisi;
use App\Models\MasterProject;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Create Division Project'])]
class CreateDivisionProject extends Component
{
    public MasterProject $masterProject;
    public Collection $divisions;
    public ?int $divisi_id = null;
    public string $name = '';
    public string $description = '';
    public ?string $start_date = null;
    public ?string $due_date = null;

    public function mount(MasterProject $masterProject): void
    {
        $this->authorize('create', DivisionProject::class);

        $this->masterProject = $masterProject;
        $this->divisions = Divisi::query()
            ->with('manager.user')
            ->where('is_active', 'active')
            ->whereNotNull('manager_id')
            ->orderBy('name')
            ->get();

    }

    protected function rules(): array
    {
        return [
            'divisi_id' => ['required', 'integer', 'exists:divisis,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function save(WorkManagementService $service): void
    {
        $this->authorize('create', DivisionProject::class);
        $validated = $this->validate();

        $employee = Auth::user()?->employees;

        if (! $employee) {
            abort(403);
        }

        $project = $service->createDivisionProject(
            masterProject: $this->masterProject,
            division: Divisi::findOrFail($validated['divisi_id']),
            creator: $employee,
            name: $validated['name'],
            description: $validated['description'] ?: null,
            startDate: $validated['start_date'] ?: null,
            dueDate: $validated['due_date'] ?: null,
        );

        session()->flash('success', 'Division project berhasil dibuat.');
        $this->redirectRoute('work-management.division-projects.show', [
            'divisionProject' => $project,
        ], navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.create-division-project');
    }
}
