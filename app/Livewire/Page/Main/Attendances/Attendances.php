<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\AttedanceSetting;
use App\Models\Attendances as ModelsAttendances;
use App\Models\Holidays;
use App\Models\WorkTime;
use App\Service\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use LogicException;

#[Layout("layouts.main", ['title' => "Halaman presensi"])]
class Attendances extends Component
{
    public string $day = "";

    public ?Holidays $holiday;

    public ?ModelsAttendances $att = null;

    public bool $isHoliday = false;

    public string $messageHoliday = "";

    public ?WorkTime $workTime = null;

    public bool $isCanCheckIn = false;

    public bool $isAbsenceRequest = false;

    public ?AttedanceSetting $setting = null;

    public function mount(): void
    {
        $this->loadToday();
    }

    #[On("update-data")]
    public function refreshPage(): void
    {
        $this->loadToday();
    }

    public function checkIn(
        float $latitude,
        float $longitude,
        AttendanceService $attendanceService,
    ): void {
        $this->authorize("checkIn", ModelsAttendances::class);

        try {
            $attendanceService->checkIn(
                employee: Auth::user()->employees,
                latitude: $latitude,
                longitude: $longitude,
            );

            $this->dispatch(
                'wirekit-toast',
                variant: 'success',
                title: 'Berhasil presensi',
                message: 'Presensi masuk berhasil direkam.'
            );

            $this->refreshPage();
            $this->dispatch("update-data");
        } catch (LogicException $exception) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Presensi ditolak',
                message: $exception->getMessage()
            );
        }
    }

    public function checkOut(
        float $latitude,
        float $longitude,
        AttendanceService $attendanceService,
    ): void {
        $this->authorize("checkOut", ModelsAttendances::class);

        try {
            $attendanceService->checkOut(
                employee: Auth::user()->employees,
                latitude: $latitude,
                longitude: $longitude,
            );

            $this->dispatch(
                'wirekit-toast',
                variant: "success",
                title: "Berhasil rekam keluar presensi",
                message: "Terima kasih bekerja, selamat beristirahat."
            );

            $this->refreshPage();
            $this->dispatch("update-data");
        } catch (LogicException $exception) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Check Out ditolak',
                message: $exception->getMessage()
            );
        }
    }

    private function loadToday(): void
    {
        $employee = Auth::user()->employees;

        $this->day = strtolower(now()->translatedFormat('l'));

        $this->workTime = WorkTime::query()
            ->where('day_of_week', $this->day)
            ->first();

        $this->holiday = Holidays::query()
            ->whereDate('date', today())
            ->first();

        $this->att = $employee
            ->attendances()
            ->whereDate('date', today())
            ->first();

        $this->setting = AttedanceSetting::query()->first();

        $this->isHoliday = !$this->workTime?->is_working_day
            || $this->holiday !== null;

        $this->messageHoliday = match (true) {
            !$this->workTime => 'Jadwal kerja hari ini belum tersedia.',
            !$this->workTime->is_working_day => 'Tidak ada jadwal kerja hari ini.',
            $this->holiday !== null => "Libur {$this->holiday->name}",
            default => '',
        };

        $this->isAbsenceRequest = $employee
            ->employeeAbsenceRequest()
            ->whereDate('date', today())
            ->where('status', 'approved')
            ->exists();

        $this->isCanCheckIn = $this->workTime
            ? now()->format('H:i:s') > $this->workTime->end_time
            : true;
    }

    public function render()
    {
        return view('livewire.page.main.attendances.attendances');
    }
}
