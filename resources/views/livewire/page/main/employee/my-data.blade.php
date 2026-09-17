<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <div>
            <p class="text-sm font-medium text-[#30AFFF]">
                Akun Saya
            </p>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Data Saya
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Informasi pribadi dan kepegawaian yang tersimpan pada sistem.
            </p>
        </div>

    </x-wirekit::stack>


    {{-- =====================================================
        IDENTITY
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                {{-- AVATAR --}}
                <div
                    class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#92EEFF] ring-1 ring-slate-200">

                    @if ($employee->user->getFirstMediaUrl('avatar'))
                        <img src="{{ $employee->user->getFirstMediaUrl('avatar') }}" alt="{{ $employee->user->name }}"
                            class="size-full object-cover">
                    @else
                        <img src="{{ asset('assets/nonProfile.jpg') }}" alt="" class="size-full object-cover">
                    @endif

                </div>


                <div class="min-w-0">

                    <h2 class="text-xl font-semibold text-slate-900">
                        {{ $employee->user->name }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $employee->employee_code }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">

                        <span @class([
                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                            'bg-emerald-50 text-emerald-600' => $employee->status_employee === 'active',
                            'bg-red-50 text-red-600' => in_array($employee->status_employee, [
                                'inactive',
                                'resign',
                            ]),
                            'bg-yellow-50 text-yellow-600' =>
                                $employee->status_employee === 'onboarding',
                            'bg-slate-50 text-slate-600' => $employee->status_employee === 'terminated',
                            'bg-sky-50 text-sky-600' => $employee->status_employee === null,
                        ])>
                            {{ $employee->status_employee ?? 'Belum diketahui' }}
                        </span>

                        <span
                            class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-600">
                            {{ $employee->position?->name ?? 'Belum diketahui' }}
                        </span>

                        <span
                            class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                            {{ $employee->team?->divisi?->name ?? 'Belum diketahui' }}
                        </span>

                    </div>

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
                    Data identitas pribadi yang tersimpan pada sistem.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-x-8 gap-y-6 md:grid-cols-2">

                {{-- Nama --}}
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
                        {{ $employee->profile?->nik ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Tempat Lahir --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Tempat Lahir
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile?->birth_address ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Tanggal Lahir --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Tanggal Lahir
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile?->birth_date?->format('d F Y') ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Gender --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Jenis Kelamin
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        @if ($employee->profile?->gender === 'male')
                            Laki-laki
                        @elseif ($employee->profile?->gender === 'female')
                            Perempuan
                        @else
                            Belum diketahui
                        @endif
                    </p>
                </div>


                {{-- Telepon --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Nomor Telepon
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile?->phone_number ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Email --}}
                <div class="md:col-span-2">

                    <span class="text-xs font-medium text-slate-400">
                        Email
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800 break-all">
                        {{ $employee->user->email }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        ADDRESS
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Alamat
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi alamat tempat tinggal yang tersimpan pada sistem.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Provinsi --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Provinsi
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile?->addressProfile?->village?->district?->regency?->province?->name ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Kabupaten --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Kabupaten/Kota
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile?->addressProfile?->village?->district?->regency?->name ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Kecamatan --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Kecamatan
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile?->addressProfile?->village?->district?->name ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Desa --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Desa
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->profile?->addressProfile?->village?->name ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Alamat Lengkap --}}
                <div class="sm:col-span-2 lg:col-span-4">

                    <span class="text-xs font-medium text-slate-400">
                        Alamat Lengkap
                    </span>

                    <p class="mt-1 text-sm font-medium leading-6 text-slate-800">
                        {{ $employee->profile?->addressProfile?->full_address ?? 'Belum diketahui' }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        EMPLOYMENT INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Kepegawaian
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi posisi dan penempatan kerja Anda.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                {{-- Employee Code --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Nomor Karyawan
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->employee_code }}
                    </p>
                </div>


                {{-- Position --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Jabatan
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->position?->name ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Team --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Team
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->team?->name ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Division --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Divisi
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->team?->divisi?->name ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Employee Status --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Status Karyawan
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->status_employee ?? 'Belum diketahui' }}
                    </p>
                </div>


                {{-- Supervisor --}}
                <div>
                    <span class="text-xs font-medium text-slate-400">
                        Supervisor
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->supervisorTeam?->user?->name ?? 'Belum diketahui' }}
                    </p>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        CONTRACT
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Contract Aktif
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi contract kerja yang sedang digunakan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($employee->latestEmployeeContract)
                @php
                    $contract = $employee->latestEmployeeContract;
                @endphp

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                    {{-- Nomor --}}
                    <div>
                        <span class="text-xs font-medium text-slate-400">
                            Nomor Contract
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $contract->contract_number }}
                        </p>
                    </div>


                    {{-- Jenis --}}
                    <div>
                        <span class="text-xs font-medium text-slate-400">
                            Jenis Contract
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $contract->employement_type }}
                        </p>
                    </div>


                    {{-- Position --}}
                    <div>
                        <span class="text-xs font-medium text-slate-400">
                            Jabatan
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $contract->position_name }}
                        </p>
                    </div>


                    {{-- Start --}}
                    <div>
                        <span class="text-xs font-medium text-slate-400">
                            Tanggal Mulai
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $contract->start_date?->format('d F Y') ?? 'Belum diketahui' }}
                        </p>
                    </div>


                    {{-- End --}}
                    <div>
                        <span class="text-xs font-medium text-slate-400">
                            Tanggal Berakhir
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $contract->end_date?->format('d F Y') ?? 'Pegawai tetap' }}
                        </p>
                    </div>


                    {{-- Salary --}}
                    <div>
                        <span class="text-xs font-medium text-slate-400">
                            Gaji Pokok / Hari
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            Rp{{ number_format($contract->salary_daily ?? 0) }}
                        </p>
                    </div>

                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-4">

                    <p class="text-sm font-medium text-slate-700">
                        Belum memiliki contract.
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi contract belum tersedia.
                    </p>

                </div>
            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>


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
                    Jatah cuti yang diberikan berdasarkan contract.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($employee->latestEmployeeContract?->contractLeave?->isNotEmpty())

                <div class="wk-scrollbar max-h-[360px] overflow-auto pr-2">

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                        @foreach ($employee->latestEmployeeContract->contractLeave as $leave)
                            @php
                                $used = $employee
                                    ->leaveRequest()
                                    ->where('leave_type_id', $leave->leave_type_id)
                                    ->where('status', 'approved')
                                    ->whereYear('start_date', now()->year)
                                    ->sum('total_days');

                                $pending = $employee
                                    ->leaveRequest()
                                    ->where('leave_type_id', $leave->leave_type_id)
                                    ->where('status', 'pending')
                                    ->whereYear('start_date', now()->year)
                                    ->sum('total_days');

                                $remaining = max(0, $leave->days - $used - $pending);
                            @endphp

                            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-medium text-slate-900">
                                            {{ $leave->leaveType?->name ?? 'Jenis Cuti' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Jatah {{ $leave->days }} hari
                                        </p>

                                    </div>


                                    <div class="shrink-0 text-right">

                                        <p class="text-lg font-semibold text-slate-900">
                                            {{ $remaining }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            tersisa
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
                                                Menunggu
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

                    <p class="text-sm font-medium text-slate-700">
                        Belum ada jatah cuti.
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Contract Anda belum memiliki jatah cuti yang terdaftar.
                    </p>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        BENEFITS
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Benefit
                </h2>

                <p class="text-sm text-slate-500">
                    Benefit yang tercantum pada contract Anda.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($employee->latestEmployeeContract?->benefits?->isNotEmpty())

                <x-wirekit::stack gap="sm">

                    @foreach ($employee->latestEmployeeContract->benefits as $benefit)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                            <div class="flex items-center justify-between gap-4">

                                <div>

                                    <p class="text-sm font-medium text-slate-900">
                                        {{ $benefit->name }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Per hari kerja
                                    </p>

                                </div>

                                <p class="text-sm font-semibold text-slate-900">
                                    Rp{{ number_format($benefit->pivot->amount ?? 0) }}
                                </p>

                            </div>

                        </div>
                    @endforeach

                </x-wirekit::stack>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-4">

                    <p class="text-sm text-slate-500">
                        Tidak ada benefit pada contract Anda.
                    </p>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>


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

            @if ($employee->profile?->bankAccount)
                <div class="grid gap-6 md:grid-cols-3">

                    <div>

                        <span class="text-xs font-medium text-slate-400">
                            Bank
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $employee->profile->bankAccount->bank?->name ?? 'Belum diketahui' }}
                        </p>

                    </div>


                    <div>

                        <span class="text-xs font-medium text-slate-400">
                            Nomor Rekening
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $employee->profile->bankAccount->account_number ?? 'Belum diketahui' }}
                        </p>

                    </div>


                    <div>

                        <span class="text-xs font-medium text-slate-400">
                            Nama Pemilik Rekening
                        </span>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $employee->profile->bankAccount->account_holder ?? 'Belum diketahui' }}
                        </p>

                    </div>

                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-4">

                    <p class="text-sm text-slate-500">
                        Belum ada rekening bank yang terdaftar.
                    </p>

                </div>
            @endif

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

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Email
                    </span>

                    <p class="mt-1 break-all text-sm font-medium text-slate-800">
                        {{ $employee->user->email }}
                    </p>

                </div>


                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Role
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->getRoleNames()->implode(', ') ?: 'Belum ada role' }}
                    </p>

                </div>


                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Status Akun
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->status }}
                    </p>

                </div>


                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Terdaftar
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $employee->user->created_at?->format('d F Y') ?? '-' }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
