<?php

namespace App\Livewire\Page\Main\Time;

use App\Models\WorkTime;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Halaman waktu kerja'])]
class Time extends Component
{
    public bool $is_edit = false;

    public Collection $times;

    public array $updateWorkTime = [];


    public function toggleEdit()
    {
        $this->is_edit = !$this->is_edit;
    }


    public function mount()
    {
        $this->times = WorkTime::get()->mapWithKeys(function ($time) {
            return [
                $time->id => [
                    'day_of_week' => $time->day_of_week,
                    'start_time' => $time->start_time,
                    'end_time' => $time->end_time,
                    'is_working_day' => $time->is_working_day,
                ]
            ];
        });
        $this->updateWorkTime = $this->times->toArray();
    }

    public function submit(): void
    {
        $this->authorize('update', WorkTime::class);

        foreach ($this->updateWorkTime as $id => $workTime) {
            $isWorkingDay = (bool) ($workTime['is_working_day'] ?? false);

            if ($isWorkingDay && (
                blank($workTime['start_time'] ?? null)
                || blank($workTime['end_time'] ?? null)
            )) {
                $this->addError(
                    "updateWorkTime.$id.start_time",
                    'Jam mulai dan jam selesai wajib diisi untuk hari kerja.'
                );

                return;
            }

            $record = WorkTime::find($id);

            if (!$record) {
                continue;
            }

            $record->update([
                'start_time' => $workTime['start_time'] ?? null,
                'end_time' => $workTime['end_time'] ?? null,
                'is_working_day' => $isWorkingDay,
            ]);
        }

        $this->times = WorkTime::query()
            ->get()
            ->mapWithKeys(function ($time) {
                return [
                    $time->id => [
                        'day_of_week' => $time->day_of_week,
                        'start_time' => $time->start_time,
                        'end_time' => $time->end_time,
                        'is_working_day' => (bool) $time->is_working_day,
                    ],
                ];
            });

        $this->updateWorkTime = $this->times->toArray();
        $this->is_edit = false;
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Update jadwal',
            message: 'Jadwal berhasil di ubah.'
        );
    }
    public function render()
    {

        return view('livewire.page.main.time.time');
    }
}
