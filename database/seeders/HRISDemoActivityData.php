<?php

namespace Database\Seeders;

use App\Models\ContractLeaveEntitlements;
use App\Models\Attendances;
use App\Models\EmployeeAbsenceRequest;
use App\Models\EmployeeResignation;
use App\Models\EmployeeTermination;
use App\Models\EmployeeWarningLetter;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\PositionJobdesk;
use App\Models\User;
use App\Models\WorkTime;
use App\Service\AbsenceRequestService;
use App\Service\LeaveRequestService;
use App\Service\ResignationService;
use App\Service\TerminationService;
use App\Service\WarningLetterService;
use Carbon\Carbon;

/**
 * Interconnected development-only HRIS scenarios. Called by HRISDevelopmentSeeder
 * around historical attendance and payroll generation.
 */
class HRISDemoActivityData
{
    /** @param array<string, Employees> $employees @param array<string, User> $users */
    public function beforeAttendance(array $employees, array $users): void
    {
        $this->seedPositionJobdesks();
        $this->seedHolidays();
        $this->seedLeaveTypesAndEntitlements($employees);
        $this->seedLeaveRequests($employees, $users['hr']);
        $this->seedAbsenceRequests($employees, $users['hr']);
    }

    private function seedPositionJobdesks(): void
    {
        $descriptions = [
            'General Manager' => ['Menetapkan arah operasional dan sasaran perusahaan.', 'Mengevaluasi capaian operasional dan keuangan.'],
            'Manager' => ['Menyusun target kerja sesuai tanggung jawab jabatan.', 'Membina dan mengevaluasi kompetensi karyawan.'],
            'Supervisor' => ['Memantau kualitas pelaksanaan pekerjaan.', 'Memberikan arahan dan umpan balik kerja.'],
            'Software Engineer' => ['Membangun fitur aplikasi sesuai kebutuhan.', 'Menulis pengujian otomatis dan dokumentasi teknis.'],
            'Backend Developer' => ['Merancang REST API dan logika bisnis.', 'Mengoptimalkan query dan keamanan database.'],
            'Frontend Developer' => ['Mengembangkan antarmuka web responsif.', 'Mengintegrasikan komponen UI dengan API.'],
            'Mobile Developer' => ['Mengembangkan aplikasi Android dan integrasi API.', 'Menguji performa dan kompatibilitas perangkat.'],
            'UI/UX Designer' => ['Menyusun wireframe dan prototipe desain.', 'Melakukan evaluasi kemudahan penggunaan.'],
            'QA Engineer' => ['Menyusun skenario pengujian aplikasi.', 'Mendokumentasikan bug serta hasil pengujian.'],
            'DevOps Engineer' => ['Mengelola deployment dan pipeline CI/CD.', 'Memantau ketersediaan dan keamanan server.'],
            'IT Support Specialist' => ['Menangani dukungan perangkat dan aplikasi kantor.', 'Membuat catatan penanganan masalah pengguna.'],
            'Network Support Specialist' => ['Memelihara konektivitas jaringan internal.', 'Mendiagnosis gangguan jaringan dan keamanan akses.'],
            'Data Analyst' => ['Mengolah data operasional menjadi laporan.', 'Menjaga konsistensi dan kualitas dataset.'],
            'Product Specialist' => ['Menggali kebutuhan pengguna dan prioritas produk.', 'Mendokumentasikan perubahan kebutuhan sistem.'],
            'Project Coordinator' => ['Mengatur agenda dan komunikasi kegiatan.', 'Menyusun notulensi dan laporan koordinasi.'],
            'Finance Officer' => ['Menyiapkan administrasi pembayaran perusahaan.', 'Merekap dan merekonsiliasi transaksi keuangan.'],
            'Accounting Staff' => ['Mencatat transaksi dan menyusun jurnal.', 'Memeriksa kelengkapan dokumen pembukuan.'],
            'Administrator' => ['Mengelola akun dan hak akses aplikasi.', 'Menangani penggantian password dan status pengguna.'],
            'Administrative Officer' => ['Mengarsipkan dokumen administratif.', 'Memastikan ketepatan input data operasional.'],
            'HR Officer' => ['Mengelola data karyawan, kontrak, dan cuti.', 'Memproses administrasi absensi serta payroll.'],
            'Recruitment Staff' => ['Menyeleksi dokumen pelamar.', 'Mengatur komunikasi dan jadwal wawancara.'],
            'People Operations Staff' => ['Memelihara dokumen layanan karyawan.', 'Mendampingi proses onboarding dan offboarding.'],
            'Marketing Specialist' => ['Menyiapkan kampanye komunikasi produk.', 'Menganalisis hasil kegiatan pemasaran.'],
            'Content Specialist' => ['Menyusun konten dan dokumentasi publikasi.', 'Menjaga konsistensi bahasa dan materi komunikasi.'],
            'Security Analyst' => ['Meninjau risiko keamanan aplikasi.', 'Menganalisis insiden dan tindak lanjut mitigasi.'],
        ];

        foreach (Position::query()->get() as $position) {
            foreach ($descriptions[$position->name] ?? [] as $jobdesk) {
                PositionJobdesk::query()->firstOrCreate([
                    'position_id' => $position->id,
                    'jobdesk' => $jobdesk,
                ]);
            }
        }
    }

    private function seedHolidays(): void
    {
        $events = [
            ['name' => 'Libur Internal Perusahaan', 'date' => today()->subMonthsNoOverflow(2)->startOfMonth()->addDays(11), 'description' => 'Simulasi libur kegiatan internal HRWork.'],
            ['name' => 'Hari Kebersamaan Karyawan', 'date' => today()->subMonthNoOverflow()->startOfMonth()->addDays(18), 'description' => 'Simulasi hari kebersamaan perusahaan.'],
            ['name' => 'Libur Operasional Perusahaan', 'date' => today()->addMonthNoOverflow()->startOfMonth()->addDays(9), 'description' => 'Simulasi libur operasional yang dijadwalkan.'],
        ];

        foreach ($events as $event) {
            Holidays::query()->updateOrCreate(
                ['name' => $event['name']],
                ['date' => $event['date']->toDateString(), 'description' => $event['description']],
            );
        }
    }

    /** @param array<string, Employees> $employees */
    private function seedLeaveTypesAndEntitlements(array $employees): void
    {
        $definitions = [
            ['name' => 'Cuti Sakit', 'days' => 6, 'gender' => 'all', 'description' => 'Jatah simulasi cuti pemulihan kesehatan.'],
            ['name' => 'Cuti Penting', 'days' => 3, 'gender' => 'all', 'description' => 'Jatah simulasi keperluan keluarga mendesak.'],
            ['name' => 'Cuti Melahirkan', 'days' => 90, 'gender' => 'female', 'description' => 'Jatah simulasi cuti melahirkan.'],
            ['name' => 'Cuti Ayah', 'days' => 2, 'gender' => 'male', 'description' => 'Jatah simulasi mendampingi persalinan.'],
        ];

        foreach ($definitions as $definition) {
            $type = LeaveType::query()->updateOrCreate(
                ['name' => $definition['name']],
                [
                    'default_days' => $definition['days'],
                    'gender' => $definition['gender'],
                    'description' => $definition['description'],
                    'status' => 'active',
                ],
            );

            foreach ($employees as $employee) {
                if ($definition['gender'] !== 'all' && $employee->profile?->gender !== $definition['gender']) {
                    continue;
                }

                $contract = $employee->employeeContract()->where('status', 'active')->firstOrFail();

                ContractLeaveEntitlements::query()->updateOrCreate(
                    ['employee_contract_id' => $contract->id, 'leave_type_id' => $type->id],
                    ['days' => $definition['days']],
                );
            }
        }
    }

    /** @param array<string, Employees> $employees */
    private function seedLeaveRequests(array $employees, User $hr): void
    {
        $service = app(LeaveRequestService::class);
        $list = array_values($employees);
        $types = LeaveType::query()->whereIn('name', ['Cuti Tahunan', 'Cuti Sakit', 'Cuti Penting'])->get()->keyBy('name');
        $typeNames = ['Cuti Tahunan', 'Cuti Sakit', 'Cuti Penting'];
        $past = $this->workingDate(today()->subMonthNoOverflow()->startOfMonth()->addDays(5));
        $future = $this->workingDate(today()->addMonthNoOverflow()->startOfMonth()->addDays(5));
        $rejected = $this->workingDate(today()->subMonthsNoOverflow(2)->startOfMonth()->addDays(12));
        $cancelled = $this->workingDate(today()->addMonthNoOverflow()->startOfMonth()->addDays(15));

        foreach (array_slice($list, 0, 24) as $index => $employee) {
            $state = $index < 8 ? 'approved' : ($index < 16 ? 'pending' : ($index < 20 ? 'rejected' : 'cancelled'));
            $date = match ($state) {
                'approved' => $past,
                'pending' => $future,
                'rejected' => $rejected,
                default => $cancelled,
            };
            $leaveType = $types[$typeNames[$index % count($typeNames)]];

            if (LeaveRequest::query()->where('employee_id', $employee->id)
                ->where('leave_type_id', $leaveType->id)
                ->whereDate('start_date', $date->toDateString())->exists()) {
                continue;
            }

            $request = $service->createPending(
                $employee, $leaveType->id, $date->toDateString(), $date->toDateString(),
                'Keperluan pribadi dan keluarga (simulasi pengajuan cuti).',
            );

            match ($state) {
                'approved' => $service->approve($request, $hr->id),
                'rejected' => $service->reject($request, $hr->id, 'Jadwal belum memungkinkan untuk izin cuti tersebut.'),
                'cancelled' => $service->cancel($request),
                default => null,
            };

            $timestamps = match ($state) {
                'approved' => [
                    'created_at' => $date->copy()->subDays(10),
                    'approved_at' => $date->copy()->subDays(8),
                    'updated_at' => $date->copy()->subDays(8),
                ],
                'rejected' => [
                    'created_at' => $date->copy()->subDays(10),
                    'rejected_at' => $date->copy()->subDays(8),
                    'updated_at' => $date->copy()->subDays(8),
                ],
                'cancelled' => [
                    'created_at' => now()->subDays(5),
                    'cancelled_at' => now()->subDays(3),
                    'updated_at' => now()->subDays(3),
                ],
                default => ['created_at' => now()->subDay()],
            };

            $request->refresh()->update($timestamps);
        }
    }

    /** @param array<string, Employees> $employees */
    private function seedAbsenceRequests(array $employees, User $hr): void
    {
        $service = app(AbsenceRequestService::class);
        $list = array_values($employees);
        $past = $this->workingDate(today()->subMonthNoOverflow()->startOfMonth()->addDays(21));
        $future = $this->workingDate(today()->addDays(4));
        $rejected = $this->workingDate(today()->subMonthsNoOverflow(2)->startOfMonth()->addDays(20));

        foreach (array_slice($list, 24, 12) as $index => $employee) {
            $state = $index < 6 ? 'approved' : ($index < 9 ? 'pending' : 'rejected');
            $date = match ($state) {
                'approved' => $past,
                'pending' => $future,
                default => $rejected,
            };

            if (EmployeeAbsenceRequest::query()->where('employee_id', $employee->id)
                ->whereDate('date', $date->toDateString())->exists()) {
                continue;
            }

            $request = $service->create(
                $employee,
                $index % 2 === 0 ? 'sakit' : 'izin',
                $index % 2 === 0 ? 'Pemulihan kesehatan (data simulasi).' : 'Urusan keluarga (data simulasi).',
                $date,
            );

            if ($state === 'approved') {
                $service->approve($request, $hr->id);
            } elseif ($state === 'rejected') {
                $service->reject($request, $hr->id);
            }

            if ($state !== 'pending') {
                $request->refresh()->update([
                    'created_at' => $date->copy()->subDay(),
                    'approved_at' => $date->copy()->setTime(10, 0),
                    'updated_at' => $date->copy()->setTime(10, 0),
                ]);
            }
        }
    }

    /** @param array<string, Employees> $employees @param array<string, User> $users */
    public function afterPayroll(array $employees, array $users): void
    {
        $this->seedWarningLetters($employees, $users['hr']);
        $this->seedExitProcesses($employees, $users);
    }

    /** @param array<string, Employees> $employees */
    private function seedWarningLetters(array $employees, User $hr): void
    {
        $service = app(WarningLetterService::class);
        $candidates = array_values($employees);
        $levels = ['SP1', 'SP1', 'SP2', 'SP3'];

        foreach (array_slice($candidates, 4, 12) as $index => $employee) {
            if (EmployeeWarningLetter::query()->where('employee_id', $employee->id)->exists()) {
                continue;
            }

            $letter = $service->createDraft([
                'employee_id' => $employee->id,
                'warning_level' => $levels[$index % count($levels)],
                'issued_date' => today()->toDateString(),
                'reason' => $index % 2 === 0
                    ? 'Keterlambatan kehadiran yang berulang.'
                    : 'Pelanggaran tata tertib kerja yang telah diklarifikasi.',
                'description' => 'Dokumen simulasi untuk pengujian administrasi kedisiplinan.',
            ], $hr->id);

            if ($index % 4 === 0) {
                continue; // Leave a few drafts waiting for HRD.
            }

            $service->issue($letter, $hr->id);

            if ($index % 5 === 0) {
                $service->cancel($letter, 'Dibatalkan setelah klarifikasi ulang (data simulasi).', $hr->id);
            }
        }
    }

    /** @param array<string, Employees> $employees @param array<string, User> $users */
    private function seedExitProcesses(array $employees, array $users): void
    {
        $hr = $users['hr'];
        $resignation = app(ResignationService::class);
        $termination = app(TerminationService::class);

        $submittedEmployee = $employees['worker-backend-2'];
        if (! $submittedEmployee->resignations()->exists()) {
            $resignation->create(
                $submittedEmployee, $submittedEmployee->user,
                today()->addDays(30)->toDateString(),
                'Mengembangkan karier di bidang lain (data simulasi).',
            );
        }

        $approvedEmployee = $employees['worker-frontend-1'];
        if (! $approvedEmployee->resignations()->exists()) {
            $case = $resignation->create(
                $approvedEmployee, $approvedEmployee->user,
                today()->addDays(21)->toDateString(),
                'Melanjutkan pendidikan (data simulasi).',
            );
            $case = $resignation->approve($case, $hr, 'Pengajuan telah ditinjau HRD.');
            $resignation->createHandoverItem($case, 'Serah terima dokumen dan akses aplikasi', 'Menyiapkan dokumen operasional untuk penerus.');
        }

        $rejectedEmployee = $employees['worker-uiux-2'];
        if (! $rejectedEmployee->resignations()->exists()) {
            $case = $resignation->create(
                $rejectedEmployee, $rejectedEmployee->user,
                today()->addDays(35)->toDateString(),
                'Alasan pribadi (data simulasi).',
            );
            $resignation->reject($case, $hr, 'Perlu menyelesaikan komitmen administrasi terlebih dahulu.');
        }

        $inProgressEmployee = $employees['worker-finance-1'];
        if (! $inProgressEmployee->terminations()->exists()) {
            $case = $termination->create(
                $inProgressEmployee, $hr, today()->addDays(28)->toDateString(),
                'performance', 'Hasil evaluasi kinerja belum memenuhi standar (data simulasi).',
            );
            $termination->createHandoverItem($case, 'Serah terima dokumen administrasi', 'Dokumen kerja yang masih digunakan.');
        }

        $cancelledEmployee = $employees['worker-admin-1'];
        if (! $cancelledEmployee->terminations()->exists()) {
            $case = $termination->create(
                $cancelledEmployee, $hr, today()->addDays(25)->toDateString(),
                'efficiency', 'Evaluasi kebutuhan posisi (data simulasi).',
            );
            $termination->cancel($case, $hr, 'Keputusan dihentikan setelah evaluasi ulang.');
        }

        // Historical cases were finalized in the previous payroll month.
        // Store consistent snapshots without re-triggering real exit side effects.
        $lastWorkingDate = today()->startOfMonth()->subDay();
        $this->seedCompletedResignation($employees['worker-people-2'], $employees['hr'], $hr, $lastWorkingDate);
        $this->seedCompletedTermination($employees['worker-people-3'], $employees['hr'], $hr, $lastWorkingDate);
    }

    private function seedCompletedResignation(Employees $employee, Employees $recipient, User $hr, Carbon $date): void
    {
        $contract = $employee->employeeContract()->firstOrFail();
        $case = EmployeeResignation::query()->updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'employee_contract_id' => $contract->id,
                'submitted_by' => $employee->user_id,
                'submitted_at' => $date->copy()->subDays(35)->setTime(9, 0),
                'proposed_last_working_date' => $date->toDateString(),
                'approved_last_working_date' => $date->toDateString(),
                'reason' => 'Pindah domisili dan kesempatan kerja baru (data simulasi).',
                'status' => 'completed',
                'reviewed_by' => $hr->id,
                'reviewed_at' => $date->copy()->subDays(28)->setTime(10, 0),
                'completed_by' => $hr->id,
                'completed_at' => $date->copy()->setTime(17, 0),
                'exit_interview_by' => $hr->id,
                'exit_interview_at' => $date->copy()->subDays(2)->setTime(14, 0),
                'exit_interview_notes' => 'Karyawan menyelesaikan seluruh administrasi akhir.',
                'created_at' => $date->copy()->subDays(35),
                'updated_at' => $date,
            ],
        );

        $this->recordExitHistory($case, $hr, [
            ['to' => 'submitted', 'from' => null, 'note' => 'Pengajuan resign diterima.', 'at' => $date->copy()->subDays(35)],
            ['to' => 'approved', 'from' => 'submitted', 'note' => 'HRD menyetujui pengajuan.', 'at' => $date->copy()->subDays(28)],
            ['to' => 'completed', 'from' => 'approved', 'note' => 'Resignation selesai.', 'at' => $date],
        ]);

        foreach (['work', 'asset', 'finance', 'document', 'access'] as $category) {
            $case->clearances()->updateOrCreate(
                ['category' => $category],
                ['status' => 'completed', 'notes' => 'Clearance selesai (data simulasi).', 'verified_by' => $hr->id, 'verified_at' => $date],
            );
        }

        $case->handoverItems()->updateOrCreate(
            ['title' => 'Dokumentasi dan serah terima pekerjaan'],
            [
                'description' => 'Seluruh dokumen telah diteruskan kepada HRD.',
                'handover_to_employee_id' => $recipient->id,
                'status' => 'completed',
                'notes' => 'Serah terima diterima dengan lengkap.',
                'verified_by' => $hr->id,
                'verified_at' => $date,
            ],
        );

        $employee->update(['status_employee' => 'resign', 'ResignDate' => $date->toDateString()]);
        $employee->user?->update(['status' => 'inactive']);
        $contract->update(['status' => 'terminated']);
        $employee->statusHistory()->updateOrCreate(
            ['new_status' => 'resign', 'effective_date' => $date->toDateString()],
            ['old_status' => 'active', 'reason' => $case->reason],
        );

        Attendances::query()->where('employee_id', $employee->id)
            ->whereDate('date', '>', $date->toDateString())->delete();
    }

    private function seedCompletedTermination(Employees $employee, Employees $recipient, User $hr, Carbon $date): void
    {
        $contract = $employee->employeeContract()->firstOrFail();
        $case = EmployeeTermination::query()->updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'employee_contract_id' => $contract->id,
                'initiated_by' => $hr->id,
                'initiated_at' => $date->copy()->subDays(30)->setTime(9, 0),
                'effective_date' => $date->toDateString(),
                'reason_type' => 'efficiency',
                'reason' => 'Penyesuaian kebutuhan operasional (data simulasi).',
                'status' => 'completed',
                'completed_by' => $hr->id,
                'completed_at' => $date->copy()->setTime(17, 0),
                'created_at' => $date->copy()->subDays(30),
                'updated_at' => $date,
            ],
        );

        $this->recordExitHistory($case, $hr, [
            ['to' => 'in_progress', 'from' => null, 'note' => 'Proses PHK dimulai HRD.', 'at' => $date->copy()->subDays(30)],
            ['to' => 'completed', 'from' => 'in_progress', 'note' => 'Administrasi PHK selesai.', 'at' => $date],
        ]);

        foreach (['work', 'asset', 'finance', 'document', 'access'] as $category) {
            $case->clearances()->updateOrCreate(
                ['category' => $category],
                ['status' => 'completed', 'notes' => 'Clearance selesai (data simulasi).', 'verified_by' => $hr->id, 'verified_at' => $date],
            );
        }

        $case->handoverItems()->updateOrCreate(
            ['title' => 'Penyerahan dokumen dan inventaris'],
            [
                'description' => 'Serah terima inventaris dan administrasi kerja.',
                'handover_to_employee_id' => $recipient->id,
                'status' => 'completed',
                'notes' => 'Dokumen dan barang telah diterima.',
                'verified_by' => $hr->id,
                'verified_at' => $date,
            ],
        );

        $employee->update(['status_employee' => 'terminated', 'TerminationDate' => $date->toDateString()]);
        $employee->user?->update(['status' => 'inactive']);
        $contract->update(['status' => 'terminated']);
        $employee->statusHistory()->updateOrCreate(
            ['new_status' => 'terminated', 'effective_date' => $date->toDateString()],
            ['old_status' => 'active', 'reason' => $case->reason],
        );

        Attendances::query()->where('employee_id', $employee->id)
            ->whereDate('date', '>', $date->toDateString())->delete();
    }

    /**
     * @param EmployeeResignation|EmployeeTermination $case
     * @param array<int, array{to:string,from:?string,note:string,at:Carbon}> $history
     */
    private function recordExitHistory(EmployeeResignation|EmployeeTermination $case, User $actor, array $history): void
    {
        foreach ($history as $entry) {
            $case->histories()->updateOrCreate(
                ['to_status' => $entry['to']],
                [
                    'actor_id' => $actor->id,
                    'from_status' => $entry['from'],
                    'note' => $entry['note'],
                    'created_at' => $entry['at'],
                    'updated_at' => $entry['at'],
                ],
            );
        }
    }

    private function workingDate(Carbon $candidate): Carbon
    {
        $date = $candidate->copy()->startOfDay();
        $dayNames = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'];
        $workingDays = WorkTime::query()->where('is_working_day', true)->pluck('day_of_week')->all();

        for ($tries = 0; $tries < 14; $tries++) {
            if (in_array($dayNames[$date->dayOfWeekIso], $workingDays, true)
                && ! Holidays::query()->whereDate('date', $date->toDateString())->exists()) {
                return $date;
            }

            $date->addDay();
        }

        throw new \RuntimeException('Tidak ditemukan hari kerja untuk simulasi data cuti/izin.');
    }
}
