<x-wirekit::stack gap="md">

    {{-- =====================================================
    HEADER
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Monitoring Presensi
            </h1>

            <p class="text-sm text-slate-500">
                Pantau kehadiran karyawan pada hari ini.
            </p>

        </x-wirekit::stack>

    </div>


    {{-- =====================================================
    DATE
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <p class="text-sm font-medium text-slate-900">
                        Hari Ini - toleransi {{ $attedanceSetting->late_tolerance_minutes }} menit
                    </p>

                    <p class="text-sm text-slate-500">
                        {{ today()->translatedFormat('l, d F Y') }}
                    </p>

                </x-wirekit::stack>


                <div class="flex gap-2">
                    <x-wirekit::button type="button" variant="outline" wire:click="$refresh"
                        class="border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        <x-wirekit::icon name="refresh" />
                        <span wire:loading.remove wire:target='$refresh'>
                            Refresh
                        </span>
                        <span wire:loading wire:target='$refresh'>
                            Tunggu sebentar
                        </span>
                    </x-wirekit::button>
                    <livewire:components.main.attendances.modal-setting :attedanceSetting="$attedanceSetting">
                        <x-wirekit::button type="button" variant="outline"
                            class="border-slate-200 bg-yellow-500 text-slate-700 hover:bg-slate-50">
                            <x-wirekit::icon name="gear" />
                            Setting
                        </x-wirekit::button>
                    </livewire:components.main.attendances.modal-setting>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
    SUMMARY
    ====================================================== --}}

    @php
        $totalPresent = $employees
            ->whereIn('monitoring_status', ['present', 'late', 'working'])
            ->count();

        $totalWorking = $employees
            ->where('monitoring_status', 'working')
            ->count();

        $totalLate = $employees
            ->where('daily_status', 'late')
            ->count();

        $totalAbsent = $employees
            ->where('daily_status', 'pending')
            ->count();
    @endphp


    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- HADIR --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Hadir
                        </span>

                        <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-50">

                            <x-wirekit::icon name="check" class="text-emerald-600" />

                        </div>

                    </div>

                    <p class="text-2xl font-bold text-slate-900">
                        {{ $totalPresent }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Employee sudah melakukan presensi
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- BEKERJA --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Sedang Bekerja
                        </span>

                        <div class="flex size-9 items-center justify-center rounded-lg bg-sky-50">

                            <x-wirekit::icon name="clock" class="text-sky-600" />

                        </div>

                    </div>

                    <p class="text-2xl font-bold text-slate-900">
                        {{ $totalWorking }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Sudah check in, belum check out
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- TERLAMBAT --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Terlambat
                        </span>

                        <div class="flex size-9 items-center justify-center rounded-lg bg-amber-50">

                            <x-wirekit::icon name="warning" class="text-amber-600" />

                        </div>

                    </div>

                    <p class="text-2xl font-bold text-slate-900">
                        {{ $totalLate }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Check in melewati jam kerja
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- BELUM HADIR --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Belum Hadir
                        </span>

                        <div class="flex size-9 items-center justify-center rounded-lg bg-red-50">

                            <x-wirekit::icon name="close" class="text-red-600" />

                        </div>

                    </div>

                    <p class="text-2xl font-bold text-slate-900">
                        {{ $totalAbsent }}
                    </p>

                    <p class="text-xs text-slate-500">
                        Belum melakukan presensi
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =====================================================
    ATTENDANCE LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Attendance List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar presensi karyawan hari ini.
                    </p>

                </x-wirekit::stack>


                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">

                    {{-- SEARCH --}}
                    <div class="w-full sm:w-64">

                        <x-wirekit::input placeholder="Cari nama employee" name="search" class="text-black"
                            wire:model.live.debounce.400ms='search' />

                    </div>


                    {{-- STATUS FILTER --}}
                    <div class="w-full sm:w-44">

                        <select wire:model.live="status"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5
        text-sm text-slate-700 outline-none
        focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">
                            <option value="">
                                Semua Status
                            </option>

                            <option value="working">
                                Sedang Bekerja
                            </option>

                            <option value="present">
                                Hadir
                            </option>

                            <option value="late">
                                Terlambat
                            </option>

                            <option value="absent">
                                Belum Hadir
                            </option>

                            <option value="paid_leave">
                                Cuti
                            </option>

                            <option value="absence_sick">
                                Sakit
                            </option>

                            <option value="absence_permit">
                                Izin
                            </option>

                            <option value="holiday">
                                Libur
                            </option>

                            <option value="non_working">
                                Non-Hari Kerja
                            </option>

                            <option value="outside_contract">
                                Di Luar Kontrak
                            </option>
                        </select>

                    </div>

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar max-h-[500px] overflow-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Employee
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Check In
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Check Out
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Durasi
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($employees as $employee)
                            @php
                                $attendance = $employee->attendances->first();
                            @endphp

                            <x-wirekit::table.row>

                                {{-- EMPLOYEE --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">

                                            @if ($employee->user->getFirstMediaUrl('avatar'))
                                                <img src="{{ $employee->user->getFirstMediaUrl('avatar') }}"
                                                    alt="{{ $employee->user->name }}"
                                                    class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                                            @else
                                                <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                                    class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                                            @endif

                                        </div>


                                        <x-wirekit::stack gap="1">

                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $employee->user->name }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $employee->employee_code }}
                                            </p>

                                        </x-wirekit::stack>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- CHECK IN --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">

                                        {{ $attendance?->check_in_at?->format('H:i') ?? '—' }}

                                    </span>

                                </x-wirekit::table.td>


                                {{-- CHECK OUT --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">

                                        {{ $attendance?->check_out_at?->format('H:i') ?? '—' }}

                                    </span>

                                </x-wirekit::table.td>


                                {{-- DURASI --}}
                                <x-wirekit::table.td>

                                    @if ($attendance?->check_in_at && $attendance?->check_out_at)
                                        @php
                                            $duration = $attendance->check_in_at->diff($attendance->check_out_at);
                                        @endphp

                                        <span class="text-sm text-slate-700">
                                            {{ $duration->h }}j {{ $duration->i }}m
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            —
                                        </span>
                                    @endif

                                </x-wirekit::table.td>


                                {{-- STATUS --}}
                                <x-wirekit::table.td>

                                    @switch($employee->monitoring_status)
                                        @case('working')
                                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-600">
                                                {{ $employee->monitoring_status_label }}
                                            </span>
                                        @break

                                        @case('present')
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">
                                                {{ $employee->monitoring_status_label }}
                                            </span>
                                        @break

                                        @case('late')
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-600">
                                                {{ $employee->monitoring_status_label }}
                                            </span>
                                        @break

                                        @case('paid_leave')
                                        @case('absence_sick')
                                        @case('absence_permit')
                                            <span class="inline-flex items-center rounded-full bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-600">
                                                {{ $employee->monitoring_status_label }}
                                            </span>
                                        @break

                                        @case('absent')
                                        @case('unpresent')
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-600">
                                                {{ $employee->monitoring_status_label }}
                                            </span>
                                        @break

                                        @case('holiday')
                                        @case('non_working')
                                        @case('outside_contract')
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                                                {{ $employee->monitoring_status_label }}
                                            </span>
                                        @break

                                        @default
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                                                {{ $employee->monitoring_status_label }}
                                            </span>
                                    @endswitch

                                </x-wirekit::table.td>


                                {{-- ACTIONS --}}
                                <x-wirekit::table.td>

                                    @can('show-attendance')
                                        @if ($attendance?->id)
                                            <livewire:components.main.attendances.modal-detail-attendance :attendance-id="$attendance->id"
                                                :key="'attendance-detail-' . $attendance->id">
                                                <x-wirekit::button type="button" variant="outline"
                                                    class="px-3 py-1.5 text-xs">
                                                    Detail
                                                </x-wirekit::button>
                                            </livewire:components.main.attendances.modal-detail-attendance>
                                        @else
                                            <span class="text-xs text-slate-400">
                                                —
                                            </span>
                                        @endif
                                    @endcan

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="6">

                                    <div class="flex flex-col items-center justify-center gap-2 py-10 text-center">

                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada data employee.
                                        </p>

                                        <p class="text-sm text-slate-500">
                                            Belum terdapat karyawan yang dapat ditampilkan.
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
