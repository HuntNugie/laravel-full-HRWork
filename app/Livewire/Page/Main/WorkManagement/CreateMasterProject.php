<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\MasterProject;
use App\Service\WorkManagementService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Create Master Project'])]
class CreateMasterProject extends Component
{
    public string $name = '';
    public string $description = '';
    public ?string $start_date = null;
    public ?string $due_date = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function save(WorkManagementService $service): void
    {
        $this->authorize('create', MasterProject::class);
        $validated = $this->validate();

        $employee = Auth::user()?->employees;
        if (! $employee) {
            abort(403);
        }

        $project = $service->createMasterProject(
            creator: $employee,
            name: $validated['name'],
            description: $validated['description'] ?: null,
            startDate: $validated['start_date'] ?: null,
            dueDate: $validated['due_date'] ?: null,
        );

        session()->flash('success', 'Master project berhasil dibuat.');
        $this->redirectRoute('work-management.master-projects.show', ['masterProject' => $project], navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.create-master-project');
    }
}
