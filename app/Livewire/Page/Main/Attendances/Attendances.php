<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\AttedanceSetting;
use App\Models\Attendances as ModelsAttendances;
use App\Models\Holidays;
use App\Models\WorkTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.main", ['title' => "Halaman presensi"])]
class Attendances extends Component
{
    public string $day = "";
    public ?Holidays $holiday;
    public ?ModelsAttendances $att = null;

    public bool $isHoliday;

    public string $messageHoliday = "";
    public WorkTime $workTime;

    public AttedanceSetting $setting;
    public function mount()
    {
        $this->day = strtolower(now()->translatedFormat('l'));
        $this->workTime = WorkTime::where('day_of_week', "=", $this->day)->first();
        $this->holiday = Holidays::whereDate('date', '=', today())->first();
        $this->att = ModelsAttendances::where('employee_id', '=', Auth::user()->employees->id)->whereDate('date', '=', today())->first();
        $this->isHoliday = !$this->workTime->is_working_day || $this->holiday !== null;
        if (!$this->workTime->is_working_day) {
            $this->messageHoliday = 'Tidak ada jadwal kerja hari ini';
        } else if ($this->holiday !== null) {
            $this->messageHoliday = "Libur {$this->holiday->name}";
        }

        $this->setting = AttedanceSetting::first();
    }

    public function checkIn(float $latitude, float $longitude)
    {
        $this->authorize("checkIn", ModelsAttendances::class);

        if (!$latitude || !$longitude) {
            $this->dispatch('wirekit-toast', variant: "warning", title: "Ada sesuatu yang salah", message: "lokasi belum terdeteksi");
            return;
        }
        // validasi
        if ($this->isHoliday) {
            $this->dispatch('wirekit-toast', variant: "danger", title: "Ada sesuatu yang salah", message: "tidak ada jadwal kerja hari ini");
            return;
        }

        if ($this->att && $this?->att->check_in_at) {
            $this->dispatch('wirekit-toast', variant: "danger", title: "Ada sesuatu yang salah", message: "Anda sudah melakukan check in");
            return;
        }

        $startTime = today()->setTimeFromTimeString($this->workTime->start_time);

        $lateLimit = $startTime->copy()
            ->addMinutes($this->setting->late_tolerance_minutes);
        dd($lateLimit);
        $checkIn = now();

        $isLate = $checkIn->greaterThan($lateLimit);
        $lateMinutes = $isLate
            ? $startTime->diffInMinutes($checkIn)
            : 0;

        // tambah disini
        $user = Auth::user()->employees->attendances()->create([
            "date" => today(),
            "check_in_at" => $checkIn,
            "check_in_latitude" => $latitude,
            "check_in_longitude" => $longitude,
            "status" => $isLate ? "late" : "present",
            "late_minutes" => $lateMinutes,
        ]);

        if ($user->status === "late") {
            $this->dispatch('wirekit-toast', variant: "warning", title: "Berhasil presensi", message: "Terlambat presensi!");
            return;
        }
        $this->dispatch('wirekit-toast', variant: "success", title: "Berhasil presensi", message: "Terima kasih sudah presensi tepat waktu");
    }

    public function render()
    {
        return view('livewire.page.main.attendances.attendances');
    }
}
