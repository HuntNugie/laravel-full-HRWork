<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="xs">

            <span class="text-sm font-medium text-[#30AFFF]">
                Ketidakhadiran
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Manajemen Cuti
            </h1>

            <p class="text-sm text-slate-500">
                Kelola dan tinjau pengajuan cuti karyawan.
            </p>

        </x-wirekit::stack>

    </div>


    {{-- =====================================================
        STATUS FILTER
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <x-wirekit::segmented-control label="Status Pengajuan" name="leave_status" :options="[
                'all' => 'Semua',
                'pending' => 'Menunggu',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'cancelled' => 'Dibatalkan',
            ]"
                wire:model.live="segment" />

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        FILTER
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">

                {{-- SEARCH --}}
                <div class="w-full lg:flex-1">

                    <x-wirekit::input type="text" label="Cari Karyawan" name="search"
                        placeholder="Cari nama atau kode karyawan..." wire:model.live.debounce.300ms="search" />

                </div>


                {{-- DARI TANGGAL --}}
                <div class="w-full sm:w-48">

                    <x-wirekit::input type="date" label="Dari Tanggal" wire:model.live="dateFrom" />

                </div>


                {{-- SAMPAI TANGGAL --}}
                <div class="w-full sm:w-48">

                    <x-wirekit::input type="date" label="Sampai Tanggal" wire:model.live="dateTo" />

                </div>


                {{-- RESET --}}
                <x-wirekit::button type="button" variant="outline" wire:click="resetFilters" class="shrink-0">
                    Reset
                </x-wirekit::button>

            </div>


            {{-- ERROR RANGE TANGGAL --}}
            @error('dateTo')
                <p class="mt-2 text-sm text-red-500">
                    {{ $message }}
                </p>
            @enderror

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex items-start gap-3">

                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                    <x-wirekit::icon name="information-circle" class="size-5 text-sky-500" />
                </div>


                <div>

                    <p class="text-sm font-medium text-slate-800">
                        Informasi Pengajuan
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Pengajuan dengan status Menunggu belum mengurangi
                        jatah cuti secara permanen. Ketersediaan jatah akan
                        diperiksa kembali saat pengajuan diproses.
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        TABLE
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Pengajuan Cuti
                </h2>

                <p class="text-sm text-slate-500">
                    Daftar pengajuan cuti yang dibuat oleh karyawan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar overflow-x-auto">

                <x-wirekit::table hoverable table-label="Daftar pengajuan cuti">

                    {{-- =================================================
                        TABLE HEADER
                    ================================================== --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Karyawan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Jenis Cuti
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Periode
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Durasi
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    {{-- =================================================
                        TABLE BODY
                    ================================================== --}}
                    <x-wirekit::table.body>

                        @forelse ($leaveRequests as $request)
                            <x-wirekit::table.row>

                                {{-- =====================================
                                    KARYAWAN
                                ====================================== --}}
                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $request->employees?->user?->name ?? '-' }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            {{ $request->employees?->employee_code ?? '-' }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- =====================================
                                    JENIS CUTI
                                ====================================== --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $request->leaveType?->name ?? '-' }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- =====================================
                                    PERIODE
                                ====================================== --}}
                                <x-wirekit::table.td>

                                    <p class="text-sm text-slate-700">

                                        {{ \Carbon\Carbon::parse($request->start_date)->translatedFormat('d M Y') }}

                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">

                                        s.d.

                                        {{ \Carbon\Carbon::parse($request->end_date)->translatedFormat('d M Y') }}

                                    </p>

                                </x-wirekit::table.td>


                                {{-- =====================================
                                    DURASI
                                ====================================== --}}
                                <x-wirekit::table.td>

                                    {{ $request->total_days }} Hari

                                </x-wirekit::table.td>


                                {{-- =====================================
                                    STATUS
                                ====================================== --}}
                                <x-wirekit::table.td>

                                    @if ($request->status === 'pending')
                                        <x-wirekit::badge intent="warning">
                                            Menunggu
                                        </x-wirekit::badge>
                                    @elseif ($request->status === 'approved')
                                        <x-wirekit::badge intent="success">
                                            Disetujui
                                        </x-wirekit::badge>
                                    @elseif ($request->status === 'rejected')
                                        <x-wirekit::badge intent="danger">
                                            Ditolak
                                        </x-wirekit::badge>
                                    @elseif ($request->status === 'cancelled')
                                        <x-wirekit::badge intent="secondary">
                                            Dibatalkan
                                        </x-wirekit::badge>
                                    @endif

                                </x-wirekit::table.td>


                                {{-- =====================================
                                    AKSI
                                ====================================== --}}
                                <x-wirekit::table.td align="right">

                                    @can('show-management-leave')
                                        <livewire:components.main.leave.modal-detail-management-leave :request="$request"
                                            :key="'management-leave-detail-' . $request->id">
                                            <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                                Detail
                                            </x-wirekit::button>
                                        </livewire:components.main.leave.modal-detail-management-leave>
                                    @endcan

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            {{-- =========================================
                                EMPTY STATE
                            ========================================== --}}
                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="6">

                                    <div class="py-10 text-center">

                                        <div
                                            class="mx-auto flex size-12 items-center justify-center rounded-xl bg-slate-100">
                                            <x-wirekit::icon name="calendar" class="size-6 text-slate-400" />
                                        </div>

                                        <p class="mt-4 text-sm font-semibold text-slate-700">
                                            Tidak ada pengajuan cuti
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Tidak ditemukan pengajuan cuti
                                            yang sesuai dengan filter yang dipilih.
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>


            {{-- =================================================
                PAGINATION
            ================================================== --}}
            @if ($leaveRequests->hasPages())
                <div class="mt-4">
                    {{ $leaveRequests->links() }}
                </div>
            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
