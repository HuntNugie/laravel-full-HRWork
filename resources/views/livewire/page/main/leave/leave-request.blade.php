<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="xs">

            <span class="text-sm font-medium text-[#30AFFF]">
                Layanan Karyawan
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Cuti Saya
            </h1>

            <p class="text-sm text-slate-500">
                Kelola hak dan pengajuan cuti Anda.
            </p>

        </x-wirekit::stack>

        {{-- Ajukan Cuti --}}
        <livewire:components.main.leave.modal-leave-request>
            <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                <x-wirekit::icon name="plus" />
                Ajukan Cuti
            </x-wirekit::button>
        </livewire:components.main.leave.modal-leave-request>


    </div>


    {{-- =====================================================
        LEAVE BALANCE
    ====================================================== --}}
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">

        @forelse ($entitlements as $leav)
            @php
                $used = $this->usedLeave($leav->leave_type_id);
                $remaining = $this->remainingLeave($leav);
            @endphp

            <x-wirekit::card>

                <x-wirekit::card.body>

                    <div class="space-y-5">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-sm font-medium text-slate-600">
                                    {{ $leav->leaveType?->name }}
                                </p>

                                <p class="mt-1 text-2xl font-bold text-slate-900">
                                    {{ $remaining }} Hari
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Sisa cuti
                                </p>

                            </div>

                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                                <x-wirekit::icon name="calendar" class="size-5 text-sky-500" />
                            </div>

                        </div>

                        <div class="space-y-2">

                            <x-wirekit::progress :value="$used" :max="$leav->days" label="Terpakai" show-value
                                intent="primary" size="sm" />

                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>
                                    Terpakai {{ $used }} hari
                                </span>

                                <span>
                                    dari {{ $leav->days }} hari
                                </span>
                            </div>

                        </div>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>

        @empty

            <x-wirekit::card class="md:col-span-2 xl:col-span-3">

                <x-wirekit::card.body>

                    <div
                        class="flex items-start gap-3 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-4">

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                            <x-wirekit::icon name="information-circle" class="size-5 text-slate-500" />
                        </div>

                        <div>

                            <p class="text-sm font-semibold text-slate-800">
                                Belum ada hak cuti pada kontrak aktif
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Kontrak aktif Anda belum memiliki jatah cuti
                                yang terdaftar saat ini.
                            </p>

                        </div>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>
        @endforelse

    </div>


    {{-- =====================================================
        CONTRACT AKTIF
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Contract Aktif
                </h2>

                <p class="text-sm text-slate-500">
                    Contract yang menjadi dasar hak cuti Anda saat ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if (!$employee->latestEmployeeContract)
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center">

                    <p class="text-base font-semibold text-slate-800">
                        Belum ada contract aktif
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        Saat ini Anda belum memiliki contract aktif
                        yang menjadi dasar hak cuti.
                    </p>

                </div>
            @else
                @php
                    $contract = $employee->latestEmployeeContract;
                @endphp

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-start gap-3">

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                            <x-wirekit::icon name="document-text" class="size-5 text-slate-600" />
                        </div>

                        <div>

                            <div class="flex flex-wrap items-center gap-2">

                                <p class="text-sm font-semibold text-slate-800">
                                    {{ $contract->contract_number }}
                                </p>

                                <x-wirekit::badge intent="success">
                                    Aktif
                                </x-wirekit::badge>

                            </div>

                            <p class="mt-1 text-sm text-slate-600">
                                {{ $contract->position_name }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ \Carbon\Carbon::parse($contract->start_date)->translatedFormat('d F Y') }}
                                –
                                {{ \Carbon\Carbon::parse($contract->end_date)->translatedFormat('d F Y') }}
                            </p>

                        </div>

                    </div>

                </div>
            @endif

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
                        Pengajuan yang masih menunggu persetujuan belum mengurangi
                        sisa jatah cuti. Jatah akan berkurang setelah pengajuan disetujui.
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        HISTORY
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Riwayat Pengajuan Cuti
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar pengajuan cuti yang pernah Anda buat.
                    </p>

                </x-wirekit::stack>


                {{-- FILTER --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">

                    <div class="w-full sm:w-40">
                        <x-wirekit::input type="date" label="Dari Tanggal" />
                    </div>

                    <div class="w-full sm:w-40">
                        <x-wirekit::input type="date" label="Sampai Tanggal" />
                    </div>

                </div>

            </div>

            <div class="mt-4">

                <x-wirekit::segmented-control label="Status Pengajuan" name="leave_status" :options="[
                    'all' => 'Semua',
                    'pending' => 'Menunggu',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    'cancelled' => 'Dibatalkan',
                ]"
                    value="all" />

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar overflow-x-auto">

                <x-wirekit::table hoverable table-label="Riwayat pengajuan cuti">

                    {{-- TABLE HEADER --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

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


                    {{-- TABLE BODY --}}
                    <x-wirekit::table.body>

                        @forelse ($leaveRequests as $request)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $request->leaveType?->name }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            Diajukan
                                            {{ $request->created_at?->translatedFormat('d F Y') }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <p class="text-sm text-slate-700">
                                        {{ \Carbon\Carbon::parse($request->start_date)->translatedFormat('d M Y') }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        s.d.
                                        {{ \Carbon\Carbon::parse($request->end_date)->translatedFormat('d M Y') }}
                                    </p>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>
                                    {{ $request->total_days }} Hari
                                </x-wirekit::table.td>


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


                                <x-wirekit::table.td align="right">

                                    <div class="flex justify-end gap-2">

                                        <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                            Detail
                                        </x-wirekit::button>

                                        @if ($request->status === 'pending')
                                            <x-wirekit::button type="button" variant="outline" intent="danger"
                                                class="px-3 py-1.5 text-xs">
                                                Batalkan
                                            </x-wirekit::button>
                                        @endif

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="5">

                                    <div class="py-8 text-center">

                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada pengajuan cuti
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Riwayat pengajuan cuti Anda akan tampil di sini.
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
