<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\Employees;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman monitoring presensi'])]
class MonitorAttendance extends Component
{
    public string $status = '';
    public string $search = '';


    public function render()
    {

        $employees = Employees::query()
            ->with([
                'user',
                'attendances' => function ($query) {
                    $query->whereDate('date', today());
                },
            ])->when($this->search, function ($q) {
                $q->whereHas('user', function ($qe) {
                    $qe->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('employee_code', "like", '%' . $this->search . '%');
            })
            ->get();

        foreach ($employees as $employee) {

            $attendance = $employee->attendances->first();

            if (!$attendance) {
                $employee->monitoring_status = 'absent';
            } elseif (!$attendance->check_out_at) {
                $employee->monitoring_status = 'working';
            } elseif ($attendance->check_in_at->format('H:i:s') > '08:00:00') {
                $employee->monitoring_status = 'late';
            } else {
                $employee->monitoring_status = 'present';
            }
        }

        if ($this->status) {
            $employees = $employees->filter(
                fn($employee) => $employee->monitoring_status === $this->status
            );
        }
        return view('livewire.page.main.attendances.monitor-attendance', compact('employees'));
    }
}
