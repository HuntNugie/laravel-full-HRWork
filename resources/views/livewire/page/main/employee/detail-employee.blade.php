<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('employee.view') }}" wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Karyawan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Detail Karyawan
        </h1>

        <p class="text-sm text-slate-500">
            Informasi lengkap mengenai data pribadi dan kepegawaian karyawan.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        EMPLOYEE HEADER
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

                {{-- Employee Identity --}}
                <div class="flex items-center gap-4">

                    {{-- Avatar --}}
                    <div
                        class="flex h-24 w-24 shrink-0 items-center justify-center
                               rounded-full bg-[#92EEFF]
                               text-3xl font-bold text-sky-700
                               ring-1 ring-slate-200">
                        NP
                    </div>

                    {{-- Information --}}
                    <x-wirekit::stack gap="1">

                        <h2 class="text-xl font-semibold text-slate-900">
                            {{ $employee->user->name }}
                        </h2>

                        <p class="text-sm text-slate-500">
                            {{ $employee->employee_code }}
                        </p>

                        <div class="mt-2 flex flex-wrap items-center gap-2">

                            <span @class([
                                "inline-flex items-center rounded-full
                                                                                                px-2.5 py-1
                                                                                               text-xs font-medium ",
                                'bg-emerald-50 text-emerald-600' => $employee?->status === 'active',
                                'bg-red-50 text-red-600' =>
                                    $employee?->status === 'inactive' || $employee?->status === 'resign',
                                'bg-yellow-50 text-yellow-600' => $employee?->status === 'onboarding',
                                'bg-slate-50 text-slate-600' => $employee?->status === 'terminated',
                                'bg-sky-50 text-sky-600' => $employee?->status === null,
                            ])>
                                {{ $employee?->status ?? 'Belum di ketahui' }}
                            </span>

                            <span
                                class="inline-flex items-center rounded-full
                                       bg-orange-50 px-2.5 py-1
                                       text-xs font-medium text-orange-600">
                                {{ $employee?->position->name ?? 'Belum di ketahui' }}
                            </span>

                            <span
                                class="inline-flex items-center rounded-full
                                       bg-slate-100 px-2.5 py-1
                                       text-xs font-medium text-slate-600">
                                {{ $employee?->team->divisi->name ?? 'Belum di ketahui' }}
                            </span>

                        </div>

                    </x-wirekit::stack>

                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-2">

                    <x-wirekit::button type="button"
                        class="border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        Edit Employee
                    </x-wirekit::button>

                    <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                        Edit Contract
                    </x-wirekit::button>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        PERSONAL INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Pribadi
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi identitas dan kontak karyawan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-x-8 gap-y-6 md:grid-cols-2">

                {{-- Full Name --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Nama Lengkap
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->name }}
                    </p>
                </div>

                {{-- NIK --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        NIK
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee?->profile->nik ?? 'Belum di ketahui' }}
                    </p>
                </div>

                {{-- Gender --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Jenis Kelamin
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee?->profile->gender ?? 'Belum di ketahui' }}
                    </p>
                </div>

                {{-- Phone --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Nomor Telepon
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee?->profile->phone_number ?? 'Belum di ketahui' }}
                    </p>
                </div>

                {{-- Email --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Email
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->email }}
                    </p>
                </div>

                {{-- Address --}}
                <div class="md:col-span-2">
                    <span class="text-xs font-medium text-slate-400">
                        Alamat
                    </span>

                    <div class="mt-1 grid gap-3 sm:grid-cols-3">
                        <div>
                            <span class="text-xs text-slate-400">Provinsi</span>
                            <p class="text-sm font-medium text-slate-800">

                                {{ $employee?->profile->addressProfile->village->district->regency->province->name ?? 'Belum di ketahui' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">Kabupaten/Kota</span>
                            <p class="text-sm font-medium text-slate-800">
                                {{ $employee?->profile->addressProfile->village->district->regency->name ?? 'Belum di ketahui' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">Kecamatan</span>
                            <p class="text-sm font-medium text-slate-800">
                                {{ $employee?->profile->addressProfile->village->district->name ?? 'Belum di ketahui' }}
                            </p>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400">Desa</span>
                            <p class="text-sm font-medium text-slate-800">
                                {{ $employee?->profile->addressProfile->village->name ?? 'Belum di ketahui' }}
                            </p>
                        </div>

                        <div class="sm:col-span-3">
                            <span class="text-xs text-slate-400">Alamat Lengkap</span>
                            <p class="text-sm font-medium leading-6 text-slate-800">
                                {{ $employee?->profile->addressProfile->full_address ?? 'Belum di ketahui' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        EMPLOYMENT INFORMATION
    ====================================================== --}}
    <div class="grid gap-6 lg:grid-cols-2">



            {{-- Team & Position --}}
            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Penempatan Kerja
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi posisi dan tim karyawan.
                        </p>

                    </x-wirekit::stack>

                </x-wirekit::card.header>


                <x-wirekit::card.body>
                    @if ($employee?->team || $employee?->position)

                    <div class="grid gap-6 sm:grid-cols-2">

                        {{-- Team --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Team
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $employee?->team->name ?? 'Belum di tambahkan' }}
                            </p>

                        </div>

                        {{-- Position --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Position
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $employee?->position->name ?? 'Belum di tambahkan' }}
                            </p>

                        </div>


                        {{-- Employment Status --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Status
                            </span>

                            <p @class([
                                'mt-1 text-sm font-medium ',
                                'bg-emerald-50 text-emerald-600' => $employee?->status === 'active',
                                'bg-red-50 text-red-600' =>
                                    $employee?->status === 'inactive' || $employee?->status === 'resign',
                                'bg-yellow-50 text-yellow-600' => $employee?->status === 'onboarding',
                                'bg-slate-50 text-slate-600' => $employee?->status === 'terminated',
                                'bg-sky-50 text-sky-600' => $employee?->status === null,
                            ])>
                                {{ $employee?->status ?? 'Belum di atur' }}
                            </p>

                        </div>

                    </div>
                    @else
                    <div>
                        Belum di tempatkan kerja
                    </div>

                    @endif
                </x-wirekit::card.body>

            </x-wirekit::card>
        {{-- Contract --}}

            <x-wirekit::card>

                <x-wirekit::card.header>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Contract
                            </h2>

                            <p class="text-sm text-slate-500">
                                Informasi kontrak kerja karyawan.
                            </p>

                        </x-wirekit::stack>

                        <div class="flex items-center gap-2">

                            <span
                                @class(['inline-flex items-center rounded-full
                        px-2.5 py-1
                       text-xs font-medium','bg-emerald-50 text-emerald-600' => $employee?->latestEmployeeContract?->status == 'active',
                        'bg-slate-50 text-slate-600' => $employee?->latestEmployeeContract?->status == 'draft',
                        'bg-yellow-50 text-yellow-600' => $employee?->latestEmployeeContract?->status == 'expired',
                        'bg-red-50 text-red-600' => $employee?->latestEmployeeContract?->status == 'terminated',
                        'bg-orange-50 text-orange-600' => $employee?->latestEmployeeContract?->status == null,
                       ])>
                              {{$employee?->latestEmployeeContract?->status ?? "Belum membuat contract"}}
                            </span>

                            @if ($employee?->latestEmployeeContract)
                                 <x-wirekit::button type="button" wire:navigate
                                class="border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                                Detail Contract
                            </x-wirekit::button>
                            @else
                             <x-wirekit::button type="button" wire:navigate href="{{ route('contract.create',$employee->id) }}"
                                class="border border-blue-200 bg-white text-blue-700 hover:bg-blue-50">
                                <x-wirekit::icon name="plus"/> Create Contract
                            </x-wirekit::button>
                            @endif


                        </div>

                    </div>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                  @if (count($employee->employeeContract))
                        <div class="grid gap-6 sm:grid-cols-2">

                        {{-- Contract Type --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Jenis Kontrak
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                PKWT
                            </p>

                        </div>

                        {{-- Contract Number --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Nomor Kontrak
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                CTR-2026-001
                            </p>

                        </div>

                        {{-- Start Date --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Tanggal Mulai
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                01 September 2026
                            </p>

                        </div>

                        {{-- End Date --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Tanggal Berakhir
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                31 August 2027
                            </p>

                        </div>

                    </div>


                    {{-- Salary --}}
                    <div class="mt-6 border-t border-slate-100 pt-6">

                        <span class="text-xs font-medium text-slate-400">
                            Gaji Pokok
                        </span>

                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            Rp 8.000.000
                        </p>

                    </div>
                  @endif

                </x-wirekit::card.body>

            </x-wirekit::card>

    </div>


    {{-- =====================================================
        BANK ACCOUNT
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Rekening Bank
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi rekening yang digunakan untuk kebutuhan penggajian.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 md:grid-cols-3">

                {{-- Bank --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Bank
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        Bank Central Asia
                    </p>

                </div>

                {{-- Account Number --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Nomor Rekening
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        1234567890
                    </p>

                </div>

                {{-- Account Holder --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Nama Pemilik Rekening
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        Nugie Pratama
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        ACCOUNT INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Akun
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi akun yang digunakan untuk mengakses sistem.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-3">

                {{-- Username / Email --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Email Akun
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        nugie@perusahaan.com
                    </p>

                </div>

                {{-- Role --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Role
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        Employee
                    </p>

                </div>

                {{-- Account Status --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Status Akun
                    </span>

                    <p class="mt-1 text-sm font-medium text-emerald-600">
                        Active
                    </p>

                </div>

                {{-- Joined --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Dibuat Pada
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        24 August 2026
                    </p>

                </div>

                {{-- Last Login --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Login Terakhir
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        24 August 2026, 09:42
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
