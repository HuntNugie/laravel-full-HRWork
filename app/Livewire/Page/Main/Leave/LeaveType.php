<?php

namespace App\Livewire\Page\Main\Leave;

use App\Models\LeaveType as ModelsLeaveType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman jenis cuti'])]
class LeaveType extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[On('change-leave-type')]
    public function updatedLeaveType() {}

    public function render()
    {
        $leaveTypes = ModelsLeaveType::query()->when($this->search, function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%');
        })->latest()->paginate(5);
        return view('livewire.page.main.leave.leave-type', compact('leaveTypes'));
    }
}
