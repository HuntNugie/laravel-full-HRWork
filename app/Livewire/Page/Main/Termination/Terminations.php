<?php

namespace App\Livewire\Page\Main\Termination;

use App\Models\EmployeeTermination;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Manajemen PHK'])]
class Terminations extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $reasonType = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedReasonType(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'reasonType']);
        $this->resetPage();
    }

    public function render()
    {
        $query = EmployeeTermination::query()
            ->with([
                'employee.user',
                'employee.position',
            ])
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';

                $query->where(function ($query) use ($search) {
                    $query
                        ->whereHas('employee.user', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', $search)
                                ->orWhere('email', 'like', $search);
                        })
                        ->orWhereHas('employee', function ($query) use ($search) {
                            $query->where('employee_code', 'like', $search);
                        });
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->reasonType, function ($query) {
                $query->where('reason_type', $this->reasonType);
            });

        $terminations = $query
            ->latest()
            ->paginate(10);

        $summary = [
            'in_progress' => (clone $query)->where('status', EmployeeTermination::STATUS_IN_PROGRESS)->count(),
            'completed' => (clone $query)->where('status', EmployeeTermination::STATUS_COMPLETED)->count(),
            'cancelled' => (clone $query)->where('status', EmployeeTermination::STATUS_CANCELLED)->count(),
        ];

        $reasonTypes = app(\App\Service\TerminationService::class)->reasonTypes();

        return view('livewire.page.main.termination.terminations', compact(
            'terminations',
            'summary',
            'reasonTypes',
        ));
    }
}
