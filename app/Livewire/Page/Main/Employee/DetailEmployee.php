<?php

namespace App\Livewire\Page\Main\Employee;

use App\Models\Attendances;
use App\Models\Employees;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman Detail Karyawan'])]
class DetailEmployee extends Component
{
    public Employees $employee;

    public string $attendanceMonth;

    public function mount(Employees $employee): void
    {
        $this->employee = $employee->load([
            'user',
            'position',
            'team',
            'employeeContract',
            'profile',
        ]);

        // Default bulan berjalan
        $this->attendanceMonth = now()->format('Y-m');
    }

    #[On('change-team')]
    public function updateOnChangeTeam(): void
    {
        $this->employee->load([
            'user',
            'position',
            'team',
            'employeeContract',
            'profile',
        ]);
    }

    #[On('change-photo')]
    public function updateOnChangeProfile(): void
    {
        $this->employee->load([
            'user',
            'position',
            'team',
            'employeeContract',
            'profile',
        ]);
    }

    public function render()
    {
        $startOfMonth = Carbon::createFromFormat(
            'Y-m',
            $this->attendanceMonth
        )->startOfMonth();

        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Ambil attendance karyawan pada bulan yang dipilih
        |--------------------------------------------------------------------------
        */
        $attendanceModels = Attendances::query()
            ->where('employee_id', $this->employee->id)
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->date)->toDateString();
            });

        /*
        |--------------------------------------------------------------------------
        | Buat seluruh hari kerja pada bulan tersebut
        |--------------------------------------------------------------------------
        */
        $attendanceHistory = collect();

        foreach (
            CarbonPeriod::create(
                $startOfMonth,
                $endOfMonth
            ) as $date
        ) {
            // Minggu tidak dihitung sebagai hari kerja
            if ($date->dayOfWeekIso === 7) {
                continue;
            }

            $dateValue = $date->toDateString();

            $attendance = $attendanceModels->get($dateValue);

            $checkIn = $attendance?->check_in_at
                ? Carbon::parse($attendance->check_in_at)
                : null;

            $checkOut = $attendance?->check_out_at
                ? Carbon::parse($attendance->check_out_at)
                : null;

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            if (!$checkIn) {
                $status = 'absent';
                $statusLabel = 'Belum Hadir';
            } elseif ($checkIn->format('H:i:s') > '08:00:00') {
                $status = 'late';
                $statusLabel = 'Terlambat';
            } else {
                $status = 'present';
                $statusLabel = 'Hadir';
            }

            /*
            |--------------------------------------------------------------------------
            | Durasi kerja
            |--------------------------------------------------------------------------
            */
            $duration = null;

            if ($checkIn && $checkOut) {
                $diff = $checkIn->diff($checkOut);

                $duration = sprintf(
                    '%d jam %d menit',
                    ($diff->days * 24) + $diff->h,
                    $diff->i
                );
            }

            $attendanceHistory->push([
                'attendance_id' => $attendance?->id,
                'date' => $date->format('d M Y'),
                'date_value' => $dateValue,
                'check_in' => $checkIn?->format('H:i'),
                'check_out' => $checkOut?->format('H:i'),
                'duration' => $duration,
                'status' => $status,
                'status_label' => $statusLabel,
            ]);
        }

        return view(
            'livewire.page.main.employee.detail-employee',
            [
                'attendanceHistory' => $attendanceHistory,
            ]
        );
    }
}
