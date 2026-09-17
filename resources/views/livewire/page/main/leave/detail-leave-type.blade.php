<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-start gap-3">


            <x-wirekit::stack gap="xs">

                <a href="{{ route('leave.type.view') }}" wire:navigate
                    class="mb-3 inline-flex items-center text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">

                    ← Kembali ke jenis cuti

                </a>
                <div class="flex items-center gap-3">

                    <h1 class="text-2xl font-semibold text-slate-900">
                        {{ $leaveType->name }}
                    </h1>

                    <x-wirekit::badge intent="{{ $leaveType->status === 'active' ? 'success' : 'danger' }}">
                        {{ $leaveType->status }}
                    </x-wirekit::badge>

                </div>

            </x-wirekit::stack>

        </div>


        {{-- ACTION --}}
        <div class="flex items-center gap-2">

            <livewire:components.main.leave.type-form-edit :leaveType="$leaveType">
                <x-wirekit::button type="button" variant="outline">
                    <x-wirekit::icon name="pencil" />
                    Edit
                </x-wirekit::button>
            </livewire:components.main.leave.type-form-edit>

        </div>

    </div>


    {{-- =====================================================
        INFORMATION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div>
                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Jenis Cuti
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi dasar dan aturan yang berlaku untuk jenis cuti ini.
                </p>
            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Nama --}}
                <div>
                    <p class="text-sm text-slate-500">
                        Nama Cuti
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $leaveType->name }}
                    </p>
                </div>


                {{-- Jatah Minimum --}}
                <div>
                    <p class="text-sm text-slate-500">
                        Jatah Default/Minimum
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $leaveType->default_days }} Hari
                    </p>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Minimum per tahun
                    </p>
                </div>


                {{-- Berlaku --}}
                <div>
                    <p class="text-sm text-slate-500">
                        Berlaku Untuk
                    </p>

                    @if ($leaveType->gender === 'all')
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            Semua Karyawan
                        </p>
                    @elseif ($leaveType->gender === 'male')
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            Laki Laki
                        </p>
                    @else
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            Perempuan
                        </p>
                    @endif
                </div>


                {{-- Status --}}
                <div>
                    <p class="text-sm text-slate-500">
                        Status
                    </p>

                    <div class="mt-1">
                        @if ($leaveType->status === 'active')
                            <x-wirekit::badge intent="success">
                                Aktif
                            </x-wirekit::badge>
                        @else
                            <x-wirekit::badge intent="danger">
                                Tidak aktif
                            </x-wirekit::badge>
                        @endif
                    </div>
                </div>

            </div>


            {{-- Description --}}
            <div class="mt-6 border-t border-slate-200 pt-5">

                <p class="text-sm text-slate-500">
                    Deskripsi
                </p>

                <p class="mt-2 text-sm leading-6 text-slate-700">
                    {{ $leaveType->description }}
                </p>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
    STATISTICS
====================================================== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        {{-- Used by --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                        <x-wirekit::icon name="users" class="size-5 text-sky-500" />
                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Digunakan Oleh
                        </p>

                        <p class="mt-1 text-xl font-semibold text-slate-900">
                            {{ $contractEntitlements->map(fn($entitlement) => $entitlement->employeeContract?->employee_id)->filter()->unique()->count() }}
                            Karyawan
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Active contracts --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-emerald-50">
                        <x-wirekit::icon name="file-text" class="size-5 text-emerald-500" />
                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Kontrak Aktif
                        </p>

                        <p class="mt-1 text-xl font-semibold text-slate-900">
                            {{ $contractEntitlements->filter(fn($entitlement) => $entitlement->employeeContract?->status === 'active')->count() }}
                            Kontrak
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex flex-col gap-1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Karyawan yang Menggunakan Cuti Ini
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar contract karyawan yang memiliki jatah untuk jenis cuti ini.
                    </p>

                </div>


                <div class="w-full sm:w-72">

                    <x-wirekit::input type="text" name="search" placeholder="Cari karyawan atau kontrak" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar max-h-[500px] overflow-auto">

                <x-wirekit::table hoverable table-label="Kontrak yang menggunakan jenis cuti">

                    {{-- =================================================
                    HEADER
                ================================================== --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Karyawan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                No. Kontrak
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Jatah
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status Kontrak
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Periode
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    {{-- =================================================
                    BODY
                ================================================== --}}
                    <x-wirekit::table.body>

                        @forelse ($contractEntitlements as $entitlement)
                            @php
                                $contract = $entitlement->employeeContract;
                                $employee = $contract?->employees;
                            @endphp

                            <x-wirekit::table.row>

                                {{-- KARYAWAN --}}
                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm font-medium text-slate-900">
                                            {{ $employee?->user?->name ?? 'Karyawan tidak ditemukan' }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ $employee?->employee_code ?? '-' }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- NOMOR CONTRACT --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm font-medium text-slate-800">
                                        {{ $contract?->contract_number ?? '-' }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- JATAH --}}
                                <x-wirekit::table.td>

                                    <span class="font-medium text-slate-800">
                                        {{ $entitlement->days }} Hari
                                    </span>

                                </x-wirekit::table.td>


                                {{-- STATUS CONTRACT --}}
                                <x-wirekit::table.td>

                                    @if ($contract?->status === 'active')
                                        <x-wirekit::badge intent="success">
                                            Aktif
                                        </x-wirekit::badge>
                                    @elseif ($contract?->status === 'draft')
                                        <x-wirekit::badge intent="warning">
                                            Draft
                                        </x-wirekit::badge>
                                    @elseif ($contract?->status === 'expired')
                                        <x-wirekit::badge intent="secondary">
                                            Expired
                                        </x-wirekit::badge>
                                    @elseif ($contract?->status === 'terminated')
                                        <x-wirekit::badge intent="danger">
                                            Terminated
                                        </x-wirekit::badge>
                                    @else
                                        <x-wirekit::badge intent="secondary">
                                            {{ $contract?->status ?? 'Tidak diketahui' }}
                                        </x-wirekit::badge>
                                    @endif

                                </x-wirekit::table.td>


                                {{-- PERIODE --}}
                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm text-slate-700">
                                            {{ $contract?->start_date?->translatedFormat('d M Y') ?? '-' }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            s.d.
                                            {{ $contract?->end_date?->translatedFormat('d M Y') ?? 'Sekarang' }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="5">

                                    <div class="py-8 text-center">

                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada contract
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Belum ada karyawan yang memiliki jatah
                                            untuk jenis cuti ini.
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
