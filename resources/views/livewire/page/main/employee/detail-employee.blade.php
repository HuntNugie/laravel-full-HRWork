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
                            @if ($employee->getFirstMediaUrl('avatar'))
                                <img src="{{ $employee->getFirstMediaUrl('avatar') }}"
                                    alt="gambar dari {{ $employee->name }}"
                                    class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                            @else
                                <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                    class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                            @endif
                        </div>

                        {{-- Edit Foto --}}
@can('update-employee')
                        <div class="mt-2">
                            <livewire:components.main.employee.modal-edit-photo :employee="$employee">
                                <x-wirekit::button type="button"
                                    class="border border-slate-200 bg-white text-xs text-slate-700 hover:bg-slate-50">
                                    <x-wirekit::icon name="pencil" />
                                    Edit Foto
                                </x-wirekit::button>
                            </livewire:components.main.employee.modal-edit-photo>
                        </div>
@endcan

                    </div>



                    {{-- Information --}}
                    <x-wirekit::stack gap="1">

                        <h2 class="text-xl font-semibold text-slate-900">
                            {{ $employee->name }}
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

                        </div>

                    </x-wirekit::stack>

                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-2">

@can('update-employee')
                    <x-wirekit::button type="button" href="{{ route('employee.edit', $employee->id) }}" wire:navigate
                        class="border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                        Edit Employee
                    </x-wirekit::button>
@endcan

                    @can('create-contract')
                        <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            href="{{ route('contract.create', $employee->id) }}" wire:navigate>
                            Buat Contract baru
                        </x-wirekit::button>
                    @endcan

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
                        {{ $employee->name }}
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
                        {{ $employee->email }}
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

@can('update-employee')
                    <livewire:components.main.employee.modal-edit-rekening :employee="$employee">
                        <x-wirekit::button class="bg-[#30AFFF] text-white hover:bg-sky-500">
                            Edit Rekening
                        </x-wirekit::button>
                    </livewire:components.main.employee.modal-edit-rekening>
@endcan


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
                        {{ $employee->profile->bankAccount?->bank?->name ?? 'Belum diisi' }}
                    </p>

                </div>

                {{-- Account Number --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Nomor Rekening
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile->bankAccount?->account_number ?? 'Belum diisi' }}
                    </p>

                </div>

                {{-- Account Holder --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Nama Pemilik Rekening
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile->bankAccount?->account_holder ?? 'Belum diisi' }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

    
    <x-wirekit::card><x-wirekit::card.body>
        <h2 class="mb-3 text-lg font-semibold text-slate-900">Riwayat Status Kepegawaian</h2>
        @forelse ($employee->statusHistory->sortByDesc('id') as $history)
            <div class="border-b border-slate-200 py-3 text-sm">
                <strong>{{ $history->old_status ?? '—' }} → {{ $history->new_status }}</strong>
                <span class="ml-2 text-slate-500">{{ $history->effective_date }}</span>
                <p class="text-slate-500">{{ $history->reason }}</p>
            </div>
        @empty
            <p class="text-sm text-slate-500">Belum ada riwayat status.</p>
        @endforelse
    </x-wirekit::card.body></x-wirekit::card>
    @can('show-contract')
        <x-wirekit::card><x-wirekit::card.body>
            <h2 class="mb-3 text-lg font-semibold text-slate-900">Riwayat Kontrak</h2>
            @forelse ($contracts as $contract)
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 py-3 text-sm">
                    <div>
                        <strong>{{ $contract->contract_number }}</strong>
                        <p class="text-slate-500">{{ $contract->position_name }} · {{ $contract->status }} · {{ $contract->start_date->format('d M Y') }} — {{ $contract->end_date?->format('d M Y') ?? 'Tanpa tanggal akhir' }}</p>
                    </div>
                    <div class="flex gap-2">
                        @can('update-contract')
                            @if ($contract->status === 'draft' && $contract->is($employee->latestEmployeeContract))
                                <a wire:navigate class="text-sky-600" href="{{ route('contract.edit', $employee->id) }}">Edit draft</a>
                            @endif
                        @endcan
                        <a wire:navigate class="text-sky-600" href="{{ route('contract.show', [$employee->id, $contract->id]) }}">Detail</a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada kontrak.</p>
            @endforelse
        </x-wirekit::card.body></x-wirekit::card>
    @endcan
</x-wirekit::stack>
