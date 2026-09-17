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
                    {{-- Avatar + Edit Foto --}}
                    <div class="flex shrink-0 flex-col items-center">

                        {{-- Avatar --}}
                        <div
                            class="flex h-24 w-24 items-center justify-center rounded-full
                   bg-[#92EEFF] text-3xl font-bold text-sky-700
                   ring-1 ring-slate-200">
                            @if ($employee->user->getFirstMediaUrl('avatar'))
                                <img src="{{ $employee->user->getFirstMediaUrl('avatar') }}"
                                    alt="gambar dari {{ $employee->user->name }}"
                                    class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                            @else
                                <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                    class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                            @endif
                        </div>

                        {{-- Edit Foto --}}
                        <div class="mt-2">
                            <livewire:components.main.employee.modal-edit-photo :user="$employee->user">
                                <x-wirekit::button type="button"
                                    class="border border-slate-200 bg-white text-xs text-slate-700 hover:bg-slate-50">
                                    <x-wirekit::icon name="pencil" />
                                    Edit Foto
                                </x-wirekit::button>
                            </livewire:components.main.employee.modal-edit-photo>
                        </div>

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
                                'bg-emerald-50 text-emerald-600' =>
                                    $employee?->status_employee === 'active',
                                'bg-red-50 text-red-600' =>
                                    $employee?->status_employee === 'inactive' ||
                                    $employee?->status_employee === 'resign',
                                'bg-yellow-50 text-yellow-600' =>
                                    $employee?->status_employee === 'onboarding',
                                'bg-slate-50 text-slate-600' =>
                                    $employee?->status_employee === 'terminated',
                                'bg-sky-50 text-sky-600' => $employee?->status_employee === null,
                            ])>
                                {{ $employee?->status_employee ?? 'Belum di ketahui' }}
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

                    <x-wirekit::button type="button" href="{{ route('employee.edit', $employee->id) }}" wire:navigate
                        class="border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        Edit Employee
                    </x-wirekit::button>

                    @if ($employee?->latestEmployeeContract)
                        <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            href="{{ route('contract.create', $employee->id) }}" wire:navigate>
                            Buat Contract baru
                        </x-wirekit::button>
                    @endif

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

                {{-- Tempat Lahir --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Tempat Lahir
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee?->profile->birth_address ?? 'Belum di ketahui' }}
                    </p>
                </div>

                {{-- Tanggal Lahir --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Tanggal Lahir
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee?->profile?->birth_date?->format('d F Y') ?? 'Belum di ketahui' }}
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
        BANK ACCOUNT
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="sm">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Rekening Bank
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi rekening yang digunakan untuk kebutuhan penggajian.
                    </p>

                </x-wirekit::stack>

                <div class="flex items-center gap-2">

                    <livewire:components.main.employee.modal-edit-rekening :employee="$employee">
                        <x-wirekit::button class="bg-[#30AFFF] text-white hover:bg-sky-500">
                            Edit Rekening
                        </x-wirekit::button>
                    </livewire:components.main.employee.modal-edit-rekening>


                </div>

            </div>
        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 md:grid-cols-3">

                {{-- Bank --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Bank
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile->bankAccount->bank->name }}
                    </p>

                </div>

                {{-- Account Number --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Nomor Rekening
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile->bankAccount->account_number }}
                    </p>

                </div>

                {{-- Account Holder --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Nama Pemilik Rekening
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile->bankAccount->account_holder }}
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
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <x-wirekit::stack gap="xs">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Informasi Akun
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi akun yang digunakan untuk mengakses sistem.
                    </p>
                </x-wirekit::stack>

                <div class="flex items-center gap-2">
                    <x-wirekit::button type="button" href="{{ route('user.show', $employee->user->id) }}"
                        wire:navigate class="border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        Detail Akun User
                    </x-wirekit::button>
                </div>

            </div>




        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-3">

                {{-- Username / Email --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Email Akun
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->email }}
                    </p>

                </div>

                {{-- Role --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Role
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->getRoleNames()->first() ?? 'Belum ada role' }}
                    </p>

                </div>

                {{-- Account Status --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Status Akun
                    </span>

                    <p @class([
                        'mt-1 text-sm font-medium ',
                        'text-emerald-600' => $employee->user->status === 'active',
                        'text-red-600' => $employee->user->status === 'inactive',
                        'text-yellow-600' => $employee->user->status === 'pending',
                    ])>
                        {{ $employee->user->status }}
                    </p>

                </div>

                {{-- Joined --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Dibuat Pada
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->created_at->format('d F Y') }}
                    </p>

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


                    @if (!$employee?->supervisorTeam)
                        <livewire:components.main.employee.modal-add-team :employee="$employee">
                            <x-wirekit::button>
                                <x-wirekit::icon name="{{ $employee?->team ? 'paint-brush' : 'plus' }}"
                                    intent="primary" />
                                {{ $employee?->team ? ' Ubah' : ' Tambahkan' }} team karyawan
                            </x-wirekit::button>
                        </livewire:components.main.employee.modal-add-team>
                    @endif

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

                                @if ($employee?->supervisorTeam)
                                    <x-wirekit::badge intent="success">Supervisor</x-wirekit::badge>
                                @endif
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
                                Status karyawan
                            </span>

                            <span @class([
                                "inline-flex items-center rounded-full
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            px-2.5 py-1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           text-xs font-medium ",
                                'bg-emerald-50 text-emerald-600' =>
                                    $employee?->status_employee === 'active',
                                'bg-red-50 text-red-600' =>
                                    $employee?->status_employee === 'inactive' ||
                                    $employee?->status_employee === 'resign',
                                'bg-yellow-50 text-yellow-600' =>
                                    $employee?->status_employee === 'onboarding',
                                'bg-slate-50 text-slate-600' =>
                                    $employee?->status_employee === 'terminated',
                                'bg-sky-50 text-sky-600' => $employee?->status_employee === null,
                            ])>
                                {{ $employee?->status_employee ?? 'Belum di ketahui' }}
                            </span>

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

                        <span @class([
                            'inline-flex items-center rounded-full
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                px-2.5 py-1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               text-xs font-medium',
                            'bg-emerald-50 text-emerald-600' =>
                                $employee?->latestEmployeeContract?->status == 'active',
                            'bg-slate-50 text-slate-600' =>
                                $employee?->latestEmployeeContract?->status == 'draft',
                            'bg-yellow-50 text-yellow-600' =>
                                $employee?->latestEmployeeContract?->status == 'expired',
                            'bg-red-50 text-red-600' =>
                                $employee?->latestEmployeeContract?->status == 'terminated',
                            'bg-orange-50 text-orange-600' =>
                                $employee?->latestEmployeeContract?->status == null,
                        ])>
                            {{ $employee?->latestEmployeeContract?->status ?? 'Belum membuat contract' }}
                        </span>

                        @if ($employee?->latestEmployeeContract)
                            @if($employee?->latestEmployeeContract?->status === 'draft')
                            @can('update-contract')
                                <x-wirekit::button type="button" wire:navigate
                                    href="{{ route('contract.edit', [
                                        'employee' => $employee->id,
                                    ]) }}"
                                    class="border border-white bg-sky-500 text-white hover:bg-sky-500">
                                    Edit Contract Draft
                                </x-wirekit::button>
                            @endcan
                            @endif
                            @can('show-contract')
                                <x-wirekit::button type="button" wire:navigate
                                    href="{{ route('contract.show', [
                                        'employee' => $employee->id,
                                        'contract' => $employee->latestEmployeeContract->id,
                                    ]) }}"
                                    class="border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                                    Detail Contract
                                </x-wirekit::button>
                            @endcan
                        @else
                            <x-wirekit::button type="button" wire:navigate
                                href="{{ route('contract.create', $employee->id) }}"
                                class="border border-blue-200 bg-white text-blue-700 hover:bg-blue-50">
                                <x-wirekit::icon name="plus" /> Create Contract
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
                                {{ $employee->latestEmployeeContract->employement_type }}
                            </p>

                        </div>

                        {{-- Contract Number --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Nomor Kontrak
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $employee->latestEmployeeContract->contract_number }}
                            </p>

                        </div>

                        {{-- Start Date --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Tanggal Mulai
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $employee->latestEmployeeContract->start_date }}
                            </p>

                        </div>

                        {{-- End Date --}}
                        <div>

                            <span class="text-xs font-medium text-slate-400">
                                Tanggal Berakhir
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $employee->latestEmployeeContract->end_date ?? 'Pegawai Tetap' }}
                            </p>

                        </div>

                    </div>


                    {{-- Salary --}}
                    <div class="mt-6 border-t border-slate-100 pt-6">

                        <span class="text-xs font-medium text-slate-400">
                            Gaji Pokok / hari
                        </span>

                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            Rp {{ number_format($employee->latestEmployeeContract->salary_daily) }}
                        </p>

                    </div>
                @endif

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>

{{-- =====================================================
    LEAVE ENTITLEMENTS
====================================================== --}}
<x-wirekit::card>

    <x-wirekit::card.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Jatah Cuti
            </h2>

            <p class="text-sm text-slate-500">
                Informasi jatah dan sisa cuti karyawan pada tahun {{ now()->year }}.
            </p>

        </x-wirekit::stack>

    </x-wirekit::card.header>


    <x-wirekit::card.body>

        @if ($leaveEntitlements->isNotEmpty())

            <div class="wk-scrollbar max-h-[320px] overflow-auto pr-2">

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                    @foreach ($leaveEntitlements as $leave)

                        @php
                            $used = $this->usedLeave($leave->leave_type_id);
                            $pending = $this->pendingLeave($leave->leave_type_id);
                            $remaining = $this->remainingLeave($leave);
                        @endphp

                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                            <div class="flex items-start justify-between gap-4">

                                <div class="flex min-w-0 items-center gap-3">

                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                                        <x-wirekit::icon
                                            name="calendar"
                                            class="size-5 text-sky-500"
                                        />
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-medium text-slate-900">
                                            {{ $leave->leaveType?->name ?? 'Jenis Cuti' }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            Jatah {{ $leave->days }} hari
                                        </p>

                                    </div>

                                </div>


                                <div class="shrink-0 text-right">

                                    <p class="text-lg font-semibold text-slate-900">
                                        {{ $remaining }}
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Hari tersisa
                                    </p>

                                </div>

                            </div>


                            <div class="mt-4 border-t border-slate-200 pt-3">

                                <div class="flex items-center justify-between text-xs">

                                    <span class="text-slate-400">
                                        Terpakai
                                    </span>

                                    <span class="font-medium text-slate-700">
                                        {{ $used }} / {{ $leave->days }} Hari
                                    </span>

                                </div>


                                @if ($pending > 0)

                                    <div class="mt-1 flex items-center justify-between text-xs">

                                        <span class="text-slate-400">
                                            Menunggu persetujuan
                                        </span>

                                        <span class="font-medium text-amber-600">
                                            {{ $pending }} Hari
                                        </span>

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @else

            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-4">

                <div class="flex items-start gap-3">

                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">

                        <x-wirekit::icon
                            name="information-circle"
                            class="size-5 text-slate-500"
                        />

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-slate-800">
                            Belum ada jatah cuti
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Contract karyawan ini belum memiliki jatah cuti
                            yang terdaftar.
                        </p>

                    </div>

                </div>

            </div>

        @endif

    </x-wirekit::card.body>

</x-wirekit::card>
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Riwayat Contract
                    </h2>

                    <p class="text-sm text-slate-500">
                        Riwayat kontrak kerja karyawan dari waktu ke waktu.
                    </p>
                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::timeline>

                   @foreach ($employee->employeeContract as $contract)

    <x-wirekit::timeline.item
        :icon="$contract->status === 'active'
            ? 'check'
            : ($contract->status === 'terminated'
                ? 'close'
                : 'warning')"
        :intent="$contract->status === 'active'
            ? 'success'
            : ($contract->status === 'terminated'
                ? 'danger'
                : 'warning')"
        :time="$contract->start_date->format('d M Y') . ' — ' . ($contract->end_date
            ? $contract->end_date->format('d M Y')
            : 'Sekarang')"
    >

        <x-slot:title>
            {{ $contract->contract_number }}
        </x-slot:title>

        <div class="mt-1 space-y-1">

            <p class="text-sm text-slate-700">
                {{ $contract->position_name ?? 'Belum diketahui' }}
                ·
                {{ $contract->employement_type }}
            </p>

            <p class="text-xs text-slate-500">
                @switch($contract->status)
                    @case('active')
                        Kontrak aktif
                        @break

                    @case('terminated')
                        Kontrak dihentikan
                        @break

                    @case('expired')
                        Kontrak telah berakhir
                        @break

                    @default
                        {{ ucfirst($contract->status) }}
                @endswitch
            </p>

        </div>

        <div class="mt-3">

            @can('show-contract')
                <x-wirekit::button
                    type="button"
                    variant="outline"
                    class="px-3 py-1.5 text-xs"
                    href="{{ route('contract.show', [
                        'employee' => $employee->id,
                        'contract' => $contract->id,
                    ]) }}"
                    wire:navigate
                >
                    Detail Contract
                </x-wirekit::button>
            @endcan



        </div>

    </x-wirekit::timeline.item>

@endforeach

                </x-wirekit::timeline>

            </x-wirekit::card.body>

        </x-wirekit::card>


    <livewire:components.main.employee.section-attendance-history :employee="$employee" />



    <livewire:components.main.employee.section-absence-history :employee="$employee" />

</x-wirekit::stack>
