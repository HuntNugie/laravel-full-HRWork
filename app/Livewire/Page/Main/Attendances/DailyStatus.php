<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\Divisi;
use App\Models\Employees;
use App\Models\Team;
use App\Service\EmployeeDailyStatusService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Daily Status Karyawan'])]
class DailyStatus extends Component
{
    public string $date;

    public string $search = '';

    public string $status = '';

    public ?int $divisiId = null;

    public ?int $teamId = null;

    public function mount(): void
    {
        $this->date = today()->toDateString();
    }

    public function updatedDivisiId(): void
    {
        $this->teamId = null;
    }

    public function resetFilter(): void
    {
        $this->date = today()->toDateString();
        $this->search = '';
        $this->status = '';
        $this->divisiId = null;
        $this->teamId = null;
    }

    private function employees(): iterable
    {
        $employees = Employees::query()
            ->with([
                'user',
                'team.divisi',
                'attendances' => function ($query) {
                    $query->whereDate('date', $this->date);
                },
            ])
            ->when(
                filled($this->search),
                function ($query) {
                    $search = trim($this->search);

                    $query->where(function ($employeeQuery) use ($search) {
                        $employeeQuery
                            ->where('employee_code', 'like', "%{$search}%")
                            ->orWhereHas(
                                'user',
                                fn($userQuery) => $userQuery->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                            );
                    });
                }
            )
            ->when(
                $this->divisiId,
                fn($query) => $query->whereHas(
                    'team',
                    fn($teamQuery) => $teamQuery->where(
                        'divisi_id',
                        $this->divisiId
                    )
                )
            )
            ->when(
                $this->teamId,
                fn($query) => $query->where(
                    'team_id',
                    $this->teamId
                )
            )
            ->orderBy('employee_code')
            ->get();

        $dailyStatusService = app(EmployeeDailyStatusService::class);

        foreach ($employees as $employee) {
            $state = $dailyStatusService->getStatus(
                employee: $employee,
                date: $this->date,
            );

            if ($this->status !== '' && $state['status'] !== $this->status) {
                continue;
            }

            $attendance = $employee->attendances->first();

            yield [
                'state' => $state,
                'employee_name' => $employee->user?->name ?? '—',
                'employee_code' => $employee->employee_code ?? '—',
                'avatar' => $employee->user?->getFirstMediaUrl('avatar'),
                'division_name' => $employee->team?->divisi?->name ?? '—',
                'team_name' => $employee->team?->name ?? '—',
                'work_time_start' => $state['work_time_id']
                    ? $this->formatTimeValue($state['work_time_id'], 'start_time')
                    : null,
                'work_time_end' => $state['work_time_id']
                    ? $this->formatTimeValue($state['work_time_id'], 'end_time')
                    : null,
                'check_in' => $attendance?->check_in_at?->format('H:i'),
                'check_out' => $attendance?->check_out_at?->format('H:i'),
                'late_minutes' => $state['late_minutes'],
            ];
        }
    }

    private function formatTimeValue(int $workTimeId, string $column): ?string
    {
        $workTime = \App\Models\WorkTime::query()->find($workTimeId);

        return $workTime?->{$column}
            ? Carbon::parse($workTime->{$column})->format('H:i')
            : null;
    }

    private function label(string $status): string
    {
        return match ($status) {
            EmployeeDailyStatusService::STATUS_PRESENT => 'Hadir',
            EmployeeDailyStatusService::STATUS_LATE => 'Terlambat',
            EmployeeDailyStatusService::STATUS_PAID_LEAVE => 'Cuti',
            EmployeeDailyStatusService::STATUS_ABSENCE_SICK => 'Sakit',
            EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT => 'Izin',
            EmployeeDailyStatusService::STATUS_PENDING => 'Menunggu',
            EmployeeDailyStatusService::STATUS_UNPRESENT => 'Belum Hadir',
            EmployeeDailyStatusService::STATUS_HOLIDAY => 'Libur',
            EmployeeDailyStatusService::STATUS_NON_WORKING => 'Non-Hari Kerja',
            EmployeeDailyStatusService::STATUS_OUTSIDE_CONTRACT => 'Di Luar Kontrak',
            default => '—',
        };
    }

    private function intent(string $status): string
    {
        return match ($status) {
            EmployeeDailyStatusService::STATUS_PRESENT => 'success',
            EmployeeDailyStatusService::STATUS_LATE => 'warning',
            EmployeeDailyStatusService::STATUS_PAID_LEAVE,
            EmployeeDailyStatusService::STATUS_ABSENCE_SICK,
            EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT => 'accent',
            EmployeeDailyStatusService::STATUS_UNPRESENT => 'danger',
            EmployeeDailyStatusService::STATUS_PENDING => 'secondary',
            default => 'neutral',
        };
    }

    private function sourceLabel(string $source): string
    {
        return match ($source) {
            'attendance' => 'Presensi',
            'leave' => 'Cuti',
            'absence' => 'Sakit/Izin',
            'holiday' => 'Hari Libur',
            'work_time' => 'Jadwal Kerja',
            'contract' => 'Kontrak',
            'calculation' => 'Perhitungan',
            default => ucfirst($source),
        };
    }

    public function render(): View
    {
        $rows = collect($this->employees());

        $summary = [
            'present' => $rows->where('state.status', EmployeeDailyStatusService::STATUS_PRESENT)->count(),
            'late' => $rows->where('state.status', EmployeeDailyStatusService::STATUS_LATE)->count(),
            'paid_leave' => $rows->where('state.status', EmployeeDailyStatusService::STATUS_PAID_LEAVE)->count(),
            'absence' => $rows->whereIn('state.status', [
                EmployeeDailyStatusService::STATUS_ABSENCE_SICK,
                EmployeeDailyStatusService::STATUS_ABSENCE_PERMIT,
            ])->count(),
            'unpresent' => $rows->where('state.status', EmployeeDailyStatusService::STATUS_UNPRESENT)->count(),
            'pending' => $rows->where('state.status', EmployeeDailyStatusService::STATUS_PENDING)->count(),
        ];

        $divisis = Divisi::query()
            ->orderBy('name')
            ->get();

        $teams = Team::query()
            ->when(
                $this->divisiId,
                fn($query) => $query->where('divisi_id', $this->divisiId)
            )
            ->orderBy('name')
            ->get();

        return view(
            'livewire.page.main.attendances.daily-status',
            [
                'rows' => $rows,
                'summary' => $summary,
                'divisis' => $divisis,
                'teams' => $teams,
                'statusLabel' => fn(string $status) => $this->label($status),
                'statusIntent' => fn(string $status) => $this->intent($status),
                'sourceLabel' => fn(string $source) => $this->sourceLabel($source),
            ]
        );
    }
}
