<x-wirekit::modal name="detail-attendance" class="w-[900px] max-w-[95vw]">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
    HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Detail Presensi
            </h2>

            <p class="text-sm text-slate-500">
                Informasi presensi dan lokasi karyawan.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    {{-- =====================================================
    BODY
    ====================================================== --}}
    <x-wirekit::modal.body>

        <div class="space-y-5">

            {{-- INFORMASI KARYAWAN --}}
            <div class="flex items-center justify-between gap-4">

                <div class="flex items-center gap-3">

                    <div class="size-12 shrink-0 overflow-hidden rounded-full bg-slate-100">

                        @if ($this->attendance->employees?->user?->getFirstMediaUrl('avatar'))
                            <img src="{{ $this->attendance->employees->user->getFirstMediaUrl('avatar') }}"
                                alt="{{ $this->attendance->employees->user->name }}" class="size-full object-cover">
                        @else
                            <div
                                class="flex size-full items-center justify-center text-sm font-semibold text-slate-500">
                                {{ substr($this->attendance->employees->user->name, 0, 1) }}
                            </div>
                        @endif

                    </div>

                    <div>
                        <div class="text-sm font-semibold text-slate-900">
                            {{ $this->attendance->employees->user->name }}
                        </div>

                        <div class="text-xs text-slate-500">
                            {{ $this->attendance->employees->employee_code }}
                        </div>

                        <div class="text-xs text-slate-500">
                            {{ Carbon\Carbon::parse($this->attendance->date)->translatedFormat('d F Y') }}
                        </div>
                    </div>

                </div>

                <x-wirekit::badge :variant="$this->status === 'late' ? 'warning' : 'success'">
                    {{ $this->statusLabel }}
                </x-wirekit::badge>

            </div>


            {{-- INFORMASI WAKTU --}}
            <div class="grid grid-cols-2 gap-3">

                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Check In
                    </div>

                    <div class="mt-1 text-lg font-semibold text-slate-900">
                        {{ $this->checkIn?->format('H:i') ?? '—' }}

                        @if ($this->checkIn)
                            <span class="text-xs font-normal text-slate-400">
                                WIB
                            </span>
                        @endif
                    </div>
                </div>


                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Check Out
                    </div>

                    <div class="mt-1 text-lg font-semibold text-slate-900">
                        {{ $this->checkOut?->format('H:i') ?? '—' }}

                        @if ($this->checkOut)
                            <span class="text-xs font-normal text-slate-400">
                                WIB
                            </span>
                        @endif
                    </div>
                </div>


                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Durasi Kerja
                    </div>

                    <div class="mt-1 text-lg font-semibold text-slate-900">
                        {{ $this->duration ?? '—' }}
                    </div>
                </div>


                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status
                    </div>

                    <div class="mt-1 text-sm font-semibold text-orange-500">
                        {{ $this->statusLabel }}
                    </div>
                </div>

            </div>


            {{-- LOKASI --}}
            <div>

                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-slate-900">
                        Lokasi Presensi
                    </h3>

                    <p class="text-xs text-slate-500">
                        Posisi karyawan saat melakukan presensi.
                    </p>
                </div>


                {{-- MAP FULL WIDTH --}}
                <div class="overflow-hidden rounded-xl border border-slate-200">

                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <x-wirekit::map :center="$this->center" :markers="$this->markers" provider="leaflet" :zoom="17"
                            :list="false" height="320px" aria-label="Lokasi presensi karyawan"
                            style-url="https://tile.openstreetmap.org/{z}/{x}/{y}.png"
                            attribution="© OpenStreetMap contributors" />
                    </div>

                </div>


                {{-- KOORDINAT --}}
                <div class="mt-3 grid grid-cols-2 gap-3">

                    <div class="rounded-xl border border-slate-200 p-3">

                        <div class="text-xs text-slate-400">
                            Lokasi Check In
                        </div>

                        <div class="mt-1 text-sm font-medium text-slate-700">
                            @if ($this->attendance->check_in_latitude !== null && $this->attendance->check_in_longitude !== null)
                                {{ $this->attendance->check_in_latitude }},
                                {{ $this->attendance->check_in_longitude }}
                            @else
                                Tidak tersedia
                            @endif
                        </div>

                    </div>


                    <div class="rounded-xl border border-slate-200 p-3">

                        <div class="text-xs text-slate-400">
                            Lokasi Check Out
                        </div>

                        <div class="mt-1 text-sm font-medium text-slate-700">
                            @if ($this->attendance->check_out_latitude !== null && $this->attendance->check_out_longitude !== null)
                                {{ $this->attendance->check_out_latitude }},
                                {{ $this->attendance->check_out_longitude }}
                            @else
                                Tidak tersedia
                            @endif
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </x-wirekit::modal.body>


    {{-- =====================================================
    FOOTER
    ====================================================== --}}
    <x-wirekit::modal.footer>

        <x-wirekit::row justify="end" gap="sm">

            <x-wirekit::modal.close>

                <x-wirekit::button type="button" variant="outline">
                    Tutup
                </x-wirekit::button>

            </x-wirekit::modal.close>

        </x-wirekit::row>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
