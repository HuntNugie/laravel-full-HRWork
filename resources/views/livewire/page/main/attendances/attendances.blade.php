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


                    <span
                        class="hidden rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-slate-500 shadow-sm sm:inline-flex">
                        Waktu Lokal
                    </span>

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
                                            09:00 - 17:00
                                        </p>
                                    </div>


                                    <div class="flex size-11 items-center justify-center rounded-xl bg-[#92EEFF]/60">

                                        <x-wirekit::icon name="clock" class="size-5 text-cyan-700" />

                                    </div>

                                </x-wirekit::row>


                                <x-wirekit::divider />


                                <x-wirekit::row justify="between" align="center">

                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Status
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            Anda belum melakukan presensi hari ini.
                                        </p>

                                    </div>


                                    <span
                                        class="shrink-0 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700">
                                        Belum Presensi
                                    </span>

                                </x-wirekit::row>

                            </x-wirekit::stack>

                        </div>


                        {{-- =================================
                            ACTION
                        ================================== --}}
                        <x-wirekit::stack gap="sm" align="center">

                            <x-wirekit::button x-data
                                @click="
        navigator.geolocation.getCurrentPosition(
            (position) => {
                console.log(position.coords.latitude);
                console.log(position.coords.longitude);
                console.log('Accuracy:', position.coords.accuracy, 'meters');
            },
            (error) => {
                console.error(error);
            }
        )
    ">
                                <x-wirekit::icon name="finger-print" />
                                Check In
                            </x-wirekit::button>


                            <x-wirekit::button variant="outline" size="md">

                                <x-wirekit::icon name="document-text" />

                                Sakit / Izin

                            </x-wirekit::button>


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
        INFORMASI HARI INI
    ====================================================== --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        {{-- JADWAL --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="xs">

                    <h2 class="text-base font-semibold text-slate-900">
                        Jadwal Hari Ini
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi jam kerja yang berlaku hari ini.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                        <x-wirekit::row justify="between" align="center">

                            <div>
                                <p class="text-sm font-medium text-slate-900">
                                    Senin
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Hari kerja
                                </p>
                            </div>

                            <p class="text-base font-semibold text-slate-900">
                                09:00 - 17:00
                            </p>

                        </x-wirekit::row>

                    </div>


                    <div class="rounded-xl border border-[#92EEFF]/50 bg-[#92EEFF]/20 px-4 py-3">

                        <p class="text-sm font-medium text-slate-900">
                            Waktu kerja hari ini adalah 8 jam.
                        </p>

                        <p class="mt-1 text-xs text-slate-600">
                            Pastikan melakukan check in dan check out sesuai jadwal.
                        </p>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- RINGKASAN --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="xs">

                    <h2 class="text-base font-semibold text-slate-900">
                        Ringkasan Bulan Ini
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan kehadiran Anda pada bulan berjalan.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="grid grid-cols-2 gap-3">

                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                        <p class="text-xs text-slate-500">
                            Hadir
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            20
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            hari
                        </p>

                    </div>


                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                        <p class="text-xs text-slate-500">
                            Terlambat
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            3
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            hari
                        </p>

                    </div>


                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                        <p class="text-xs text-slate-500">
                            Hari Libur
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            2
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            hari
                        </p>

                    </div>


                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                        <p class="text-xs text-slate-500">
                            Rata-rata
                        </p>

                        <p class="mt-2 text-2xl font-semibold text-slate-900">
                            8j
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            per hari
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =====================================================
        LOCATION INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex items-start gap-3">

                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-[#92EEFF]">

                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-cyan-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 21s7-6.1 7-12a7 7 0 10-14 0c0 5.9 7 12 7 12z" />

                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a2 2 0 100-4 2 2 0 000 4z" />

                    </svg>

                </div>


                <div>

                    <p class="text-sm font-medium text-slate-900">
                        Lokasi Presensi
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Lokasi perangkat akan dicatat ketika Anda melakukan
                        check in dan check out. Lokasi tidak digunakan untuk
                        membatasi tempat Anda melakukan presensi.
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
