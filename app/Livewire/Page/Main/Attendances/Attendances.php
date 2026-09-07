<?php

namespace App\Livewire\Page\Main\Attendances;

use App\Models\Attendances as ModelsAttendances;
use App\Models\Holidays;
use App\Models\WorkTime;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("layouts.main", ['title' => "Halaman presensi"])]
class Attendances extends Component
{
    public string $day = "";
    public ?Holidays $holiday;
    public ?ModelsAttendances $attendance;

    public bool $isHoliday;

    public string $messageHoliday = "";
    public WorkTime $workTime;
    public function mount()
    {
        $this->day = strtolower(now()->translatedFormat('l'));
        $this->workTime = WorkTime::where('day_of_week', "=", $this->day)->first();
        $this->holiday = Holidays::whereDate('date', '=', today())->first();
        $this->attendance = ModelsAttendances::where('employee_id', '=', Auth::user()->employees->id)->whereDate('date', '=', today())->first();
        $this->isHoliday = !$this->workTime->is_working_day || $this->holiday !== null;
        if (!$this->workTime->is_working_day) {
            $this->messageHoliday = 'Tidak ada jadwal kerja hari ini';
        } else if ($this->holiday !== null) {
            $this->messageHoliday = "Libur {$this->holiday->name}";
        }
    }

    public function checkIn(float $latitude, float $longitude)
    {
        $this->authorize("checkIn", ModelsAttendances::class);
        // validasi
        if ($this->isHoliday) {
            $this->dispatch('wirekit-toast', variant: "danger", title: "Ada sesuatu yang salah", message: "tidak ada jadwal kerja hari ini");
            return;
        }

        if ($this->attendance && $this->attendance->check_in_at) {
            $this->dispatch('wirekit-toast', variant: "danger", title: "Ada sesuatu yang salah", message: "Anda sudah melakukan check in");
            return;
        }

        // tambah disini

    }

    public function render()
    {
        return view('livewire.page.main.attendances.attendances');
    }
}
