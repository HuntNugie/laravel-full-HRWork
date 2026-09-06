<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                Hari Libur
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola tanggal yang ditetapkan sebagai hari libur perusahaan.
            </p>
        </div>

    </x-wirekit::stack>


    {{-- =====================================================
        HOLIDAY CARD
    ====================================================== --}}
    <x-wirekit::card>

        {{-- =================================================
            HEADER
        ================================================== --}}
        <x-wirekit::card.header>

            <x-wirekit::row justify="between" align="center" gap="md">

                <x-wirekit::stack gap="xs">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Hari Libur 2026
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar tanggal yang tidak termasuk dalam hari kerja.
                    </p>

                </x-wirekit::stack>


                <x-wirekit::button size="sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                    Tambah Hari Libur
                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::card.header>


        {{-- =================================================
            FILTER
        ================================================== --}}
        <x-wirekit::card.body>

            <x-wirekit::row gap="sm" align="end">

                <div class="w-full max-w-[180px]">

                    <x-wirekit::select label="Tahun" name="year" value="2026">
                        <option value="2026">
                            2026
                        </option>

                        <option value="2025">
                            2025
                        </option>

                        <option value="2024">
                            2024
                        </option>
                    </x-wirekit::select>

                </div>


                <div class="w-full max-w-sm">

                    <x-wirekit::input label="Cari" name="search" placeholder="Cari hari libur..." />

                </div>

            </x-wirekit::row>

        </x-wirekit::card.body>


        <x-wirekit::divider />


        {{-- =================================================
            HOLIDAY LIST
        ================================================== --}}
        <x-wirekit::card.body>

            <x-wirekit::stack gap="sm">

                {{-- =========================================
                    HOLIDAY 1
                ========================================== --}}
                <div
                    class="group rounded-xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50/50">

                    <x-wirekit::row justify="between" align="center" gap="md">

                        <div class="flex min-w-0 items-center gap-4">

                            {{-- DATE --}}
                            <div
                                class="flex size-12 shrink-0 flex-col items-center justify-center rounded-xl bg-[#92EEFF]">

                                <span class="text-[10px] font-medium uppercase text-cyan-700">
                                    Agu
                                </span>

                                <span class="text-lg font-semibold leading-5 text-cyan-900">
                                    17
                                </span>

                            </div>


                            {{-- INFO --}}
                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <p class="text-sm font-semibold text-slate-900">
                                        Hari Kemerdekaan Republik Indonesia
                                    </p>

                                    <span
                                        class="rounded-full bg-[#C4F7CA] px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Hari Libur
                                    </span>

                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    Senin, 17 Agustus 2026
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Hari libur nasional
                                </p>

                            </div>

                        </div>


                        {{-- ACTION --}}
                        <x-wirekit::button variant="ghost" size="sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.75a.75.75 0 100-1.5.75.75 0 000 1.5zm0 6a.75.75 0 100-1.5.75.75 0 000 1.5zm0 6a.75.75 0 100-1.5.75 0 000 1.5z" />
                            </svg>
                        </x-wirekit::button>

                    </x-wirekit::row>

                </div>


                {{-- =========================================
                    HOLIDAY 2
                ========================================== --}}
                <div
                    class="group rounded-xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50/50">

                    <x-wirekit::row justify="between" align="center" gap="md">

                        <div class="flex min-w-0 items-center gap-4">

                            <div
                                class="flex size-12 shrink-0 flex-col items-center justify-center rounded-xl bg-[#FFA239]">

                                <span class="text-[10px] font-medium uppercase text-orange-800">
                                    Des
                                </span>

                                <span class="text-lg font-semibold leading-5 text-orange-950">
                                    25
                                </span>

                            </div>


                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <p class="text-sm font-semibold text-slate-900">
                                        Hari Natal
                                    </p>

                                    <span
                                        class="rounded-full bg-[#C4F7CA] px-2.5 py-1 text-xs font-medium text-emerald-700">
                                        Hari Libur
                                    </span>

                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    Jumat, 25 Desember 2026
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Hari libur nasional
                                </p>

                            </div>

                        </div>


                        <x-wirekit::button variant="ghost" size="sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.75a.75.75 0 100-1.5.75.75 0 000 1.5zm0 6a.75.75 0 100-1.5.75 0 000 1.5zm0 6a.75.75 0 100-1.5.75 0 000 1.5z" />
                            </svg>
                        </x-wirekit::button>

                    </x-wirekit::row>

                </div>


                {{-- =========================================
                    HOLIDAY 3
                ========================================== --}}
                <div
                    class="group rounded-xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50/50">

                    <x-wirekit::row justify="between" align="center" gap="md">

                        <div class="flex min-w-0 items-center gap-4">

                            <div
                                class="flex size-12 shrink-0 flex-col items-center justify-center rounded-xl bg-slate-100">

                                <span class="text-[10px] font-medium uppercase text-slate-500">
                                    Des
                                </span>

                                <span class="text-lg font-semibold leading-5 text-slate-800">
                                    31
                                </span>

                            </div>


                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <p class="text-sm font-semibold text-slate-900">
                                        Libur Perusahaan
                                    </p>

                                    <span
                                        class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                        Khusus
                                    </span>

                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    Kamis, 31 Desember 2026
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Libur khusus perusahaan
                                </p>

                            </div>

                        </div>


                        <x-wirekit::button variant="ghost" size="sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.75a.75.75 0 100-1.5.75.75 0 000 1.5zm0 6a.75.75 0 100-1.5.75 0 000 1.5zm0 6a.75.75 0 100-1.5.75 0 000 1.5z" />
                            </svg>
                        </x-wirekit::button>

                    </x-wirekit::row>

                </div>

            </x-wirekit::stack>

        </x-wirekit::card.body>


        {{-- =================================================
            FOOTER
        ================================================== --}}
        <x-wirekit::card.footer>

            <x-wirekit::row justify="between" align="center">

                <p class="text-sm text-slate-500">
                    3 hari libur terdaftar pada tahun 2026.
                </p>

            </x-wirekit::row>

        </x-wirekit::card.footer>

    </x-wirekit::card>


    {{-- =====================================================
        ADD HOLIDAY MODAL
        STATIC PREVIEW
    ====================================================== --}}


</div>
