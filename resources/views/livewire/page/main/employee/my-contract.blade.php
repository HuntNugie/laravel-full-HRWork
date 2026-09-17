<div class="space-y-6">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <div>
            <p class="text-sm font-medium text-[#30AFFF]">
                Akun Saya
            </p>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Contract Saya
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Informasi contract kerja dan riwayat contract Anda.
            </p>
        </div>

    </x-wirekit::stack>


    {{-- =====================================================
        ACTIVE CONTRACT
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="text-lg font-semibold text-slate-900">
                            Contract Aktif
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Contract yang sedang berlaku saat ini.
                        </p>

                    </div>


                    @if ($activeContract)

                        <x-wirekit::badge intent="success">
                            Aktif
                        </x-wirekit::badge>

                    @endif

                </div>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($activeContract)

                <div class="space-y-6">

                    {{-- =================================================
                        CONTRACT SUMMARY
                    ================================================== --}}
                    <div class="rounded-2xl bg-gradient-to-br from-[#E8FAFF] via-white to-[#F1FFF4] p-5">

                        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">

                            <div>

                                <p class="text-sm font-medium text-slate-500">
                                    Contract #{{ $activeContract->contract_number }}
                                </p>

                                <h3 class="mt-1 text-2xl font-semibold text-slate-900">
                                    {{ $activeContract->position_name ?? 'Belum diketahui' }}
                                </h3>

                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $activeContract->start_date?->format('d F Y') }}
                                    —
                                    {{ $activeContract->end_date?->format('d F Y') ?? 'Pegawai tetap' }}
                                </p>

                            </div>

<x-wirekit::button
    type="button"
    variant="outline"
    size="sm"
    href="{{ route('my-contract.show', $activeContract->id) }}"
    wire:navigate
>
    Detail Contract
</x-wirekit::button>
                        </div>

                    </div>


                    {{-- =================================================
                        CONTRACT INFORMATION
                    ================================================== --}}
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                        {{-- Nomor Contract --}}
                        <div>
                            <span class="text-xs font-medium text-slate-400">
                                Nomor Contract
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $activeContract->contract_number }}
                            </p>
                        </div>


                        {{-- Jenis Contract --}}
                        <div>
                            <span class="text-xs font-medium text-slate-400">
                                Jenis Contract
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $activeContract->employement_type }}
                            </p>
                        </div>


                        {{-- Jabatan --}}
                        <div>
                            <span class="text-xs font-medium text-slate-400">
                                Jabatan
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $activeContract->position_name ?? 'Belum diketahui' }}
                            </p>
                        </div>


                        {{-- Tanggal Mulai --}}
                        <div>
                            <span class="text-xs font-medium text-slate-400">
                                Tanggal Mulai
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $activeContract->start_date?->format('d F Y') ?? '-' }}
                            </p>
                        </div>


                        {{-- Tanggal Berakhir --}}
                        <div>
                            <span class="text-xs font-medium text-slate-400">
                                Tanggal Berakhir
                            </span>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $activeContract->end_date?->format('d F Y') ?? 'Pegawai tetap' }}
                            </p>
                        </div>


                        {{-- Gaji --}}
                        <div>
                            <span class="text-xs font-medium text-slate-400">
                                Gaji Pokok / Hari
                            </span>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                Rp{{ number_format($activeContract->salary_daily ?? 0) }}
                            </p>
                        </div>

                    </div>


                    {{-- =================================================
                        JATAH CUTI
                    ================================================== --}}
                    <div class="border-t border-slate-100 pt-6">

                        <div class="mb-4">

                            <h3 class="text-base font-semibold text-slate-900">
                                Jatah Cuti
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Jatah cuti yang diberikan pada contract ini.
                            </p>

                        </div>


                        @if ($activeContract->contractLeave->isNotEmpty())

                            <div class="wk-scrollbar max-h-[320px] overflow-auto pr-2">

                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                                    @foreach ($activeContract->contractLeave as $leave)

                                        <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                                            <div class="flex items-center justify-between gap-4">

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
                                            Contract ini belum memiliki jatah cuti yang terdaftar.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                    </div>


                    {{-- =================================================
                        BENEFIT
                    ================================================== --}}
                    <div class="border-t border-slate-100 pt-6">

                        <div class="mb-4">

                            <h3 class="text-base font-semibold text-slate-900">
                                Benefit
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Benefit yang tercantum pada contract ini.
                            </p>

                        </div>


                        @if ($activeContract->benefits->isNotEmpty())

                            <div class="space-y-3">

                                @foreach ($activeContract->benefits as $benefit)

                                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                                        <div class="flex items-center justify-between gap-4">

                                            <div>

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

                            </div>

                        @else

                            <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-4">

                                <p class="text-sm text-slate-500">
                                    Contract ini tidak memiliki benefit.
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            @else

                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-6">

                    <div class="flex items-start gap-3">

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100">

                            <x-wirekit::icon
                                name="document"
                                class="size-5 text-slate-500"
                            />

                        </div>


                        <div>

                            <p class="text-sm font-semibold text-slate-800">
                                Belum memiliki contract aktif
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Saat ini tidak terdapat contract yang berstatus aktif.
                            </p>

                        </div>

                    </div>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        CONTRACT HISTORY
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Riwayat Contract
                </h2>

                <p class="text-sm text-slate-500">
                    Riwayat contract kerja Anda sebelumnya.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @php
                $previousContracts = $contracts->filter(
                    fn ($contract) =>
                        !$activeContract ||
                        $contract->id !== $activeContract->id
                );
            @endphp


            @if ($previousContracts->isNotEmpty())

                <div class="wk-scrollbar max-h-[600px] overflow-auto pr-2">

                    <x-wirekit::timeline>

                        @foreach ($previousContracts as $contract)

                            <x-wirekit::timeline.item
                                :icon="$contract->status === 'terminated'
                                    ? 'close'
                                    : ($contract->status === 'expired'
                                        ? 'warning'
                                        : ($contract->status === 'active'
                                            ? 'check'
                                            : 'document'))"
                                :intent="$contract->status === 'terminated'
                                    ? 'danger'
                                    : ($contract->status === 'expired'
                                        ? 'warning'
                                        : ($contract->status === 'active'
                                            ? 'success'
                                            : 'primary'))"
                                :time="$contract->start_date?->format('d M Y') . ' — ' . ($contract->end_date?->format('d M Y') ?? 'Sekarang')"
                            >

                                <x-slot:title>
                                    {{ $contract->contract_number }}
                                </x-slot:title>


                                <div class="mt-2 space-y-3">

                                    <div>

                                        <p class="text-sm font-medium text-slate-800">
                                            {{ $contract->position_name ?? 'Belum diketahui' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $contract->employement_type }}
                                        </p>

                                    </div>


                                    {{-- STATUS --}}
                                    <div>

                                        @if ($contract->status === 'active')

                                            <x-wirekit::badge intent="success">
                                                Aktif
                                            </x-wirekit::badge>

                                        @elseif ($contract->status === 'terminated')

                                            <x-wirekit::badge intent="danger">
                                                Terminated
                                            </x-wirekit::badge>

                                        @elseif ($contract->status === 'expired')

                                            <x-wirekit::badge intent="secondary">
                                                Expired
                                            </x-wirekit::badge>

                                        @elseif ($contract->status === 'draft')

                                            <x-wirekit::badge intent="warning">
                                                Draft
                                            </x-wirekit::badge>

                                        @else

                                            <x-wirekit::badge intent="secondary">
                                                {{ $contract->status ?? 'Tidak diketahui' }}
                                            </x-wirekit::badge>

                                        @endif

                                    </div>


                                    {{-- JATAH CUTI --}}
                                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Jatah Cuti
                                        </p>


                                        @if ($contract->contractLeave->isNotEmpty())

                                            <div class="mt-3 space-y-2">

                                                @foreach ($contract->contractLeave as $leave)

                                                    <div class="flex items-center justify-between gap-4">

                                                        <span class="text-sm text-slate-700">
                                                            {{ $leave->leaveType?->name ?? 'Jenis Cuti' }}
                                                        </span>

                                                        <span class="text-sm font-semibold text-slate-900">
                                                            {{ $leave->days }} Hari
                                                        </span>

                                                    </div>

                                                @endforeach

                                            </div>

                                        @else

                                            <p class="mt-2 text-sm text-slate-500">
                                                Tidak ada jatah cuti pada contract ini.
                                            </p>

                                        @endif

                                    </div>


                                    {{-- ACTION --}}
                                    <div>

                                <x-wirekit::button
    type="button"
    variant="outline"
    size="sm"
    href="{{ route('my-contract.show', $contract->id) }}"
    wire:navigate
>
    Detail Contract
</x-wirekit::button>

                                    </div>

                                </div>

                            </x-wirekit::timeline.item>

                        @endforeach

                    </x-wirekit::timeline>

                </div>

            @else

                <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/70 p-6 text-center">

                    <p class="text-sm font-medium text-slate-700">
                        Belum ada riwayat contract
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Belum terdapat contract sebelumnya pada data Anda.
                    </p>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
