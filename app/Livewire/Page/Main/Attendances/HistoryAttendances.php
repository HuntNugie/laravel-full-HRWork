<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\Holidays;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman riwayat presensi'])]
class HistoryAttendances extends Component
{
    public string $month = "";
    public string $year = "";
    public string $status = "all";


    public function mount()
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    #[Computed]
    public function getEventsProperty(): array
    {
        return collect($this->getCalendarData())
            ->when(
                $this->status !== 'all',
                fn($collection) => $collection->where('status', $this->status)
            )
            ->map(function (array $item) {

                $title = match ($item['status']) {
                    'present' => 'Hadir',
                    'late' => 'Terlambat',
                    'absent' => 'Tidak Hadir',
                    'holiday' => 'Libur',
                    'absence' => match ($item['absence_type']) {
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                    },
                };

                $intent = match ($item['status']) {
                    'present' => 'success',
                    'late' => 'warning',
                    'absent' => 'danger',
                    'holiday' => 'neutral',
                    'absence' => 'accent',
                };

                return [
                    'id' => $item['attendance_id']
                        ?? $item['absence_id']
                        ?? $item['date'],

                    'title' => $title,

                    'start' => $item['date'],

                    'allDay' => true,

                    'intent' => $intent,
                ];
            })
            ->all();
    }

    protected function getData()
    {
        return Auth::user()
            ->employees
            ->attendances()
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get()
            ->map(function ($att) {
                return [
                    'date' => $att->date,
                    'status' => $att->status,
                    'check_in_at' => $att->check_in_at,
                    'check_out_at' => $att->check_out_at,
                    'work_duration' => $att->work_duration,
                    'attendance_id' => $att->id,
                ];
            })
            ->toArray();
    }

    protected function getCalendarData()
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $employee = Auth::user()->employees;

        $attendances = $employee
            ->attendances()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($attendance) => $attendance->date->format('Y-m-d'));

        $holidays = Holidays::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn($holiday) => $holiday->date->format('Y-m-d'));

        $absenceRequests = EmployeeAbsenceRequest::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->keyBy(fn($request) => $request->date->format('Y-m-d'));

        $calendar = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

            $dateKey = $date->format('Y-m-d');

            $attendance = $attendances->get($dateKey);
            $holiday = $holidays->get($dateKey);
            $absence = $absenceRequests->get($dateKey);

            // Hari libur
            if ($holiday) {
                $calendar[] = [
                    'date' => $dateKey,
                    'status' => 'holiday',
                ];

                continue;
            }

            // Tidak ada jadwal kerja
            $day = strtolower($date->translatedFormat('l'));

            $workTime = WorkTime::where('day_of_week', $day)->first();

            if (!$workTime || !$workTime->is_working_day) {
                $calendar[] = [
                    'date' => $dateKey,
                    'status' => 'holiday',
                ];

                continue;
            }

            // Ada attendance
            if ($attendance) {
                $calendar[] = [
                    'date' => $dateKey,
                    'status' => $attendance->status,
                    'attendance_id' => $attendance->id,
                    'check_in_at' => $attendance->check_in_at,
                    'check_out_at' => $attendance->check_out_at,
                    'work_duration' => $attendance->work_duration,
                ];

                continue;
            }

            // Tidak ada attendance
            if ($absence) {
                $calendar[] = [
                    'date' => $dateKey,
                    'status' => 'absence',
                    'absence_id' => $absence->id,
                    'absence_type' => $absence->type,
                    'absence_status' => $absence->status,
                ];

                continue;
            }

            // Hari kerja tetapi tidak ada attendance

            if ($date->isPast()) {
                $calendar[] = [
                    'date' => $dateKey,
                    'status' => 'absent',
                ];
            }
        }

        return $calendar;
    }

    #[Computed]
    public function monthSummary(): array
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        if ($start->isSameMonth(today())) {
            $end = today();
        }

        $employee = Auth::user()->employees;

        $attendances = $employee
            ->attendances()
            ->whereBetween('date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->get()
            ->keyBy(fn($attendance) => $attendance->date->format('Y-m-d'));

        $absenceRequests = EmployeeAbsenceRequest::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->where('status', 'approved')
            ->get()
            ->keyBy(fn($absence) => $absence->date->format('Y-m-d'));

        $workTimes = WorkTime::query()
            ->get()
            ->keyBy('day_of_week');

        $present = 0;
        $late = 0;
        $absent = 0;

        $workDurations = $attendances
            ->whereNotNull('work_duration');

        foreach ($attendances as $attendance) {
            if ($attendance->status === 'present') {
                $present++;
            }

            if ($attendance->status === 'late') {
                $late++;
            }
        }

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

            $dateKey = $date->format('Y-m-d');

            $attendance = $attendances->get($dateKey);
            $absence = $absenceRequests->get($dateKey);

            // Kalau sudah ada attendance, bukan tidak hadir.
            if ($attendance) {
                continue;
            }

            // Kalau ada izin/sakit yang sudah disetujui,
            // bukan tidak hadir.
            if ($absence) {
                continue;
            }

            $day = strtolower($date->translatedFormat('l'));
            $workTime = $workTimes->get($day);

            // Bukan hari kerja.
            if (!$workTime || !$workTime->is_working_day) {
                continue;
            }

            // Hari kerja yang sudah berlalu tanpa attendance
            // dan tanpa izin/sakit.
            $absent++;
        }

        $totalWorkDuration = $workDurations->sum('work_duration');

        $averageWorkDuration = $workDurations->isNotEmpty()
            ? intdiv($totalWorkDuration, $workDurations->count())
            : 0;

        return [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'work_duration' => $averageWorkDuration,
        ];
    }

    public function render()
    {
        return view('livewire.page.main.attendances.history-attendances');
    }
}
