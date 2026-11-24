<x-wirekit::stack gap="lg">

    {{-- PAGE HEADER --}}
    <x-wirekit::stack gap="sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#30AFFF]">Dashboard HRD</p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                    {{ $greeting }}, {{ auth()->user()->name }}
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan data karyawan dan kontrak kerja.
                </p>
            </div>

            <x-wirekit::badge intent="neutral" leading-icon="calendar">
                {{ $today->format('d/m/Y') }}
            </x-wirekit::badge>
        </div>
    </x-wirekit::stack>

    {{-- SUMMARY --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @can('view-employee')
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Total Karyawan</span>
                        <x-wirekit::icon name="user" class="size-5 text-sky-600" />
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-slate-900">
                            {{ $employeeSummary['total'] }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Seluruh data karyawan terdaftar
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
        @endcan

        @can('view-employee')
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Karyawan Aktif</span>
                        <x-wirekit::icon name="check-circle" class="size-5 text-emerald-600" />
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-slate-900">
                            {{ $employeeSummary['active'] }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Karyawan dengan status aktif
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
        @endcan

        @can('view-contract')
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Kontrak Aktif</span>
                        <x-wirekit::icon name="calendar" class="size-5 text-sky-600" />
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-slate-900">
                            {{ $contractSummary['active'] }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Kontrak kerja berstatus aktif
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
        @endcan

        @can('view-contract')
        <x-wirekit::card>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-500">Kontrak Mendekati Akhir</span>
                        <x-wirekit::icon name="calendar" class="size-5 text-violet-600" />
                    </div>

                    <div>
                        <p class="text-2xl font-semibold text-slate-900">
                            {{ $expiringCount }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Berakhir dalam 30 hari
                        </p>
                    </div>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
        @endcan

    </div>

    {{-- EMPLOYEE + CONTRACT --}}
    @if ($employeeSummary || $contractSummary)
        <div class="grid gap-6 lg:grid-cols-2">
            @can('view-employee')
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <x-wirekit::stack gap="1">
                                <h2 class="text-lg font-semibold text-slate-900">Status Kepegawaian</h2>
                                <p class="text-sm text-slate-500">Ringkasan status seluruh karyawan.</p>
                            </x-wirekit::stack>
                            <x-wirekit::button href="{{ route('employee.view') }}" size="sm" intent="neutral" surface="outline" wire:navigate>Buka Karyawan</x-wirekit::button>
                        </div>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach (['active' => 'Aktif', 'onboarding' => 'Onboarding', 'inactive' => 'Nonaktif', 'resign' => 'Resign', 'terminated' => 'Terminated'] as $status => $label)
                                <div @class(['rounded-lg border border-slate-200 p-4', 'sm:col-span-2' => $loop->last])>
                                    <p class="text-xs text-slate-400">{{ $label }}</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $employeeSummary[$status] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </x-wirekit::card.body>
                </x-wirekit::card>
            @endcan

            @can('view-contract')
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <x-wirekit::stack gap="1">
                                <h2 class="text-lg font-semibold text-slate-900">Ringkasan Kontrak</h2>
                                <p class="text-sm text-slate-500">Status seluruh kontrak kerja karyawan.</p>
                            </x-wirekit::stack>
                            <x-wirekit::button href="{{ route('contract.view') }}" size="sm" intent="neutral" surface="outline" wire:navigate>Buka Kontrak</x-wirekit::button>
                        </div>
                    </x-wirekit::card.header>
                    <x-wirekit::card.body>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach (['active' => 'Aktif', 'draft' => 'Draft', 'expired' => 'Expired', 'terminated' => 'Terminated', 'total' => 'Total Kontrak'] as $status => $label)
                                <div @class(['rounded-lg border border-slate-200 p-4', 'sm:col-span-2' => $loop->last])>
                                    <p class="text-xs text-slate-400">{{ $label }}</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $contractSummary[$status] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </x-wirekit::card.body>
                </x-wirekit::card>
            @endcan
        </div>
    @endif

    @can('view-contract')
        <x-wirekit::card>
            <x-wirekit::card.header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Kontrak Mendekati Akhir</h2>
                        <p class="text-sm text-slate-500">Kontrak aktif yang akan berakhir dalam 30 hari.</p>
                    </x-wirekit::stack>
                    <x-wirekit::button href="{{ route('contract.view') }}" size="sm" intent="neutral" surface="outline" wire:navigate>Lihat semua</x-wirekit::button>
                </div>
            </x-wirekit::card.header>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="md">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Perlu perhatian</p>
                            <div class="mt-2">
                                <x-wirekit::badge :intent="$expiringCount > 0 ? 'warning' : 'success'" :dot="true">{{ $expiringCount }} kontrak</x-wirekit::badge>
                            </div>
                        </div>
                    </div>
                    @can('show-contract')
                        <x-wirekit::divider />
                        @forelse ($expiring as $contract)
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-slate-800">{{ $contract->employees->name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $contract->contract_number }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <x-wirekit::badge intent="warning">{{ $contract->end_date->format('d/m/Y') }}</x-wirekit::badge>
                                    <x-wirekit::button size="sm" intent="neutral" surface="outline" href="{{ route('contract.show', [$contract->employee_id, $contract->id]) }}" wire:navigate>Detail</x-wirekit::button>
                                </div>
                            </div>
                            @unless ($loop->last)<x-wirekit::divider />@endunless
                        @empty
                            <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4">
                                <p class="text-sm font-medium text-slate-700">Tidak ada kontrak yang mendekati akhir.</p>
                            </div>
                        @endforelse
                    @endcan
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endcan

    @if (!$employeeSummary && !$contractSummary)
        <x-wirekit::card>
            <x-wirekit::card.body>
                <div class="flex items-start gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                        <x-wirekit::icon name="shield-check" class="size-5" />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Ringkasan belum tersedia</p>
                        <p class="mt-1 text-sm leading-6 text-slate-500">Ringkasan karyawan dan kontrak ditampilkan sesuai hak akses akun HRD.</p>
                    </div>
                </div>
            </x-wirekit::card.body>
        </x-wirekit::card>
    @endif

    {{-- QUICK ACCESS --}}
    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Akses Cepat</h2>
                <p class="text-sm text-slate-500">Navigasi ke modul administrasi HRD yang tersedia.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">

                @can('view-employee')
                    <x-wirekit::button
                        href="{{ route('employee.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Data Karyawan
                    </x-wirekit::button>
                @endcan

                @can('view-contract')
                    <x-wirekit::button
                        href="{{ route('contract.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Kontrak Kerja
                    </x-wirekit::button>
                @endcan

                @can('view-position')
                    <x-wirekit::button
                        href="{{ route('position.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Jabatan
                    </x-wirekit::button>
                @endcan

                @can('view-benefit')
                    <x-wirekit::button
                        href="{{ route('benefit.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Benefit
                    </x-wirekit::button>
                @endcan

                @can('view-type-leave')
                    <x-wirekit::button
                        href="{{ route('leave.type.view') }}"
                        class="w-full"
                        intent="neutral"
                        surface="outline"
                        wire:navigate
                    >
                        Jenis Cuti
                    </x-wirekit::button>
                @endcan

            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
