<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('my-contract') }}" wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali ke Contract Saya
        </a>

        <div>

            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-semibold text-slate-900">
                    Detail Contract
                </h1>

                @if ($contract->status === 'active')
                    <x-wirekit::badge intent="success">
                        Aktif
                    </x-wirekit::badge>
                @elseif ($contract->status === 'draft')
                    <x-wirekit::badge intent="warning">
                        Draft
                    </x-wirekit::badge>
                @elseif ($contract->status === 'expired')
                    <x-wirekit::badge intent="secondary">
                        Expired
                    </x-wirekit::badge>
                @elseif ($contract->status === 'terminated')
                    <x-wirekit::badge intent="danger">
                        Terminated
                    </x-wirekit::badge>
                @else
                    <x-wirekit::badge intent="secondary">
                        {{ $contract->status ?? 'Tidak diketahui' }}
                    </x-wirekit::badge>
                @endif

            </div>

            <p class="mt-1 text-sm text-slate-500">
                Informasi lengkap contract kerja Anda.
            </p>

        </div>

    </x-wirekit::stack>


    {{-- =====================================================
        CONTRACT SUMMARY
    ====================================================== --}}
    <x-wirekit::card>

        <div class="overflow-hidden rounded-xl bg-gradient-to-br from-[#E8FAFF] via-white to-[#F1FFF4]">

            <div class="px-6 py-6">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Contract #{{ $contract->contract_number }}
                        </p>

                        <h2 class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $contract->position_name ?? 'Belum diketahui' }}
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">
                            {{ $contract->start_date?->format('d F Y') }}
                            —
                            {{ $contract->end_date?->format('d F Y') ?? 'Pegawai tetap' }}
                        </p>

                    </div>

                </div>


                {{-- Salary --}}
                <div class="mt-6 rounded-2xl border border-white/80 bg-white/70 p-5 shadow-sm backdrop-blur-sm">

                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Gaji Pokok
                            </p>

                            <p class="mt-1 text-2xl font-semibold text-slate-900">
                                Rp{{ number_format($contract->salary_daily ?? 0) }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Per hari kerja
                            </p>

                        </div>


                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-[#92EEFF]/60">

                            <x-wirekit::icon name="banknotes" class="size-5 text-cyan-700" />

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </x-wirekit::card>


    {{-- =====================================================
        CONTRACT INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="xs">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Contract
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi utama dari contract ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">

                {{-- Nomor Contract --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Nomor Contract
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract->contract_number ?? '-' }}
                    </p>

                </div>


                {{-- Status --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status
                    </p>

                    <div class="mt-1">

                        @if ($contract->status === 'active')
                            <x-wirekit::badge intent="success">
                                Aktif
                            </x-wirekit::badge>
                        @elseif ($contract->status === 'draft')
                            <x-wirekit::badge intent="warning">
                                Draft
                            </x-wirekit::badge>
                        @elseif ($contract->status === 'expired')
                            <x-wirekit::badge intent="secondary">
                                Expired
                            </x-wirekit::badge>
                        @elseif ($contract->status === 'terminated')
                            <x-wirekit::badge intent="danger">
                                Terminated
                            </x-wirekit::badge>
                        @else
                            <x-wirekit::badge intent="secondary">
                                {{ $contract->status ?? 'Tidak diketahui' }}
                            </x-wirekit::badge>
                        @endif

                    </div>

                </div>


                {{-- Jenis Contract --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jenis Contract
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract->employement_type ?? 'Tidak diketahui' }}
                    </p>

                </div>


                {{-- Jabatan --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jabatan
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract->position_name ?? 'Tidak diketahui' }}
                    </p>

                </div>


                {{-- Tanggal Mulai --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Mulai
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract->start_date?->format('d F Y') ?? 'Tidak diketahui' }}
                    </p>

                </div>


                {{-- Tanggal Berakhir --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Berakhir
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $contract->end_date?->format('d F Y') ?? 'Pegawai tetap' }}
                    </p>

                </div>


                {{-- Gaji --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Gaji Pokok / Hari
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-900">
                        Rp{{ number_format($contract->salary_daily ?? 0) }}
                    </p>

                </div>


                {{-- Durasi --}}
                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Durasi Contract
                    </p>

                    @if ($contract->end_date)
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            {{ intval(\Carbon\Carbon::parse($contract->start_date)->diffInMonths($contract->end_date)) }}
                            Bulan
                        </p>
                    @else
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            Pegawai tetap
                        </p>
                    @endif

                </div>


                {{-- Catatan --}}
                <div class="sm:col-span-2">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Catatan
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-700">
                        {{ $contract->notes ?? 'Tidak ada catatan.' }}
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

                <h2 class="text-lg font-semibold text-slate-900">
                    Jatah Cuti
                </h2>

                <p class="text-sm text-slate-500">
                    Jenis dan jumlah jatah cuti yang diberikan pada contract ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($contract->contractLeave->isNotEmpty())

                <div class="wk-scrollbar max-h-[360px] overflow-auto pr-2">

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                        @foreach ($contract->contractLeave as $leave)
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
                                                Jatah per tahun
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

                <h2 class="text-lg font-semibold text-slate-900">
                    Benefit
                </h2>

                <p class="text-sm text-slate-500">
                    Benefit yang tercantum pada contract ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($contract->benefits->isNotEmpty())

                <div class="wk-scrollbar max-h-[400px] overflow-auto pr-2">

                    <x-wirekit::stack gap="sm">

                        @foreach ($contract->benefits as $benefit)
                            <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                    <div class="min-w-0">

                                        <p class="text-sm font-medium text-slate-900">
                                            {{ $benefit->name }}
                                        </p>

                                        @if ($benefit->description)
                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                {{ $benefit->description }}
                                            </p>
                                        @endif

                                    </div>


                                    <p class="shrink-0 text-sm font-semibold text-slate-900">
                                        Rp{{ number_format($benefit->pivot->amount ?? 0) }}

                                        <span class="font-normal text-slate-400">
                                            / hari
                                        </span>
                                    </p>

                                </div>

                            </div>
                        @endforeach

                    </x-wirekit::stack>

                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-4">

                    <p class="text-sm text-slate-500">
                        Contract ini tidak memiliki benefit.
                    </p>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
