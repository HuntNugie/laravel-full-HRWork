<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a x-on:click="window.history.back()" wire:navigate
            class="inline-flex w-fit cursor-pointer items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                Detail Contract
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Informasi lengkap kontrak karyawan.
            </p>
        </div>

    </x-wirekit::stack>


    {{-- =====================================================
        EMPLOYEE INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>
            <x-wirekit::stack gap="xs">

                <h2 class="text-base font-semibold text-slate-900">
                    Informasi Karyawan
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi karyawan yang terkait dengan contract ini.
                </p>

            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <div class="flex items-start gap-4">

                {{-- AVATAR --}}
                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-slate-100">

                    @if ($employee->user->getFirstMediaUrl('avatar'))
                        <img src="{{ $employee->user->getFirstMediaUrl('avatar') }}"
                            alt="gambar dari {{ $employee->user->name }}"
                            class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                    @else
                        <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                            class="block size-full rounded-full bg-[#92EEFF]/60 object-cover">
                    @endif

                </div>


                <div class="min-w-0 flex-1">

                    <h3 class="text-base font-semibold text-slate-900">
                        {{ $employee->user->name }}
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        NIK : {{ $employee->profile->nik }}
                    </p>


                    <div class="mt-4 grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">

                        {{-- JABATAN --}}
                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Jabatan
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-900">
                                {{ $employee?->position->name ?? 'Belum ada jabatan' }}
                            </p>

                        </div>


                        {{-- TEAM --}}
                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Team
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-900">
                                {{ $employee?->team->name ?? 'Belum ada team' }}
                            </p>

                        </div>


                        {{-- DIVISI --}}
                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Divisi
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-900">
                                {{ $employee?->team?->divisi->name ?? 'Tidak ada divisi' }}
                            </p>

                        </div>


                        {{-- STATUS PEGAWAI --}}
                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Status Kepegawaian
                            </p>

                            <span @class([
                                'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                'bg-emerald-50 text-emerald-600' =>
                                    $employee?->status_employee === 'active',
                                'bg-red-50 text-red-600' => in_array($employee?->status_employee, [
                                    'inactive',
                                    'resign',
                                ]),
                                'bg-yellow-50 text-yellow-600' =>
                                    $employee?->status_employee === 'onboarding',
                                'bg-slate-50 text-slate-600' =>
                                    $employee?->status_employee === 'terminated',
                                'bg-sky-50 text-sky-600' => $employee?->status_employee === null,
                            ])>
                                {{ $employee?->status_employee ?? 'Belum di ketahui' }}
                            </span>

                        </div>


                        {{-- JENIS KELAMIN --}}
                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Jenis kelamin
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-900">
                                {{ strtoupper($employee->profile->gender) }}
                            </p>

                        </div>

                    </div>

                </div>


                <x-wirekit::button variant="outline" wire:navigate href="{{ route('employee.show', $employee->id) }}"
                    class="shrink-0">
                    Lihat Employee
                </x-wirekit::button>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        CONTRACT SUMMARY
    ====================================================== --}}
    <x-wirekit::card>

        <div class="overflow-hidden rounded-xl bg-gradient-to-br from-[#E8FAFF] via-white to-[#F1FFF4]">

            <div class="px-6 py-6">

                <x-wirekit::stack gap="lg">

                    <x-wirekit::row justify="between" align="start" gap="md">

                        <x-wirekit::stack gap="xs">

                            <div class="flex items-center gap-2">

                                <p class="text-sm font-medium text-slate-500">
                                    Contract #{{ $contract->contract_number }}
                                </p>

                                <span @class([
                                    'rounded-full px-2.5 py-1 text-xs font-medium',
                                    'bg-emerald-50 text-emerald-700' => $contract?->status === 'active',
                                    'bg-red-50 text-red-700' => $contract?->status === 'terminated',
                                    'bg-yellow-50 text-yellow-700' => $contract?->status === 'expired',
                                    'bg-slate-50 text-slate-700' => $contract?->status === 'draft',
                                    'bg-sky-50 text-sky-700' => $contract?->status === null,
                                ])>
                                    {{ $contract?->status ?? 'Belum di ketahui' }}
                                </span>

                            </div>

                            <h2 class="text-2xl font-semibold text-slate-900">
                                {{ $contract?->position_name }}
                            </h2>

                            <p class="text-sm text-slate-500">
                                {{ $contract?->start_date->format('d F Y') }}
                                —
                                {{ $contract?->end_date ?? 'Pegawai tetap' }}
                            </p>

                        </x-wirekit::stack>


                        <x-wirekit::button intent="danger"
                            href="{{ route('print.contract.employee', [
                                'employee' => $contract->employees->id,
                                'contract' => $contract->id,
                            ]) }}"
                            target="_blank" rel="noopener noreferrer">
                            <x-wirekit::icon name="archive" />
                            Cetak Contract
                        </x-wirekit::button>

                    </x-wirekit::row>


                    <div class="rounded-2xl border border-white/80 bg-white/70 p-5 shadow-sm backdrop-blur-sm">

                        <x-wirekit::row justify="between" align="center" gap="md">

                            <div>

                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Gaji Harian
                                </p>

                                <p class="mt-1 text-2xl font-semibold text-slate-900">
                                    Rp{{ number_format($contract?->salary_daily ?? 0) }}
                                </p>

                            </div>


                            <div class="flex size-11 items-center justify-center rounded-xl bg-[#92EEFF]/60">

                                <x-wirekit::icon name="banknotes" class="size-5 text-cyan-700" />

                            </div>

                        </x-wirekit::row>

                    </div>

                </x-wirekit::stack>

            </div>

        </div>

    </x-wirekit::card>


    {{-- =====================================================
        CONTRACT INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="xs">

                <h2 class="text-base font-semibold text-slate-900">
                    Informasi Contract
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi utama dari contract yang sedang berlaku.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">

                {{-- NOMOR CONTRACT --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Nomor Contract
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract?->contract_number }}
                    </p>

                </div>


                {{-- STATUS --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status
                    </p>

                    <span @class([
                        'mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium',
                        'bg-emerald-50 text-emerald-700' => $contract?->status === 'active',
                        'bg-red-50 text-red-700' => $contract?->status === 'terminated',
                        'bg-yellow-50 text-yellow-700' => $contract?->status === 'expired',
                        'bg-slate-50 text-slate-700' => $contract?->status === 'draft',
                        'bg-sky-50 text-sky-700' => $contract?->status === null,
                    ])>
                        {{ $contract?->status ?? 'Belum di ketahui' }}
                    </span>

                </div>


                {{-- JENIS CONTRACT --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jenis Contract
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract?->employement_type ?? 'Tidak di ketahui' }}
                    </p>

                </div>


                {{-- JABATAN --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jabatan
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract?->position_name ?? 'Tidak di ketahui' }}
                    </p>

                </div>


                {{-- TANGGAL MULAI --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Mulai
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract?->start_date->format('d F Y') ?? 'Tidak di ketahui' }}
                    </p>

                </div>


                {{-- TANGGAL BERAKHIR --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Berakhir
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract?->end_date?->format('d F Y') ?? 'Pegawai tetap' }}
                    </p>

                </div>


                {{-- GAJI HARIAN --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Gaji Harian
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        Rp{{ number_format($contract?->salary_daily ?? 0) }}
                    </p>

                </div>


                {{-- DURASI --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Durasi Contract
                    </p>

                    @if ($contract?->end_date)
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ intval(Carbon\Carbon::parse($contract?->start_date)->diffInMonths($contract?->end_date)) }}
                            Bulan
                        </p>
                    @else
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            Pegawai tetap
                        </p>
                    @endif

                </div>


                {{-- NOTES --}}
                <div class="sm:col-span-2">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Catatan
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract?->notes ?? 'Tidak ada notes' }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        LEAVE ENTITLEMENTS
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="xs">

                <h2 class="text-base font-semibold text-slate-900">
                    Jatah Cuti
                </h2>

                <p class="text-sm text-slate-500">
                    Jenis cuti dan jumlah jatah cuti yang diberikan pada contract ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($leaveEntitlements->isNotEmpty())

                <div class="wk-scrollbar max-h-[360px] overflow-auto pr-2">

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                        @foreach ($leaveEntitlements as $leave)
                            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex min-w-0 items-center gap-3">

                                        <div
                                            class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50">

                                            <x-wirekit::icon name="calendar" class="size-5 text-sky-500" />

                                        </div>


                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-medium text-slate-900">
                                                {{ $leave->leaveType?->name ?? 'Jenis Cuti' }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                Jatah cuti tahunan
                                            </p>

                                        </div>

                                    </div>


                                    <div class="shrink-0 text-right">

                                        <p class="text-lg font-semibold text-slate-900">
                                            {{ $leave->days }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            Hari
                                        </p>

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">

                            <x-wirekit::icon name="information-circle" class="size-5 text-slate-500" />

                        </div>


                        <div>

                            <p class="text-sm font-semibold text-slate-800">
                                Belum ada jatah cuti
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Contract ini belum memiliki jatah cuti yang terdaftar.
                            </p>

                        </div>

                    </div>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        BENEFITS
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="xs">

                <h2 class="text-base font-semibold text-slate-900">
                    Benefit
                </h2>

                <p class="text-sm text-slate-500">
                    Benefit yang tercantum dalam contract ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <x-wirekit::stack gap="sm">

                @if (count($benefits))

                    @foreach ($benefits as $benefit)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                            <x-wirekit::row justify="between" align="center" gap="md">

                                <x-wirekit::stack gap="xs">

                                    <p class="text-sm font-medium text-slate-900">
                                        {{ $benefit->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Per hari kerja
                                    </p>

                                </x-wirekit::stack>


                                <p class="text-sm font-semibold text-slate-900">
                                    Rp{{ number_format($benefit->pivot->amount) }}
                                </p>

                            </x-wirekit::row>

                        </div>
                    @endforeach


                    {{-- TOTAL --}}
                    <div class="rounded-xl border border-[#92EEFF]/50 bg-[#92EEFF]/20 p-4">

                        <x-wirekit::row justify="between" align="center">

                            <p class="text-sm font-medium text-slate-900">
                                Total Benefit per Hari
                            </p>

                            <p class="text-base font-semibold text-slate-900">
                                Rp{{ number_format($this->totalBenefits) }}
                            </p>

                        </x-wirekit::row>

                    </div>
                @else
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                        <p class="text-sm text-slate-500">
                            Contract ini tidak mendapatkan tunjangan.
                        </p>

                    </div>

                @endif

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
