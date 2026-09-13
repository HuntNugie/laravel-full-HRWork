<?php

namespace App\Livewire\Components\Main\Attendances;

use App\Models\Attendances;
use Carbon\Carbon;
use Livewire\Component;

class ModalDetailAttendance extends Component
{
    public int $attendanceId;

    public Attendances $attendance;

    public function mount(int $attendanceId): void
    {
        $this->attendanceId = $attendanceId;

        $this->attendance = Attendances::query()
            ->with([
                'employees.user',
            ])
            ->findOrFail($attendanceId);
    }

    public function getCheckInProperty(): ?Carbon
    {
        return $this->attendance->check_in_at
            ? Carbon::parse($this->attendance->check_in_at)
            : null;
    }

    public function getCheckOutProperty(): ?Carbon
    {
        return $this->attendance->check_out_at
            ? Carbon::parse($this->attendance->check_out_at)
            : null;
    }

    public function getDurationProperty(): ?string
    {
        if (!$this->checkIn || !$this->checkOut) {
            return null;
        }

        $diff = $this->checkIn->diff($this->checkOut);

        return sprintf(
            '%d jam %d menit',
            ($diff->days * 24) + $diff->h,
            $diff->i
        );
    }

    public function getStatusProperty(): string
    {
        if (!$this->checkIn) {
            return 'absent';
        }

        if ($this->checkIn->format('H:i:s') > '08:00:00') {
            return 'late';
        }

        return 'present';
    }

    public function getStatusLabelProperty(): string
    {
        return match ($this->status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Belum Hadir',
            default => '—',
        };
    }

    public function getMarkersProperty(): array
    {
        $markers = [];

        if (
            $this->attendance->check_in_latitude !== null &&
            $this->attendance->check_in_longitude !== null
        ) {
            $markers[] = [
                'id' => 'check-in',
                'lat' => (float) $this->attendance->check_in_latitude,
                'lng' => (float) $this->attendance->check_in_longitude,
                'label' => 'Check In',
                'body' => $this->checkIn?->format('H:i') . ' WIB',
                'intent' => 'success',
            ];
        }

        if (
            $this->attendance->check_out_latitude !== null &&
            $this->attendance->check_out_longitude !== null
        ) {
            $markers[] = [
                'id' => 'check-out',
                'lat' => (float) $this->attendance->check_out_latitude,
                'lng' => (float) $this->attendance->check_out_longitude,
                'label' => 'Check Out',
                'body' => $this->checkOut?->format('H:i') . ' WIB',
                'intent' => 'danger',
            ];
        }

        return $markers;
    }

    public function getCenterProperty(): array
    {
        if (
            $this->attendance->check_in_latitude !== null &&
            $this->attendance->check_in_longitude !== null
        ) {
            return [
                (float) $this->attendance->check_in_latitude,
                (float) $this->attendance->check_in_longitude,
            ];
        }

        if (
            $this->attendance->check_out_latitude !== null &&
            $this->attendance->check_out_longitude !== null
        ) {
            return [
                (float) $this->attendance->check_out_latitude,
                (float) $this->attendance->check_out_longitude,
            ];
        }

        return [
            -6.914864,
            107.608238,
        ];
    }

    public function render()
    {
        return view(
            'livewire.components.main.attendances.modal-detail-attendance'
        );
    }
}
