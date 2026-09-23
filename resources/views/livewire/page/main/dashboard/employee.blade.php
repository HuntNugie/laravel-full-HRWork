<x-wirekit::stack gap="lg">

    {{-- PAGE HEADER --}}
    <x-wirekit::stack gap="sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#30AFFF]">Dashboard Employee</p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                    {{ $greeting }}, {{ $employee->user->name }}
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan aktivitas dan informasi kepegawaian kamu.
                </p>
            </div>

            <x-wirekit::badge intent="neutral" leading-icon="calendar">
                {{ $today->format('d/m/Y') }}
            </x-wirekit::badge>
        </div>
    </x-wirekit::stack>

    {{-- SUMMARY --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Status Hari Ini</span>
                        <x-wirekit::icon name="check-circle" class="size-5 text-emerald-600" />
                    </div>

                    <div>
                        <x-wirekit::badge :intent="$todayStatusIntent" :dot="true">
                            {{ $todayStatusLabel }}
                        </x-wirekit::badge>

                        <p class="mt-2 text-xs text-slate-500">
                            Sumber:
                            {{ $todayStatus['source'] === 'calculation' ? 'perhitungan harian' : $todayStatus['source'] }}
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Hadir Bulan Ini</span>
                        <x-wirekit::icon name="calendar-check" class="size-5 text-emerald-600" />
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-slate-900">
                            {{ $attendanceSummary['present'] }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Termasuk {{ $attendanceSummary['late'] }} hari terlambat
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Sisa Cuti</span>
                        <x-wirekit::icon name="calendar" class="size-5 text-sky-600" />
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-slate-900">
                            {{ $leaveSummary['remaining'] }} hari
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Seluruh jenis cuti pada contract aktif
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Payroll Terakhir</span>
                        <x-wirekit::icon name="wallet" class="size-5 text-violet-600" />
                    </div>

                    @if ($latestPayroll)
                        <div>
                            <p class="text-xl font-semibold text-slate-900">
                                Rp{{ number_format((float) $latestPayroll->net_amount, 0, ',', '.') }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $latestPayroll->period?->name ?? 'Periode payroll' }}
                            </p>
                        </div>
                    @else
                        <div>
                            <p class="text-sm font-medium text-slate-700">Belum tersedia</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Belum ada payroll yang dapat dilihat.
                            </p>
                        </div>
                    @endif
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

    </div>

    {{-- TODAY + MONTHLY --}}
    <div class="grid gap-6 lg:grid-cols-2">

        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Presensi Hari Ini</h2>
                        <p class="text-sm text-slate-500">Status presensi berdasarkan Daily Status.</p>
                    </x-wirekit::stack>

                    @can('view-attendance')
                        <x-wirekit::button
                            href="{{ route('attendance.view') }}"
                            size="sm"
                            intent="neutral"
                            surface="outline"
                            wire:navigate
                        >
                            Buka Presensi
                        </x-wirekit::button>
                    @endcan
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="md">

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Status</p>

                            <div class="mt-2">
                                <x-wirekit::badge :intent="$todayStatusIntent" :dot="true">
                                    {{ $todayStatusLabel }}
                                </x-wirekit::badge>
                            </div>
                        </div>

                        @if ($todayStatus['is_late'])
                            <div class="text-right">
                                <p class="text-xs text-slate-400">Terlambat</p>
                                <p class="mt-1 text-sm font-semibold text-amber-600">
                                    {{ $todayStatus['late_minutes'] }} menit
                                </p>
                            </div>
                        @endif
                    </div>

                    <x-wirekit::divider />

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Check In</p>
                            <p class="mt-1 text-base font-semibold text-slate-900">
                                {{ $todayAttendance?->check_in_at?->format('H:i') ?? '—' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Check Out</p>
                            <p class="mt-1 text-base font-semibold text-slate-900">
                                {{ $todayAttendance?->check_out_at?->format('H:i') ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <x-wirekit::divider />

                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Jadwal</p>
                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $todayWorkTime?->start_time ? substr($todayWorkTime->start_time, 0, 5) : '—' }}
                                –
                                {{ $todayWorkTime?->end_time ? substr($todayWorkTime->end_time, 0, 5) : '—' }}
                            </p>
                        </div>

                        <p class="text-xs text-slate-500">
                            {{ $todayStatus['is_working_day'] ? 'Hari kerja' : 'Bukan hari kerja' }}
                        </p>
                    </div>

                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Kehadiran Bulan Ini</h2>
                    <p class="text-sm text-slate-500">Ringkasan status dari awal hingga akhir bulan.</p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="grid gap-4 sm:grid-cols-2">

                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs text-slate-400">Hadir</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $attendanceSummary['present'] }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs text-slate-400">Terlambat</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $attendanceSummary['late'] }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs text-slate-400">Cuti Dibayar</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $attendanceSummary['leave'] }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-4">
                        <p class="text-xs text-slate-400">Sakit / Izin</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $attendanceSummary['absence'] }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-4 sm:col-span-2">
                        <p class="text-xs text-slate-400">Tidak Hadir</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $attendanceSummary['unpresent'] }}
                        </p>
                    </div>

                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>

    </div>

    {{-- LEAVE + PAYROLL --}}
    <div class="grid gap-6 lg:grid-cols-2">

        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Cuti Saya</h2>
                        <p class="text-sm text-slate-500">Ringkasan quota dan pengajuan cuti.</p>
                    </x-wirekit::stack>

                    @can('view-leave')
                        <x-wirekit::button
                            href="{{ route('leave.view') }}"
                            size="sm"
                            intent="neutral"
                            surface="outline"
                            wire:navigate
                        >
                            Buka Cuti
                        </x-wirekit::button>
                    @endcan
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="md">

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <p class="text-xs text-slate-400">Quota</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $leaveSummary['quota'] }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Terpakai</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $leaveSummary['used'] }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">Pending</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900">{{ $leaveSummary['pending'] }}</p>
                        </div>
                    </div>

                    <x-wirekit::divider />

                    @if ($leaveBreakdown->isNotEmpty())
                        <x-wirekit::stack gap="sm">
                            @foreach ($leaveBreakdown->take(3) as $leave)
                                <div class="flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-slate-800">
                                            {{ $leave['name'] }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $leave['used'] }} digunakan dari {{ $leave['quota'] }} hari
                                        </p>
                                    </div>

                                    <x-wirekit::badge intent="success">
                                        {{ $leave['remaining'] }} tersisa
                                    </x-wirekit::badge>
                                </div>
                            @endforeach
                        </x-wirekit::stack>
                    @else
                        <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-medium text-slate-700">Belum ada jatah cuti</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Contract aktif belum memiliki entitlement cuti.
                            </p>
                        </div>
                    @endif

                    @if ($latestLeaveRequest)
                        <x-wirekit::divider />

                        <div class="flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-wide text-slate-400">Pengajuan terakhir</p>

                                <p class="mt-1 truncate text-sm font-medium text-slate-800">
                                    {{ $latestLeaveRequest->leaveType?->name ?? 'Pengajuan Cuti' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $latestLeaveRequest->start_date
                                        ? \Carbon\Carbon::parse($latestLeaveRequest->start_date)->format('d/m/Y')
                                        : '—' }}
                                    –
                                    {{ $latestLeaveRequest->end_date
                                        ? \Carbon\Carbon::parse($latestLeaveRequest->end_date)->format('d/m/Y')
                                        : '—' }}
                                </p>
                            </div>

                            @php
                                $leaveIntent = match ($latestLeaveRequest->status) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'rejected', 'cancelled' => 'danger',
                                    default => 'neutral',
                                };
                            @endphp

                            <x-wirekit::badge :intent="$leaveIntent">
                                {{ ucfirst($latestLeaveRequest->status) }}
                            </x-wirekit::badge>
                        </div>
                    @endif

                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Payroll Terbaru</h2>
                        <p class="text-sm text-slate-500">Slip gaji terakhir yang sudah diproses.</p>
                    </x-wirekit::stack>

                    @can('view-payroll-my')
                        <x-wirekit::button
                            href="{{ route('payroll.my.view') }}"
                            size="sm"
                            intent="neutral"
                            surface="outline"
                            wire:navigate
                        >
                            Lihat Slip Gaji
                        </x-wirekit::button>
                    @endcan
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                @if ($latestPayroll)
                    <x-wirekit::stack gap="md">

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                {{ $latestPayroll->period?->name ?? 'Periode Payroll' }}
                            </p>

                            <p class="mt-1 text-3xl font-semibold text-slate-900">
                                Rp{{ number_format((float) $latestPayroll->net_amount, 0, ',', '.') }}
                            </p>

                            <div class="mt-3">
                                <x-wirekit::badge intent="success" :dot="true">
                                    {{ ucfirst($latestPayroll->status) }}
                                </x-wirekit::badge>
                            </div>
                        </div>

                        <x-wirekit::divider />

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-400">Hari Dibayar</p>
                                <p class="mt-1 text-sm font-medium text-slate-800">
                                    {{ $latestPayroll->paid_days }} hari
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-slate-400">Potongan</p>
                                <p class="mt-1 text-sm font-medium text-slate-800">
                                    Rp{{ number_format((float) $latestPayroll->deduction_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        @can('show-payroll-my')
                            <x-wirekit::button
                                href="{{ route('payroll.my.show', $latestPayroll->id) }}"
                                size="sm"
                                intent="neutral"
                                surface="link"
                                class="px-0"
                                wire:navigate
                            >
                                Lihat detail payroll
                            </x-wirekit::button>
                        @endcan

                    </x-wirekit::stack>
                @else
                    <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-medium text-slate-700">Belum ada slip gaji</p>
                        <p class="mt-1 text-sm text-slate-500">
                            Payroll yang sudah diproses atau dibayar akan muncul di sini.
                        </p>
                    </div>
                @endif
            </x-wirekit::card.body>
        </x-wirekit::card>

    </div>

    {{-- EMPLOYMENT + ACTION --}}
    <div class="grid gap-6 lg:grid-cols-2">

        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Pekerjaan Saya</h2>
                        <p class="text-sm text-slate-500">Informasi posisi dan contract yang sedang digunakan.</p>
                    </x-wirekit::stack>

                    @can('view-contract-my')
                        <x-wirekit::button
                            href="{{ route('my-contract') }}"
                            size="sm"
                            intent="neutral"
                            surface="outline"
                            wire:navigate
                        >
                            Lihat Contract
                        </x-wirekit::button>
                    @endcan
                </div>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <div class="grid gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Nomor Karyawan</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $employee->employee_code }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Status</p>
                        <div class="mt-1">
                            <x-wirekit::badge
                                :intent="$employee->status_employee === 'active' ? 'success' : 'neutral'"
                            >
                                {{ $employee->status_employee ?? 'Belum diketahui' }}
                            </x-wirekit::badge>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Jabatan</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $employee->position?->name ?? $currentContract?->position_name ?? 'Belum diketahui' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Team</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $employee->team?->name ?? 'Belum diketahui' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Divisi</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $employee->team?->divisi?->name ?? 'Belum diketahui' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">Contract</p>
                        <p class="mt-1 text-sm font-medium text-slate-800">
                            @if ($currentContract)
                                {{ $currentContract->start_date?->format('d/m/Y') ?? '—' }}
                                –
                                {{ $currentContract->end_date?->format('d/m/Y') ?? 'Tetap' }}
                            @else
                                Belum ada contract aktif
                            @endif
                        </p>
                    </div>

                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>

        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Perlu Perhatian</h2>
                    <p class="text-sm text-slate-500">Pengajuan yang masih menunggu proses.</p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">

                    @if ($pendingLeaveCount > 0)
                        <div class="flex items-center justify-between gap-4 py-2">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-amber-50">
                                    <x-wirekit::icon name="calendar" class="size-5 text-amber-600" />
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ $pendingLeaveCount }} pengajuan cuti menunggu
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">Menunggu proses persetujuan.</p>
                                </div>
                            </div>

                            @can('view-leave')
                                <x-wirekit::button
                                    href="{{ route('leave.view') }}"
                                    size="sm"
                                    intent="neutral"
                                    surface="ghost"
                                    wire:navigate
                                >
                                    Buka
                                </x-wirekit::button>
                            @endcan
                        </div>
                    @endif

                    @if ($pendingAbsenceCount > 0)
                        <div class="flex items-center justify-between gap-4 py-2">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-50">
                                    <x-wirekit::icon name="document-text" class="size-5 text-sky-600" />
                                </div>

                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ $pendingAbsenceCount }} pengajuan sakit/izin menunggu
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">Menunggu proses persetujuan.</p>
                                </div>
                            </div>

                            @can('view-attendance')
                                <x-wirekit::button
                                    href="{{ route('attendance.view') }}"
                                    size="sm"
                                    intent="neutral"
                                    surface="ghost"
                                    wire:navigate
                                >
                                    Buka
                                </x-wirekit::button>
                            @endcan
                        </div>
                    @endif

                    @if ($pendingLeaveCount === 0 && $pendingAbsenceCount === 0)
                        <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-medium text-slate-700">Tidak ada tindakan yang tertunda</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Saat ini tidak ada pengajuan yang menunggu proses.
                            </p>
                        </div>
                    @endif

                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>

    </div>


    {{-- WARNING LETTER --}}
    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Surat Peringatan</h2>
                    <p class="text-sm text-slate-500">Ringkasan surat peringatan yang diterbitkan untuk kamu.</p>
                </x-wirekit::stack>

                @can('view-warning-letter-my')
                    <x-wirekit::button
                        href="{{ route('warning-letter.my.view') }}"
                        size="sm"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Lihat semua
                    </x-wirekit::button>
                @endcan
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            @if ($latestWarningLetter)
                <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_auto]">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-wirekit::badge intent="warning">
                                {{ $latestWarningLetter->warning_level }}
                            </x-wirekit::badge>

                            <span class="text-xs text-slate-400">
                                {{ $latestWarningLetter->issued_date?->translatedFormat('d F Y') ?? '—' }}
                            </span>
                        </div>

                        <p class="mt-3 text-sm font-semibold text-slate-900">
                            {{ $latestWarningLetter->letter_number ?? 'Nomor surat belum tersedia' }}
                        </p>

                        <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">
                            {{ $latestWarningLetter->reason }}
                        </p>

                        <p class="mt-3 text-xs text-slate-400">
                            Total surat diterbitkan: {{ $issuedWarningLettersCount }}
                        </p>
                    </div>

                    @can('show-warning-letter-my')
                        <div class="md:self-center">
                            <x-wirekit::button
                                href="{{ route('warning-letter.my.show', $latestWarningLetter) }}"
                                size="sm"
                                intent="primary"
                                wire:navigate
                            >
                                Lihat detail
                            </x-wirekit::button>
                        </div>
                    @endcan
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                            <x-wirekit::icon name="check-circle" class="size-5" />
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-800">Belum ada Surat Peringatan</p>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Tidak ada Surat Peringatan yang diterbitkan untuk akun kamu.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </x-wirekit::card.body>
    </x-wirekit::card>

    {{-- ROLE-SCOPED ATTENDANCE --}}

    @if ($employee->user->hasRole('manager'))
        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Presensi Divisi Hari Ini</h2>
                    <p class="text-sm text-slate-500">
                        Presensi employee di seluruh team dalam divisi yang kamu kelola.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                @if ($managerDivision)
                    <x-wirekit::stack gap="md">
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-lg border border-slate-200 p-4">
                                <p class="text-xs text-slate-400">Divisi</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $managerDivision->name }}
                                </p>
                            </div>
                            <div class="rounded-lg border border-slate-200 p-4">
                                <p class="text-xs text-slate-400">Team</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900">
                                    {{ $managerAttendanceSummary['teams'] }}
                                </p>
                            </div>
                            <div class="rounded-lg border border-slate-200 p-4">
                                <p class="text-xs text-slate-400">Employee</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900">
                                    {{ $managerAttendanceSummary['employees'] }}
                                </p>
                            </div>
                            <div class="rounded-lg border border-slate-200 p-4">
                                <p class="text-xs text-slate-400">Sudah Check In</p>
                                <p class="mt-1 text-2xl font-semibold text-emerald-600">
                                    {{ $managerAttendanceSummary['checked_in'] }}
                                </p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <x-wirekit::table hoverable>
                                <x-wirekit::table.head>
                                    <x-wirekit::table.row>
                                        <x-wirekit::table.th>Employee</x-wirekit::table.th>
                                        <x-wirekit::table.th>Position</x-wirekit::table.th>
                                        <x-wirekit::table.th>Team</x-wirekit::table.th>
                                        <x-wirekit::table.th>Status</x-wirekit::table.th>
                                        <x-wirekit::table.th>Check In</x-wirekit::table.th>
                                        <x-wirekit::table.th>Check Out</x-wirekit::table.th>
                                    </x-wirekit::table.row>
                                </x-wirekit::table.head>
                                <x-wirekit::table.body>
                                    @forelse ($managerAttendanceRows as $row)
                                        <x-wirekit::table.row>
                                            <x-wirekit::table.td>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800">
                                                        {{ $row['employee']->user?->name ?? '-' }}
                                                    </p>
                                                    <p class="text-xs text-slate-400">
                                                        {{ $row['employee']->employee_code }}
                                                    </p>
                                                </div>
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                {{ $row['employee']->position?->name ?? '-' }}
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                {{ $row['employee']->team?->name ?? '-' }}
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                <x-wirekit::badge
                                                    :intent="$row['status'] === 'late' ? 'warning' : ($row['attendance'] ? 'success' : 'neutral')"
                                                    :dot="true"
                                                >
                                                    {{ $row['status_label'] }}
                                                </x-wirekit::badge>
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                {{ $row['attendance']?->check_in_at?->format('H:i') ?? '—' }}
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                {{ $row['attendance']?->check_out_at?->format('H:i') ?? '—' }}
                                            </x-wirekit::table.td>
                                        </x-wirekit::table.row>
                                    @empty
                                        <x-wirekit::table.row>
                                            <x-wirekit::table.td colspan="6">
                                                <div class="py-8 text-center text-sm text-slate-500">
                                                    Belum ada employee dalam team pada divisi ini.
                                                </div>
                                            </x-wirekit::table.td>
                                        </x-wirekit::table.row>
                                    @endforelse
                                </x-wirekit::table.body>
                            </x-wirekit::table>
                        </div>
                    </x-wirekit::stack>
                @else
                    <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-medium text-slate-700">Belum ada divisi yang dikelola</p>
                        <p class="mt-1 text-sm text-slate-500">
                            Data presensi divisi akan muncul setelah kamu ditetapkan sebagai manager pada sebuah divisi.
                        </p>
                    </div>
                @endif
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif

    @if ($employee->user->hasRole('supervisor'))
        <x-wirekit::card>
            <x-wirekit::card.header>
                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">Presensi Team Hari Ini</h2>
                    <p class="text-sm text-slate-500">
                        Presensi anggota team yang kamu supervisi.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>
                @if ($supervisorTeam)
                    <x-wirekit::stack gap="md">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="rounded-lg border border-slate-200 p-4">
                                <p class="text-xs text-slate-400">Team</p>
                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $supervisorTeam->name }}
                                </p>
                            </div>
                            <div class="rounded-lg border border-slate-200 p-4">
                                <p class="text-xs text-slate-400">Anggota Team</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900">
                                    {{ $supervisorAttendanceSummary['employees'] }}
                                </p>
                            </div>
                            <div class="rounded-lg border border-slate-200 p-4">
                                <p class="text-xs text-slate-400">Sudah Check In</p>
                                <p class="mt-1 text-2xl font-semibold text-emerald-600">
                                    {{ $supervisorAttendanceSummary['checked_in'] }}
                                </p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <x-wirekit::table hoverable>
                                <x-wirekit::table.head>
                                    <x-wirekit::table.row>
                                        <x-wirekit::table.th>Employee</x-wirekit::table.th>
                                        <x-wirekit::table.th>Position</x-wirekit::table.th>
                                        <x-wirekit::table.th>Status</x-wirekit::table.th>
                                        <x-wirekit::table.th>Check In</x-wirekit::table.th>
                                        <x-wirekit::table.th>Check Out</x-wirekit::table.th>
                                    </x-wirekit::table.row>
                                </x-wirekit::table.head>
                                <x-wirekit::table.body>
                                    @forelse ($supervisorAttendanceRows as $row)
                                        <x-wirekit::table.row>
                                            <x-wirekit::table.td>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800">
                                                        {{ $row['employee']->user?->name ?? '-' }}
                                                    </p>
                                                    <p class="text-xs text-slate-400">
                                                        {{ $row['employee']->employee_code }}
                                                    </p>
                                                </div>
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                {{ $row['employee']->position?->name ?? '-' }}
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                <x-wirekit::badge
                                                    :intent="$row['status'] === 'late' ? 'warning' : ($row['attendance'] ? 'success' : 'neutral')"
                                                    :dot="true"
                                                >
                                                    {{ $row['status_label'] }}
                                                </x-wirekit::badge>
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                {{ $row['attendance']?->check_in_at?->format('H:i') ?? '—' }}
                                            </x-wirekit::table.td>
                                            <x-wirekit::table.td>
                                                {{ $row['attendance']?->check_out_at?->format('H:i') ?? '—' }}
                                            </x-wirekit::table.td>
                                        </x-wirekit::table.row>
                                    @empty
                                        <x-wirekit::table.row>
                                            <x-wirekit::table.td colspan="5">
                                                <div class="py-8 text-center text-sm text-slate-500">
                                                    Belum ada anggota team untuk ditampilkan.
                                                </div>
                                            </x-wirekit::table.td>
                                        </x-wirekit::table.row>
                                    @endforelse
                                </x-wirekit::table.body>
                            </x-wirekit::table>
                        </div>
                    </x-wirekit::stack>
                @else
                    <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-medium text-slate-700">Belum ada team yang kamu supervisi</p>
                        <p class="mt-1 text-sm text-slate-500">
                            Data presensi anggota team akan muncul setelah sebuah team menunjukmu sebagai supervisor.
                        </p>
                    </div>
                @endif
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif

    {{-- QUICK ACCESS --}}
    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Akses Cepat</h2>
                <p class="text-sm text-slate-500">Navigasi ke layanan karyawan yang sudah tersedia.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">

                @can('view-attendance')
                    <x-wirekit::button
                        href="{{ route('attendance.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Presensi
                    </x-wirekit::button>
                @endcan

                @can('view-leave')
                    <x-wirekit::button
                        href="{{ route('leave.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Pengajuan Cuti
                    </x-wirekit::button>
                @endcan

                @can('view-data-my')
                    <x-wirekit::button
                        href="{{ route('my-data') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Data Saya
                    </x-wirekit::button>
                @endcan

                @can('view-contract-my')
                    <x-wirekit::button
                        href="{{ route('my-contract') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Contract Saya
                    </x-wirekit::button>
                @endcan

                @can('view-payroll-my')
                    <x-wirekit::button
                        href="{{ route('payroll.my.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Slip Gaji
                    </x-wirekit::button>
                @endcan

            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
