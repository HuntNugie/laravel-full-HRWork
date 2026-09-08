<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                Presensi
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Catat kehadiran Anda untuk hari ini.
            </p>
        </div>

    </x-wirekit::stack>


    {{-- =====================================================
        PRESENSI HARI INI
    ====================================================== --}}
    <x-wirekit::card>

        <div class="overflow-hidden rounded-xl bg-gradient-to-br from-[#E8FAFF] via-white to-[#F1FFF4]">

            {{-- HEADER --}}
            <div class="px-6 py-5">

                <x-wirekit::row justify="between" align="start" gap="md">

                    <x-wirekit::stack gap="xs">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Presensi Hari Ini
                        </h2>

                        <p class="text-sm text-slate-500">
                            Lakukan check in dan check out sesuai jadwal kerja Anda.
                        </p>

                    </x-wirekit::stack>


                    <x-wirekit::button intent="primary" href="{{ route('history.view') }}" wire:navigate>
                        Riwayat Presensi
                    </x-wirekit::button>

                </x-wirekit::row>

            </div>


            {{-- BODY --}}
            <div class="border-t border-white/70 px-6 py-8">

                <div x-data="{
                    time: '',
                    date: '',
                
                    updateClock() {
                        const now = new Date();
                
                        this.time = now.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false,
                        });
                
                        this.date = now.toLocaleDateString('id-ID', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                        });
                    },
                
                    init() {
                        this.updateClock();
                
                        setInterval(() => {
                            this.updateClock();
                        }, 1000);
                    }
                }" x-init="init()">

                    <x-wirekit::stack gap="lg" align="center">

                        {{-- =================================
                            REALTIME CLOCK
                        ================================== --}}
                        <x-wirekit::stack gap="sm" align="center">

                            <div class="flex items-end justify-center gap-1">

                                <span x-text="time"
                                    class="text-6xl font-semibold tracking-[-0.05em] text-slate-900 tabular-nums sm:text-7xl">
                                    00:00
                                </span>

                                <span x-text="time.slice(-2)"
                                    class="mb-2 text-xl font-medium tabular-nums text-slate-400 sm:text-2xl">
                                    00
                                </span>

                            </div>


                            <p x-text="date" class="text-sm font-medium capitalize text-slate-500">
                                Memuat tanggal...
                            </p>

                        </x-wirekit::stack>


                        {{-- =================================
                            TODAY STATUS
                        ================================== --}}
                        <div
                            class="w-full max-w-lg rounded-2xl border border-white/80 bg-white/70 p-5 shadow-sm backdrop-blur-sm">

                            <x-wirekit::stack gap="sm">

                                <x-wirekit::row justify="between" align="center" gap="md">

                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Jadwal Kerja
                                        </p>

                                        <p class="mt-1 text-base font-semibold text-slate-900">
                                            {{ \Carbon\Carbon::parse($workTime->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($workTime->end_time)->format('H:i') }}
                                        </p>
                                    </div>


                                    <div class="flex size-11 items-center justify-center rounded-xl bg-[#92EEFF]/60">

                                        <x-wirekit::icon name="clock" class="size-5 text-cyan-700" />

                                    </div>

                                </x-wirekit::row>


                                <x-wirekit::divider />
                                <x-wirekit::row justify="between" align="center" gap="md">

                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Status pekerjaan hari ini
                                        </p>

                                        <p class="mt-1 text-base font-semibold text-slate-900">
                                            {{ $isHoliday ? $messageHoliday : 'Ada jadwal kerja' }}
                                        </p>
                                    </div>


                                    <div class="flex size-11 items-center justify-center rounded-xl bg-[#92EEFF]/60">

                                        <x-wirekit::icon name="calendar" class="size-5 text-cyan-700" />

                                    </div>

                                </x-wirekit::row>


                                <x-wirekit::divider />


                                @if (!$isHoliday)
                                    <x-wirekit::row justify="between" align="center">

                                        <div>

                                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                                Status
                                            </p>

                                            <p class="mt-1 text-sm font-medium text-slate-700">
                                                {{ $att ? 'Sudah melakukan presensi' : 'Belum melakukan presensi' }}
                                            </p>

                                        </div>


                                        <span @class([
                                            'shrink-0 rounded-full px-3 py-1.5 text-xs font-medium',
                                            'bg-amber-50 text-amber-700' => !$att || !$att?->check_in_at,
                                            'bg-emerald-50 text-emerald-700' => $att && !$att?->check_out_at,
                                        ])>
                                            {{ !$att || !$att?->check_in_at ? 'Belum presensi' : 'Sudah presensi' }}
                                        </span>

                                    </x-wirekit::row>
                                @endif

                            </x-wirekit::stack>

                        </div>


                        {{-- =================================
                            ACTION
                        ================================== --}}
                        <x-wirekit::stack gap="sm" align="center">

                            @if (!$att || !$att?->check_in_at)
                                <x-wirekit::button x-data="{ loading: false }" :disabled="$isAbsenceRequest || $isCanCheckIn"
                                    @click="
        loading = true;


        navigator.geolocation.getCurrentPosition(
            (position) => {
                $wire.checkIn(
                    position.coords.latitude,
                    position.coords.longitude,
                ).finally(() => {
                    loading = false;
                });
            },
            (error) => {
                loading = false;
                console.error(error);
            },
            {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 15000,
            }
        );
    ">
                                    <span x-show="!loading" class="flex items-center gap-2">
                                        <x-wirekit::icon name="finger-print" />
                                        Rekam masuk
                                    </span>

                                    <span x-show="loading" class="flex items-center gap-2">
                                        <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" />

                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                        </svg>

                                        Memproses...
                                    </span>
                                </x-wirekit::button>
                            @endif

                            @if ($att && !$att->check_out_at)
                                <x-wirekit::button x-data="{ loading: false }" x-bind:disabled="loading"
                                    @click="
        loading = true;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                $wire.checkOut(
                    position.coords.latitude,
                    position.coords.longitude,
                ).finally(() => {
                    loading = false;
                });
            },
            (error) => {
                loading = false;
                console.error(error);
            },
            {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 15000,
            }
        );
    ">
                                    <span x-show="!loading" class="flex items-center gap-2">
                                        <x-wirekit::icon name="finger-print" />
                                        Rekam keluar
                                    </span>

                                    <span x-show="loading" class="flex items-center gap-2">
                                        <svg class="size-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" />

                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                        </svg>

                                        Memproses...
                                    </span>
                                </x-wirekit::button>
                            @endif

                            @if ($isAbsenceRequest || $att?->check_in_at)
                                <x-wirekit::button variant="outline" size="md" :disabled="$isHoliday || $att?->check_in_at || $isAbsenceRequest">

                                    <x-wirekit::icon name="document-text" />

                                    Sakit / Izin

                                </x-wirekit::button>
                            @else
                                <livewire:components.main.attendances.modal-izin>
                                    <x-wirekit::button variant="outline" size="md" :disabled="$isHoliday || $att?->check_in_at || $isAbsenceRequest">

                                        <x-wirekit::icon name="document-text" />

                                        Sakit / Izin

                                    </x-wirekit::button>
                                </livewire:components.main.attendances.modal-izin>
                            @endif


                            <p class="text-xs text-slate-500">
                                Lokasi perangkat akan dicatat saat Anda melakukan presensi.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::stack>

                </div>

            </div>

        </div>

    </x-wirekit::card>


    {{-- =====================================================
    INFORMASI PRESENSI HARI INI
====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>
            <x-wirekit::stack gap="xs">
                <h2 class="text-base font-semibold text-slate-900">
                    Informasi Presensi Hari Ini
                </h2>

                <p class="text-sm text-slate-500">
                    Detail rekaman presensi Anda hari ini.
                </p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- REKAM MASUK --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Rekam Masuk
                    </p>

                    <p class="mt-2 text-xl font-semibold text-slate-900">
                        {{ $att?->check_in_at?->format('H:i') ?? '-' }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        {{ $att?->check_in_at?->format('d M Y') ?? 'Belum direkam' }}
                    </p>
                </div>

                {{-- REKAM KELUAR --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Rekam Keluar
                    </p>

                    <p class="mt-2 text-xl font-semibold text-slate-900">
                        {{ $att?->check_out_at?->format('H:i') ?? '-' }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        {{ $att?->check_out_at?->format('d M Y') ?? 'Belum direkam' }}
                    </p>
                </div>

                {{-- STATUS PRESENSI --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status Presensi
                    </p>

                    <div class="mt-2">
                        <span @class([
                            'inline-flex rounded-full px-3 py-1.5 text-xs font-medium',
                            'bg-amber-50 text-amber-700' => !$att?->check_in_at,
                            'bg-cyan-50 text-cyan-700' => $att?->check_in_at && !$att?->check_out_at,
                            'bg-emerald-50 text-emerald-700' =>
                                $att?->check_in_at && $att?->check_out_at,
                        ])>
                            @if (!$att?->check_in_at)
                                Belum Presensi
                            @elseif (!$att?->check_out_at)
                                Sedang Bekerja
                            @else
                                Selesai
                            @endif
                        </span>
                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Status presensi hari ini
                    </p>
                </div>

                {{-- WAKTU KERJA --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Waktu Kerja
                    </p>

                    <p class="mt-2 text-xl font-semibold text-slate-900">
                        @if ($att?->check_in_at && $att?->check_out_at)
                            {{ intdiv($att->work_duration, 60) }}j
                            {{ $att->work_duration % 60 }}m
                        @else
                            -
                        @endif
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Durasi kerja hari ini
                    </p>
                </div>

            </div>
        </x-wirekit::card.body>

    </x-wirekit::card>


</div>
