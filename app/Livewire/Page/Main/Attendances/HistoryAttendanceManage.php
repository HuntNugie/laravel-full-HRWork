<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\Attendances;
use App\Models\Employees;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        /*
        |--------------------------------------------------------------------------
        | TANGGAL
        |--------------------------------------------------------------------------
        */

        $startDate = $this->startDate
            ? Carbon::parse($this->startDate)->startOfDay()
            : today()->startOfMonth()->startOfDay();

        $endDate = $this->endDate
            ? Carbon::parse($this->endDate)->endOfDay()
            : today()->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE
        |--------------------------------------------------------------------------
        |
        | Employee menjadi sumber utama.
        | Jadi employee yang tidak punya attendance tetap bisa ditampilkan.
        |
        */

        $employees = Employees::query()
            ->with([
                'user',
                'attendances' => function ($query) use ($startDate, $endDate) {
                    $query
                        ->whereDate('date', '>=', $startDate->toDateString())
                        ->whereDate('date', '<=', $endDate->toDateString());
                },
            ])

            /*
            |--------------------------------------------------------------------------
            | SEARCH
            |--------------------------------------------------------------------------
            */
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


        /*
        |--------------------------------------------------------------------------
        | PERIOD
        |--------------------------------------------------------------------------
        |
        | Hanya hari Senin-Sabtu yang dibuat sebagai hari kerja.
        | Minggu tidak dibuat menjadi "Belum Hadir".
        |
        */

        $period = CarbonPeriod::create(
            $startDate->copy()->startOfDay(),
            $endDate->copy()->startOfDay()
        );


        /*
        |--------------------------------------------------------------------------
        | PARSE DATA
        |--------------------------------------------------------------------------
        */

        $attendanceHistory = collect();


        foreach ($employees as $employee) {

            /*
            |--------------------------------------------------------------------------
            | Buat map attendance berdasarkan tanggal
            |--------------------------------------------------------------------------
            */

            $attendanceByDate = $employee->attendances
                ->keyBy(function ($attendance) {
                    return Carbon::parse($attendance->date)
                        ->format('Y-m-d');
                });


            foreach ($period as $date) {

                $date = Carbon::parse($date);


                /*
                |--------------------------------------------------------------------------
                | Skip Sunday
                |--------------------------------------------------------------------------
                */

                if ($date->dayOfWeekIso === 7) {
                    continue;
                }


                $dateKey = $date->format('Y-m-d');

                $attendance = $attendanceByDate->get($dateKey);


                /*
                |--------------------------------------------------------------------------
                | Check In
                |--------------------------------------------------------------------------
                */

                $checkIn = $attendance?->check_in_at
                    ? Carbon::parse($attendance->check_in_at)
                    : null;


                /*
                |--------------------------------------------------------------------------
                | Check Out
                |--------------------------------------------------------------------------
                */

                $checkOut = $attendance?->check_out_at
                    ? Carbon::parse($attendance->check_out_at)
                    : null;


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                if (!$attendance || !$checkIn) {

                    $attendanceStatus = 'absent';
                } elseif (
                    $checkIn->format('H:i:s') > '08:00:00'
                ) {

                    $attendanceStatus = 'late';
                } else {

                    $attendanceStatus = 'present';
                }


                /*
                |--------------------------------------------------------------------------
                | DURASI
                |--------------------------------------------------------------------------
                */

                $duration = '—';

                if ($checkIn && $checkOut) {

                    $diff = $checkIn->diff($checkOut);

                    $duration = sprintf(
                        '%dj %dm',
                        $diff->h,
                        $diff->i
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | STATUS LABEL
                |--------------------------------------------------------------------------
                */

                $statusLabel = match ($attendanceStatus) {

                    'present' => 'Hadir',

                    'late' => 'Terlambat',

                    'absent' => 'Belum Hadir',

                    default => '—',
                };


                /*
                |--------------------------------------------------------------------------
                | AVATAR
                |--------------------------------------------------------------------------
                */

                $avatar = $employee->user?->getFirstMediaUrl('avatar');


                /*
                |--------------------------------------------------------------------------
                | STORE
                |--------------------------------------------------------------------------
                */

                $attendanceHistory->push([
                    'attendance_id' => $attendance?->id,

                    'date' => $date->translatedFormat('d M Y'),

                    'date_value' => $date->format('Y-m-d'),

                    'employee_id' => $employee->id,

                    'employee_name' =>
                    $employee->user?->name ?? '—',

                    'employee_code' =>
                    $employee->employee_code ?? '—',

                    'avatar' => $avatar,

                    'check_in' =>
                    $checkIn?->format('H:i') ?? '—',

                    'check_out' =>
                    $checkOut?->format('H:i') ?? '—',

                    'duration' => $duration,

                    'status' => $attendanceStatus,

                    'status_label' => $statusLabel,
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($this->status !== '') {

            $attendanceHistory = $attendanceHistory
                ->filter(function ($attendance) {
                    return $attendance['status'] === $this->status;
                })
                ->values();
        }


        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        |
        | Tanggal terbaru di atas.
        | Kemudian nama employee.
        |
        */

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
