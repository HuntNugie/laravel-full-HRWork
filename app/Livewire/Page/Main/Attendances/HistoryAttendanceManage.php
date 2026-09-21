<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\Employees;
use App\Service\EmployeeDailyStatusService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Riwayat Presensi'])]
class HistoryAttendanceManage extends Component
{
    public string $search = '';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public string $status = '';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = today()->format('Y-m-d');
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = today()->format('Y-m-d');
        $this->status = '';
    }

    public function render()
    {
        $startDate = $this->startDate
            ? Carbon::parse($this->startDate)->startOfDay()
            : today()->startOfMonth()->startOfDay();

        $endDate = $this->endDate
            ? Carbon::parse($this->endDate)->endOfDay()
            : today()->endOfDay();

        $employees = Employees::query()
            ->with([
                'user',
                'attendances' => function ($query) use ($startDate, $endDate) {
                    $query
                        ->whereDate('date', '>=', $startDate->toDateString())
                        ->whereDate('date', '<=', $endDate->toDateString());
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
        $attendanceHistory = collect();

        foreach ($employees as $employee) {
            $attendanceById = $employee->attendances->keyBy('id');

            $statuses = $dailyStatusService->getStatuses(
                employee: $employee,
                startDate: $startDate,
                endDate: $endDate,
            );

            foreach ($statuses as $state) {
                $date = Carbon::parse($state['date']);
                $attendance = $state['attendance_id']
                    ? $attendanceById->get($state['attendance_id'])
                    : null;

                $checkIn = $attendance?->check_in_at
                    ? Carbon::parse($attendance->check_in_at)
                    : null;

                $checkOut = $attendance?->check_out_at
                    ? Carbon::parse($attendance->check_out_at)
                    : null;

                $duration = '—';

                if ($checkIn && $checkOut) {
                    $diff = $checkIn->diff($checkOut);

                    $duration = sprintf(
                        '%dj %dm',
                        ($diff->days * 24) + $diff->h,
                        $diff->i
                    );
                }

                $statusLabel = match ($state['status']) {
                    EmployeeDailyStatusService::STATUS_OUTSIDE_CONTRACT => 'Di Luar Kontrak',
                    EmployeeDailyStatusService::STATUS_NON_WORKING => 'Non-Hari Kerja',
                    EmployeeDailyStatusService::STATUS_HOLIDAY => 'Libur',
                    EmployeeDailyStatusService::STATUS_PRESENT => 'Hadir',
                    EmployeeDailyStatusService::STATUS_LATE => 'Terlambat',
                    EmployeeDailyStatusService::STATUS_PAID_LEAVE => 'Cuti',
                    EmployeeDailyStatusService::STATUS_ABSENCE_SICK => 'Sakit',
                    EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT => 'Izin',
                    EmployeeDailyStatusService::STATUS_PENDING => 'Menunggu',
                    EmployeeDailyStatusService::STATUS_UNPRESENT => 'Belum Hadir',
                    default => '—',
                };

                $avatar = $employee->user?->getFirstMediaUrl('avatar');

                $attendanceHistory->push([
                    'attendance_id' => $state['attendance_id'],

                    'date' => $date->translatedFormat('d M Y'),

                    'date_value' => $state['date'],

                    'employee_id' => $employee->id,

                    'employee_name' => $employee->user?->name ?? '—',

                    'employee_code' => $employee->employee_code ?? '—',

                    'avatar' => $avatar,

                    'check_in' => $checkIn?->format('H:i') ?? '—',

                    'check_out' => $checkOut?->format('H:i') ?? '—',

                    'duration' => $duration,

                    'status' => $state['status'],

                    'status_label' => $statusLabel,
                ]);
            }
        }

        if ($this->status !== '') {
            $attendanceHistory = $attendanceHistory
                ->filter(fn(array $attendance) => $attendance['status'] === $this->status)
                ->values();
        }

        $attendanceHistory = $attendanceHistory
            ->sort(function ($a, $b) {
                $dateCompare = strcmp(
                    $b['date_value'],
                    $a['date_value']
                );

                if ($dateCompare !== 0) {
                    return $dateCompare;
                }

                return strcasecmp(
                    $a['employee_name'],
                    $b['employee_name']
                );
            })
            ->values();

        return view(
            'livewire.page.main.attendances.history-attendance-manage',
            [
                'attendances' => $attendanceHistory,
            ]
        );
    }
}
