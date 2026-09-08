<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                Riwayat Presensi
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Lihat riwayat presensi dan kehadiran Anda.
            </p>
        </div>
    </x-wirekit::stack>


    {{-- =====================================================
        FILTER
    ====================================================== --}}
    <x-wirekit::card>
        <x-wirekit::card.body>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                <x-wirekit::select label="Bulan" wire:model.live='month'>
                    <option value="">Semua bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktoberr</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </x-wirekit::select>

                <x-wirekit::select label="Tahun" wire:model.live='year'>
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                </x-wirekit::select>

                <x-wirekit::select label="Status" wire:model.live='status'>
                    <option value="all">Semua Status</option>
                    <option value="present">Hadir</option>
                    <option value="late">Terlambat</option>
                    <option value="absent">Tidak Hadir</option>
                    <option value="holiday">Libur</option>
                </x-wirekit::select>

            </div>

        </x-wirekit::card.body>
    </x-wirekit::card>


    {{-- =====================================================
        CALENDAR
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>
            <x-wirekit::stack gap="xs">
                <h2 class="text-base font-semibold text-slate-900">
                    Kalender Presensi
                </h2>

                <p class="text-sm text-slate-500">
                    Status kehadiran Anda berdasarkan tanggal.
                </p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <x-wirekit::event-calendar view="month" date="{{ $year }}-{{ $month }}-01" locale="id-ID"
                aria-label="Kalender riwayat presensi" :events="$this->getEventsProperty" />

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        STATUS LEGEND
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <x-wirekit::stack gap="sm">

                <p class="text-sm font-medium text-slate-900">
                    Keterangan Status
                </p>

                <div class="flex flex-wrap gap-x-5 gap-y-3">

                    <div class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-sm text-slate-600">
                            Hadir
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-amber-500"></span>
                        <span class="text-sm text-slate-600">
                            Terlambat
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-red-500"></span>
                        <span class="text-sm text-slate-600">
                            Tidak Hadir
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="size-2.5 rounded-full bg-slate-300"></span>
                        <span class="text-sm text-slate-600">
                            Libur
                        </span>
                    </div>

                </div>

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        MONTHLY SUMMARY
    ====================================================== --}}
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

            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <p class="text-xs text-slate-500">
                        Hadir
                    </p>

                    <p class="mt-2 text-2xl font-semibold text-slate-900">
                        {{ $this->monthSummary['present'] }}
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
                        {{ $this->monthSummary['late'] }}

                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        hari
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                    <p class="text-xs text-slate-500">
                        Tidak Hadir
                    </p>

                    <p class="mt-2 text-2xl font-semibold text-slate-900">
                        {{ $this->monthSummary['absent'] }}

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
                        @php
                            $duration = $this->monthSummary()['work_duration'];

                            $hours = intdiv($duration, 60);
                            $minutes = $duration % 60;
                        @endphp
                        {{ $hours }} jam {{ $minutes }} menit
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        per hari
                    </p>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
