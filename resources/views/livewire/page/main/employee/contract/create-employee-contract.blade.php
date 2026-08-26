<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a
            href=""
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]"
        >
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Karyawan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-black">
            Buat Contract
        </h1>

        <p class="text-sm text-black">
            Tambahkan contract kerja untuk karyawan.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        EMPLOYEE INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-black">
                    Informasi Karyawan
                </h2>

                <p class="text-sm text-black/60">
                    Contract ini akan dibuat untuk karyawan berikut.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <div class="grid gap-5 sm:grid-cols-3">

                {{-- Employee Code --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Employee Code
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        EMP-001
                    </p>
                </div>

                {{-- Employee Name --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Nama Karyawan
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        Nugie Pratama
                    </p>
                </div>

                {{-- Position --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Posisi
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        Backend Developer
                    </p>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        CONTRACT INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-black">
                    Informasi Contract
                </h2>

                <p class="text-sm text-black/60">
                    Tentukan informasi utama contract karyawan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-5 md:grid-cols-2">

                {{-- Contract Number --}}
                <x-wirekit::input
                    class="text-black"
                    label="Nomor Contract"
                    name="contract_number"
                    value="CTR-2026-001"
                    placeholder="Contoh: CTR-2026-001"
                />

                {{-- Employment Type --}}
                <x-wirekit::select
                    label="Jenis Employment"
                    name="employment_type"
                    placeholder="Pilih jenis employment..."
                    :options="[
                        'pkwt' => 'PKWT',
                        'pkwtt' => 'PKWTT',
                        'internship' => 'Internship',
                        'freelance' => 'Freelance',
                    ]"
                    value="pkwt"
                />

                {{-- Start Date --}}
                <x-wirekit::input
                    class="text-black"
                    label="Tanggal Mulai"
                    name="start_date"
                    type="date"
                    value="2026-09-01"
                />

                {{-- End Date --}}
                <x-wirekit::input
                    class="text-black"
                    label="Tanggal Berakhir"
                    name="end_date"
                    type="date"
                    value="2027-08-31"
                />

                {{-- Daily Salary --}}
                <x-wirekit::input
                    class="text-black"
                    label="Gaji Harian"
                    name="salary_daily"
                    type="number"
                    min="0"
                    value="250000"
                    placeholder="Contoh: 250000"
                />

                {{-- Status --}}
                <x-wirekit::select
                    label="Status Contract"
                    name="status"
                    placeholder="Pilih status contract..."
                    :options="[
                        'draft' => 'Draft',
                        'active' => 'Active',
                    ]"
                    value="draft"
                />

                {{-- Notes --}}
                <div class="md:col-span-2">

                    <x-wirekit::textarea
                        class="text-black"
                        label="Catatan"
                        name="notes"
                        rows="4"
                        placeholder="Tambahkan catatan mengenai contract..."
                    >Contract awal karyawan.</x-wirekit::textarea>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        CONTRACT STATUS
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-black">
                    Status Contract
                </h2>

                <p class="text-sm text-black/60">
                    Contract baru dapat dibuat sebagai draft atau langsung aktif.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Status Contract
                        </p>

                        <p class="text-xs text-slate-500">
                            Contract ini masih dalam tahap draft.
                        </p>

                    </div>

                    <span
                        class="inline-flex w-fit items-center rounded-full
                               bg-slate-100 px-2.5 py-1
                               text-xs font-medium text-slate-600"
                    >
                        Draft
                    </span>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        FORM ACTION
    ====================================================== --}}
    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">

        <x-wirekit::button
            type="button"
            class="border border-slate-200 bg-white text-black hover:bg-slate-50"
        >
            Batal
        </x-wirekit::button>

        <x-wirekit::button
            type="button"
            class="bg-[#30AFFF] text-white hover:bg-sky-500"
        >
            Buat Contract
        </x-wirekit::button>

    </div>

</x-wirekit::stack>
