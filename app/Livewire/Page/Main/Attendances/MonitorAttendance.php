<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\AttedanceSetting;
use App\Models\Employees;
use App\Service\EmployeeDailyStatusService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman monitoring presensi'])]
class MonitorAttendance extends Component
{
    public string $status = '';
    public string $search = '';
    public AttedanceSetting $attedanceSetting;

    public function mount(): void
    {
        $this->attedanceSetting = AttedanceSetting::first();
    }

    #[On('change-tolerance')]
    public function refreshPage(): void
    {
        $this->attedanceSetting = AttedanceSetting::first();
    }

    public function render()
    {
        $employees = Employees::query()
            ->with([
                'user',
                'attendances' => function ($query) {
                    $query->whereDate('date', today());
                },
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($qe) {
                        $qe->where(
                            'name',
                            'like',
                            '%' . $this->search . '%'
                        );
                    });

                    $q->orWhere(
                        'employee_code',
                        'like',
                        '%' . $this->search . '%'
                    );
                });
            })
            ->get();

        $dailyStatusService = app(EmployeeDailyStatusService::class);

        foreach ($employees as $employee) {
            $attendance = $employee->attendances->first();

            $dailyState = $dailyStatusService->getStatus(
                employee: $employee,
                date: today(),
            );

            /*
            |--------------------------------------------------------------------------
            | REALTIME UI STATUS
            |--------------------------------------------------------------------------
            |
            | "working" adalah status presentasi monitoring, bukan domain status
            | baru. Jika sudah check-in tetapi belum check-out, tampilkan sedang
            | bekerja. Dasar status lainnya tetap berasal dari Daily Status.
            |
            */

            if ($attendance?->check_in_at && !$attendance->check_out_at) {
                $monitoringStatus = 'working';
                $monitoringLabel = 'Sedang Bekerja';
            } elseif (
                $dailyState['status'] === EmployeeDailyStatusService::STATUS_PENDING
            ) {
                $monitoringStatus = 'absent';
                $monitoringLabel = 'Belum Hadir';
            } else {
                $monitoringStatus = $dailyState['status'];
                $monitoringLabel = match ($dailyState['status']) {
                    EmployeeDailyStatusService::STATUS_PRESENT => 'Hadir',
                    EmployeeDailyStatusService::STATUS_LATE => 'Terlambat',
                    EmployeeDailyStatusService::STATUS_PAID_LEAVE => 'Cuti',
                    EmployeeDailyStatusService::STATUS_ABSENCE_SICK => 'Sakit',
                    EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT => 'Izin',
                    EmployeeDailyStatusService::STATUS_HOLIDAY => 'Libur',
                    EmployeeDailyStatusService::STATUS_NON_WORKING => 'Non-Hari Kerja',
                    EmployeeDailyStatusService::STATUS_OUTSIDE_CONTRACT => 'Di Luar Kontrak',
                    EmployeeDailyStatusService::STATUS_UNPRESENT => 'Belum Hadir',
                    default => '—',
                };
            }

            $employee->daily_status = $dailyState['status'];
            $employee->monitoring_status = $monitoringStatus;
            $employee->monitoring_status_label = $monitoringLabel;
        }

        if ($this->status !== '') {
            $employees = $employees->filter(
                fn($employee) => $employee->monitoring_status === $this->status
            )->values();
        }

        return view(
            'livewire.page.main.attendances.monitor-attendance',
            compact('employees')
        );
    }
}
