<x-wirekit::stack gap="md">

    {{-- =====================================================
    HEADER
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Riwayat Presensi
            </h1>

            <p class="text-sm text-slate-500">
                Lihat riwayat kehadiran karyawan berdasarkan periode.
            </p>

        </x-wirekit::stack>

    </div>


    {{-- =====================================================
    FILTER
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Filter Riwayat
                </h2>

                <p class="text-sm text-slate-500">
                    Tentukan periode dan status presensi yang ingin ditampilkan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

                {{-- EMPLOYEE --}}
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Employee
                    </label>

                    <x-wirekit::input wire:model.live.debounce.500ms="search" placeholder="Cari nama atau kode employee"
                        name="search" class="text-black" />

                </div>


                {{-- DARI --}}
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Dari
                    </label>

                    <input type="date" wire:model.live="startDate"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5
                            text-sm text-slate-700 outline-none
                            focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">

                </div>


                {{-- SAMPAI --}}
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Sampai
                    </label>

                    <input type="date" wire:model.live="endDate"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5
                            text-sm text-slate-700 outline-none
                            focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">

                </div>


                {{-- STATUS --}}
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Status
                    </label>

                    <select wire:model.live="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5
                            text-sm text-slate-700 outline-none
                            focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20">

                        <option value="">
                            Semua Status
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

                    </select>

                </div>

            </div>


            {{-- RESET --}}
            <div class="mt-4 flex justify-end">

                <x-wirekit::button type="button" variant="outline" wire:click="resetFilter"
                    class="border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                    <x-wirekit::icon name="refresh" />
                    Reset Filter
                </x-wirekit::button>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
    ATTENDANCE HISTORY
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Attendance History
                </h2>

                <p class="text-sm text-slate-500">
                    Riwayat presensi seluruh karyawan berdasarkan periode.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            {{-- SCROLL --}}
            <div class="wk-scrollbar max-h-[600px] overflow-auto">

                <x-wirekit::table alpine-sort hoverable>

                    {{-- TABLE HEAD --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Tanggal
                            </x-wirekit::table.th>

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


                    {{-- TABLE BODY --}}
                    <x-wirekit::table.body>

                        @forelse ($attendances as $attendance)

                            <x-wirekit::table.row>

                                {{-- TANGGAL --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $attendance['date'] }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- EMPLOYEE --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center
                                                overflow-hidden rounded-full bg-sky-100">

                                            @if ($attendance['avatar'])
                                                <img src="{{ $attendance['avatar'] }}"
                                                    alt="{{ $attendance['employee_name'] }}"
                                                    class="block size-full rounded-full object-cover">
                                            @else
                                                <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                                    class="block size-full rounded-full object-cover">
                                            @endif

                                        </div>


                                        <x-wirekit::stack gap="1">

                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $attendance['employee_name'] }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $attendance['employee_code'] }}
                                            </p>

                                        </x-wirekit::stack>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- CHECK IN --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $attendance['check_in'] }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- CHECK OUT --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $attendance['check_out'] }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- DURASI --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $attendance['duration'] }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- STATUS --}}
                                <x-wirekit::table.td>

                                    @switch($attendance['status'])
                                        @case('present')
                                            <span
                                                class="inline-flex items-center rounded-full
                                                    bg-emerald-50 px-2.5 py-1
                                                    text-xs font-medium text-emerald-600">
                                                {{ $attendance['status_label'] }}
                                            </span>
                                        @break

                                        @case('late')
                                            <span
                                                class="inline-flex items-center rounded-full
                                                    bg-amber-50 px-2.5 py-1
                                                    text-xs font-medium text-amber-600">
                                                {{ $attendance['status_label'] }}
                                            </span>
                                        @break

                                        @case('absent')
                                            <span
                                                class="inline-flex items-center rounded-full
                                                    bg-slate-100 px-2.5 py-1
                                                    text-xs font-medium text-slate-500">
                                                {{ $attendance['status_label'] }}
                                            </span>
                                        @break
                                    @endswitch

                                </x-wirekit::table.td>


                                {{-- ACTIONS --}}
                                <x-wirekit::table.td>

                                    @can('show-attendance')
                                        @if ($attendance['attendance_id'])
                                            <livewire:components.main.attendances.modal-detail-attendance :attendance-id="$attendance['attendance_id']"
                                                :key="'attendance-detail-' . $attendance['attendance_id']">
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

                                    <x-wirekit::table.td colspan="7">

                                        <div
                                            class="flex flex-col items-center justify-center
                                            gap-2 py-10 text-center">

                                            <p class="text-sm font-medium text-slate-700">
                                                Tidak ada data presensi.
                                            </p>

                                            <p class="text-sm text-slate-500">
                                                Tidak ditemukan data berdasarkan filter yang dipilih.
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
