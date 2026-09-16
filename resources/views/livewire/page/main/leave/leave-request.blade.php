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
        <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
            <x-wirekit::icon name="plus" />
            Ajukan Cuti
        </x-wirekit::button>

    </div>


    {{-- =====================================================
        LEAVE BALANCE
    ====================================================== --}}
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">

        {{-- =================================================
            CUTI TAHUNAN
        ================================================== --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="space-y-5">

                    <div class="flex items-start justify-between gap-4">

                        <div>
                            <p class="text-sm font-medium text-slate-600">
                                Cuti Tahunan
                            </p>

                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                10 Hari
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Sisa cuti
                            </p>
                        </div>

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                            <x-wirekit::icon name="calendar" class="size-5 text-sky-500" />
                        </div>

                    </div>


                    <x-wirekit::progress :value="2" :max="12" label="Terpakai" show-value
                        intent="primary" size="sm" />

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- =================================================
            CUTI KHUSUS
        ================================================== --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="space-y-5">

                    <div class="flex items-start justify-between gap-4">

                        <div>
                            <p class="text-sm font-medium text-slate-600">
                                Cuti Khusus
                            </p>

                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                2 Hari
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Sisa cuti
                            </p>
                        </div>

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                            <x-wirekit::icon name="calendar-days" class="size-5 text-emerald-500" />
                        </div>

                    </div>


                    <x-wirekit::progress :value="1" :max="3" label="Terpakai" show-value
                        intent="success" size="sm" />

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- =================================================
            CUTI MELAHIRKAN
        ================================================== --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="space-y-5">

                    <div class="flex items-start justify-between gap-4">

                        <div>
                            <p class="text-sm font-medium text-slate-600">
                                Cuti Melahirkan
                            </p>

                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                90 Hari
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Sisa cuti
                            </p>
                        </div>

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-violet-50">
                            <x-wirekit::icon name="heart" class="size-5 text-violet-500" />
                        </div>

                    </div>


                    <x-wirekit::progress :value="0" :max="90" label="Terpakai" show-value
                        intent="primary" size="sm" />

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

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

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-start gap-3">

                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                        <x-wirekit::icon name="document-text" class="size-5 text-slate-600" />
                    </div>

                    <div>

                        <div class="flex flex-wrap items-center gap-2">

                            <p class="text-sm font-semibold text-slate-800">
                                CTR/2026/09/001
                            </p>

                            <x-wirekit::badge intent="success">
                                Aktif
                            </x-wirekit::badge>

                        </div>

                        <p class="mt-1 text-sm text-slate-600">
                            Software Engineer
                        </p>

                        <p class="mt-0.5 text-xs text-slate-400">
                            01 Januari 2026 – 31 Desember 2026
                        </p>

                    </div>

                </div>

            </div>

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

                        {{-- =================================================
                            PENDING
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        Cuti Tahunan
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        Diajukan 17 September 2026
                                    </p>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <p class="text-sm text-slate-700">
                                    21 Sep 2026
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    s.d. 23 Sep 2026
                                </p>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                3 Hari
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge intent="warning">
                                    Menunggu
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td align="right">

                                <div class="flex justify-end gap-2">

                                    <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                        Detail
                                    </x-wirekit::button>

                                    <x-wirekit::button type="button" variant="outline" intent="danger"
                                        class="px-3 py-1.5 text-xs ">
                                        Batalkan
                                    </x-wirekit::button>

                                </div>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                            APPROVED
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        Cuti Khusus
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        Diajukan 10 September 2026
                                    </p>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <p class="text-sm text-slate-700">
                                    14 Sep 2026
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    s.d. 15 Sep 2026
                                </p>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                2 Hari
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge intent="success">
                                    Disetujui
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td align="right">

                                <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                            REJECTED
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        Cuti Tahunan
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        Diajukan 01 Agustus 2026
                                    </p>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <p class="text-sm text-slate-700">
                                    10 Agu 2026
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    s.d. 11 Agu 2026
                                </p>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                2 Hari
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge intent="danger">
                                    Ditolak
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td align="right">

                                <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                            CANCELLED
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        Cuti Tahunan
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        Diajukan 05 Juli 2026
                                    </p>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <p class="text-sm text-slate-700">
                                    12 Jul 2026
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    s.d. 13 Jul 2026
                                </p>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                2 Hari
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge intent="secondary">
                                    Dibatalkan
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td align="right">

                                <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
