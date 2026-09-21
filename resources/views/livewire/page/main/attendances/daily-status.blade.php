<x-wirekit::stack gap="md">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <div class="flex items-center gap-2">
                <span class="text-sm font-medium text-[#30AFFF]">
                    Presensi
                </span>

                <span class="text-sm text-slate-400">
                    / Daily Status
                </span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Daily Status
            </h1>

            <p class="text-sm text-slate-500">
                Lihat status kerja karyawan berdasarkan kontrak, jadwal, presensi, cuti, izin, dan hari libur.
            </p>

        </x-wirekit::stack>

        <div class="flex items-center gap-2">

            <x-wirekit::button
                type="button"
                variant="outline"
                wire:click="resetFilter"
                class="border-slate-200 bg-white text-slate-700 hover:bg-slate-50"
            >
                <x-wirekit::icon name="refresh" />
                Reset
            </x-wirekit::button>

        </div>

    </div>


    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">

        @php
            $cards = [
                ['key' => 'present', 'title' => 'Hadir', 'description' => 'Status hadir', 'icon' => 'check', 'class' => 'text-emerald-600 bg-emerald-50'],
                ['key' => 'late', 'title' => 'Terlambat', 'description' => 'Masuk melewati batas', 'icon' => 'warning', 'class' => 'text-amber-600 bg-amber-50'],
                ['key' => 'paid_leave', 'title' => 'Cuti', 'description' => 'Cuti disetujui', 'icon' => 'calendar', 'class' => 'text-sky-600 bg-sky-50'],
                ['key' => 'absence', 'title' => 'Sakit / Izin', 'description' => 'Ketidakhadiran resmi', 'icon' => 'file-text', 'class' => 'text-violet-600 bg-violet-50'],
                ['key' => 'unpresent', 'title' => 'Belum Hadir', 'description' => 'Hari kerja tanpa presensi', 'icon' => 'close', 'class' => 'text-rose-600 bg-rose-50'],
                ['key' => 'pending', 'title' => 'Menunggu', 'description' => 'Hari kerja belum selesai', 'icon' => 'clock', 'class' => 'text-slate-600 bg-slate-100'],
            ];
        @endphp

        @foreach ($cards as $card)

            <x-wirekit::card>

                <x-wirekit::card.body>

                    <x-wirekit::stack gap="sm">

                        <div class="flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-500">
                                {{ $card['title'] }}
                            </span>

                            <div class="flex size-9 items-center justify-center rounded-lg {{ $card['class'] }}">
                                <x-wirekit::icon name="{{ $card['icon'] }}" />
                            </div>

                        </div>

                        <p class="text-2xl font-bold text-slate-900">
                            {{ $summary[$card['key']] }}
                        </p>

                        <p class="text-xs text-slate-500">
                            {{ $card['description'] }}
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.body>

            </x-wirekit::card>

        @endforeach

    </div>


    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Filter Daily Status
                </h2>

                <p class="text-sm text-slate-500">
                    Pilih tanggal dan kelompok karyawan yang ingin diperiksa.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        wire:model.live="date"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                    >

                </div>

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Karyawan
                    </label>

                    <x-wirekit::input
                        wire:model.live.debounce.400ms="search"
                        placeholder="Nama atau kode employee"
                        name="search"
                    />

                </div>

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Divisi
                    </label>

                    <select
                        wire:model.live="divisiId"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                    >
                        <option value="">Semua Divisi</option>

                        @foreach ($divisis as $divisi)
                            <option value="{{ $divisi->id }}">
                                {{ $divisi->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Team
                    </label>

                    <select
                        wire:model.live="teamId"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                    >
                        <option value="">Semua Team</option>

                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">
                                {{ $team->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Status
                    </label>

                    <select
                        wire:model.live="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                    >
                        <option value="">Semua Status</option>
                        <option value="present">Hadir</option>
                        <option value="late">Terlambat</option>
                        <option value="paid_leave">Cuti</option>
                        <option value="absence_sick">Sakit</option>
                        <option value="absence_permit">Izin</option>
                        <option value="pending">Menunggu</option>
                        <option value="unpresent">Belum Hadir</option>
                        <option value="holiday">Libur</option>
                        <option value="non_working">Non-Hari Kerja</option>
                        <option value="outside_contract">Di Luar Kontrak</option>
                    </select>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Status Karyawan
                    </h2>

                    <p class="text-sm text-slate-500">
                        {{ Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </p>

                </x-wirekit::stack>

                <span class="text-sm text-slate-500">
                    {{ $rows->count() }} karyawan
                </span>

            </div>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <div class="wk-scrollbar max-h-[620px] overflow-auto">

                <x-wirekit::table hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Karyawan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Organisasi
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Jadwal
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Check In
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Check Out
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Sumber
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>

                    <x-wirekit::table.body>

                        @forelse ($rows as $row)

                            @php
                                $state = $row['state'];
                                $schedule = $row['work_time_start'] && $row['work_time_end']
                                    ? $row['work_time_start'] . ' - ' . $row['work_time_end']
                                    : '—';
                            @endphp

                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-sky-100">

                                            @if ($row['avatar'])
                                                <img
                                                    src="{{ $row['avatar'] }}"
                                                    alt="{{ $row['employee_name'] }}"
                                                    class="block size-full rounded-full object-cover"
                                                >
                                            @else
                                                <img
                                                    src="{{ asset('assets/nonProfile.jpg') }}"
                                                    alt=""
                                                    class="block size-full rounded-full object-cover"
                                                >
                                            @endif

                                        </div>

                                        <x-wirekit::stack gap="1">

                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $row['employee_name'] }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $row['employee_code'] }}
                                            </p>

                                        </x-wirekit::stack>

                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <x-wirekit::stack gap="1">

                                        <span class="text-sm text-slate-700">
                                            {{ $row['division_name'] }}
                                        </span>

                                        <span class="text-xs text-slate-400">
                                            {{ $row['team_name'] }}
                                        </span>

                                    </x-wirekit::stack>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $schedule }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $row['check_in'] ?? '—' }}
                                    </span>

                                    @if ($row['late_minutes'] > 0)
                                        <span class="block text-xs text-amber-600">
                                            +{{ $row['late_minutes'] }} menit
                                        </span>
                                    @endif

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $row['check_out'] ?? '—' }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                        @switch($state['status'])
                                            @case('present') bg-emerald-50 text-emerald-600 @break
                                            @case('late') bg-amber-50 text-amber-600 @break
                                            @case('paid_leave') bg-sky-50 text-sky-600 @break
                                            @case('absence_sick') bg-violet-50 text-violet-600 @break
                                            @case('absence_permit') bg-violet-50 text-violet-600 @break
                                            @case('unpresent') bg-rose-50 text-rose-600 @break
                                            @case('pending') bg-slate-100 text-slate-600 @break
                                            @default bg-slate-100 text-slate-500
                                        @endswitch
                                    ">
                                        {{ $statusLabel($state['status']) }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <x-wirekit::badge variant="secondary" intent="secondary">
                                        {{ $sourceLabel($state['source']) }}
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="right">

                                    <livewire:components.main.attendances.modal-detail-daily-status
                                        :row="$row"
                                        :key="'daily-status-detail-' . $row['state']['employee_id'] . '-' . $row['state']['date']"
                                    >
                                        <x-wirekit::button
                                            type="button"
                                            variant="outline"
                                            class="px-3 py-1.5 text-xs"
                                        >
                                            Detail
                                        </x-wirekit::button>
                                    </livewire:components.main.attendances.modal-detail-daily-status>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="8">

                                    <div class="flex flex-col items-center justify-center py-12 text-center">

                                        <p class="text-sm font-medium text-slate-700">
                                            Tidak ada data Daily Status.
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Coba ubah tanggal atau filter yang digunakan.
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
