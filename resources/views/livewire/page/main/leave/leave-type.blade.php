<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="xs">

            <h1 class="text-2xl font-semibold text-slate-900">
                Jenis Cuti
            </h1>

            <p class="text-sm text-slate-500">
                Kelola jenis cuti dan aturan dasar yang tersedia dalam sistem.
            </p>

        </x-wirekit::stack>


        {{-- Tambah --}}
        <livewire:components.main.leave.type-form-add>
            <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                <x-wirekit::icon name="plus" />
                Tambah Jenis Cuti
            </x-wirekit::button>
        </livewire:components.main.leave.type-form-add>

    </div>


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
                        Tentang Jenis Cuti
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Jenis cuti digunakan sebagai master untuk menentukan hak cuti
                        yang dapat diberikan pada kontrak karyawan.
                    </p>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        LEAVE TYPE LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex flex-col gap-1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Daftar Jenis Cuti
                    </h2>

                    <p class="text-sm text-slate-500">
                        Jenis cuti yang tersedia untuk digunakan pada kontrak karyawan.
                    </p>

                </div>

                <x-wirekit::input type="text" name="search" placeholder="Cari nama cuti" />

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar
                    overflow-x-auto">

                <x-wirekit::table hoverable table-label="Daftar jenis cuti">

                    {{-- =================================================
                        HEADER
                    ================================================== --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Jenis Cuti
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Jatah Default
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Berlaku Untuk
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
                        BODY
                    ================================================== --}}
                    <x-wirekit::table.body>

                        {{-- =================================================
                            CUTI TAHUNAN
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div>
                                    <p class="text-sm font-medium text-slate-900">
                                        Cuti Tahunan
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Cuti tahunan karyawan.
                                    </p>
                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm font-medium text-slate-800">
                                    12 Hari
                                </span>

                                <span class="ml-1 text-xs text-slate-400">
                                    / tahun
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge>
                                    Semua Karyawan
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge variant="success">
                                    Aktif
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td align="right">

                                <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                            CUTI KHUSUS
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div>
                                    <p class="text-sm font-medium text-slate-900">
                                        Cuti Khusus
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Cuti untuk kebutuhan khusus karyawan.
                                    </p>
                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm font-medium text-slate-800">
                                    3 Hari
                                </span>

                                <span class="ml-1 text-xs text-slate-400">
                                    / tahun
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge>
                                    Semua Karyawan
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge variant="success">
                                    Aktif
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td align="right">

                                <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                            CUTI MELAHIRKAN
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div>
                                    <p class="text-sm font-medium text-slate-900">
                                        Cuti Melahirkan
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Cuti yang diberikan kepada karyawan perempuan.
                                    </p>
                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm font-medium text-slate-800">
                                    90 Hari
                                </span>

                                <span class="ml-1 text-xs text-slate-400">
                                    / pengajuan
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge variant="warning">
                                    Perempuan
                                </x-wirekit::badge>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::badge variant="success">
                                    Aktif
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
