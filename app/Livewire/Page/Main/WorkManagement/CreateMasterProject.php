<?php

namespace App\Livewire\Page\Main\WorkManagement;

use App\Models\MasterProject;
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

    public function save(): void
    {
        $this->authorize('create', MasterProject::class);
        $validated = $this->validate();

        MasterProject::create([
            ...$validated,
            'created_by' => Auth::user()?->employees?->getKey(),
            'status' => 'draft',
        ]);

        session()->flash('success', 'Master project berhasil dibuat.');
        $this->redirectRoute('work-management.master-projects', navigate: true);
    }

    public function render()
    {
        return view('livewire.page.main.work-management.create-master-project');
    }
}
