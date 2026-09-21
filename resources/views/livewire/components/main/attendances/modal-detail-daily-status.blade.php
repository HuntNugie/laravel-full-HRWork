<x-wirekit::modal name="detail-daily-status">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Detail Daily Status
            </h2>

            <p class="text-sm text-slate-500">
                Ringkasan status kerja berdasarkan hasil Daily Status Service.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>

    <x-wirekit::modal.body>

        @if ($row)

            @php
                $state = $row['state'];

                $statusLabel = match ($state['status']) {
                    'present' => 'Hadir',
                    'late' => 'Terlambat',
                    'paid_leave' => 'Cuti',
                    'absence_sick' => 'Sakit',
                    'absence_permit' => 'Izin',
                    'pending' => 'Menunggu',
                    'unpresent' => 'Belum Hadir',
                    'holiday' => 'Libur',
                    'non_working' => 'Non-Hari Kerja',
                    'outside_contract' => 'Di Luar Kontrak',
                    default => '—',
                };

                $sourceLabel = match ($state['source']) {
                    'attendance' => 'Presensi',
                    'leave' => 'Cuti',
                    'absence' => 'Sakit/Izin',
                    'holiday' => 'Hari Libur',
                    'work_time' => 'Jadwal Kerja',
                    'contract' => 'Kontrak',
                    'calculation' => 'Perhitungan',
                    default => ucfirst($state['source']),
                };
            @endphp

            <div class="space-y-6">

                <div class="flex items-center gap-3">

                    <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-sky-100">

                        @if ($row['avatar'])
                            <img
                                src="{{ $row['avatar'] }}"
                                alt="{{ $row['employee_name'] }}"
                                class="size-full object-cover"
                            >
                        @else
                            <img
                                src="{{ asset('assets/nonProfile.jpg') }}"
                                alt=""
                                class="size-full object-cover"
                            >
                        @endif

                    </div>

                    <div>

                        <p class="text-base font-semibold text-slate-900">
                            {{ $row['employee_name'] }}
                        </p>

                        <p class="text-sm text-slate-500">
                            {{ $row['employee_code'] }}
                        </p>

                    </div>

                </div>

                <div class="grid gap-4 sm:grid-cols-2">

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Tanggal
                        </span>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($state['date'])->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Status
                        </span>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $statusLabel }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Sumber
                        </span>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $sourceLabel }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Source ID
                        </span>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $state['source_id'] ?? '—' }}
                        </p>
                    </div>

                </div>

                <div>

                    <h3 class="text-sm font-semibold text-slate-900">
                        Presensi
                    </h3>

                    <div class="mt-3 grid gap-4 sm:grid-cols-3">

                        <div>
                            <span class="text-xs text-slate-400">
                                Check In
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $row['check_in'] ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">
                                Check Out
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $row['check_out'] ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">
                                Keterlambatan
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $row['late_minutes'] > 0 ? $row['late_minutes'] . ' menit' : '—' }}
                            </p>
                        </div>

                    </div>

                </div>

                <div>

                    <h3 class="text-sm font-semibold text-slate-900">
                        Referensi Daily Status
                    </h3>

                    <div class="mt-3 grid gap-4 sm:grid-cols-2">

                        <div>
                            <span class="text-xs text-slate-400">
                                Contract ID
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $state['contract_id'] ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">
                                Work Time ID
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $state['work_time_id'] ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">
                                Attendance ID
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $state['attendance_id'] ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">
                                Leave Request ID
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $state['leave_request_id'] ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">
                                Absence Request ID
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $state['absence_request_id'] ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">
                                Holiday ID
                            </span>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $state['holiday_id'] ?? '—' }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        @endif

    </x-wirekit::modal.body>

</x-wirekit::modal>
