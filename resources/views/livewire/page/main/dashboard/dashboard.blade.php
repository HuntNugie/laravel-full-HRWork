<x-wirekit::stack gap="lg">
    <div class="relative overflow-hidden rounded-2xl border border-blue-700 bg-blue-700 px-6 py-7 shadow-sm sm:px-8">
        <div class="pointer-events-none absolute -right-20 -top-24 size-64 rounded-full bg-cyan-300/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-blue-300/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <x-wirekit::stack gap="sm">
                <span class="text-sm font-semibold text-cyan-100">{{ $admin ? 'System Administration' : 'Human Resources' }}</span>
                <div class="space-y-2">
                    <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
                        Selamat datang, {{ auth()->user()->name }}.
                    </h1>
                    <p class="max-w-2xl text-sm leading-6 text-slate-300">
                        {{ $admin ? 'Pantau akun dan pengaturan hak akses HRD dalam satu dashboard.' : 'Pantau data karyawan dan administrasi kontrak kerja dalam satu dashboard.' }}
                    </p>
                </div>
            </x-wirekit::stack>
            <div class="rounded-xl border border-white/20 bg-white/10 px-4 py-3">
                <p class="text-xs font-medium uppercase tracking-wide text-blue-100">Hari ini</p>
                <p class="mt-1 text-sm font-semibold text-white">{{ now()->format('d M Y') }}</p>
            </div>
        </div>
    </div>
    @if ($metrics)
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($metrics as $label => $count)
                <x-wirekit::card>
                    <x-wirekit::card.body>
                        <x-wirekit::stack gap="sm">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-500">{{ $label }}</span>
                                <span @class(['size-2.5 rounded-full', 'bg-[#30AFFF]' => $loop->index % 3 === 0, 'bg-emerald-400' => $loop->index % 3 === 1, 'bg-amber-400' => $loop->index % 3 === 2])></span>
                            </div>
                            <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $count }}</p>
                            <span class="text-xs text-slate-400">{{ $admin ? 'Ringkasan akun HRD' : 'Ringkasan kepegawaian' }}</span>
                        </x-wirekit::stack>
                    </x-wirekit::card.body>
                </x-wirekit::card>
            @endforeach
        </div>
    @else
        <x-wirekit::card><x-wirekit::card.body>
            <div class="flex flex-col items-center gap-3 py-6 text-center">
                <x-wirekit::icon name="shield-check" class="size-8 text-slate-400" />
                <h2 class="font-semibold text-slate-900">Hak akses belum tersedia</h2>
                <p class="text-sm text-slate-500">Hubungi Administrator untuk memperoleh hak akses operasional.</p>
            </div>
        </x-wirekit::card.body></x-wirekit::card>
    @endif
    <div class="grid items-start gap-6 xl:grid-cols-3">
        @if ($admin)
            <x-wirekit::card class="xl:col-span-2">
                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Account & Access</h2>
                        <p class="text-sm text-slate-500">Ringkasan distribusi status akun HRD.</p>
                    </x-wirekit::stack>
                </x-wirekit::card.header>
                <x-wirekit::card.body>
                    <x-wirekit::stack gap="lg">
                        @foreach (['Aktif' => 'bg-emerald-400', 'Pending' => 'bg-amber-400', 'Nonaktif' => 'bg-red-400'] as $label => $color)
                            @php($count = $metrics[$label] ?? 0)
                            <x-wirekit::stack gap="sm">
                                <x-wirekit::row align="center" justify="between">
                                    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                                    <span class="text-sm font-semibold text-slate-900">{{ $count }}</span>
                                </x-wirekit::row>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $color }}" style="width: {{ min(100, round($count / max(1, $metrics['Total akun HRD']) * 100)) }}%"></div>
                                </div>
                            </x-wirekit::stack>
                        @endforeach
                    </x-wirekit::stack>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @elseif (auth()->user()->can('view-contract'))
            <x-wirekit::card class="xl:col-span-2">
                <x-wirekit::card.header>
                    <x-wirekit::row align="center" justify="between" class="flex-wrap gap-3">
                        <x-wirekit::stack gap="1">
                            <h2 class="text-lg font-semibold text-slate-900">Kontrak Mendekati Akhir</h2>
                            <p class="text-sm text-slate-500">Kontrak aktif yang berakhir dalam 30 hari.</p>
                        </x-wirekit::stack>
                        <a href="{{ route('contract.view') }}" wire:navigate class="text-xs font-semibold text-[#168ED1] hover:underline">Lihat Semua</a>
                    </x-wirekit::row>
                </x-wirekit::card.header>
                <x-wirekit::card.body>
                    <div class="overflow-x-auto">
                        <x-wirekit::table hoverable>
                            <x-wirekit::table.head><x-wirekit::table.row>
                                <x-wirekit::table.th>Karyawan</x-wirekit::table.th>
                                <x-wirekit::table.th>Berakhir</x-wirekit::table.th>
                                <x-wirekit::table.th>Aksi</x-wirekit::table.th>
                            </x-wirekit::table.row></x-wirekit::table.head>
                            <x-wirekit::table.body>
                                @forelse ($expiring as $contract)
                                    <x-wirekit::table.row>
                                        <x-wirekit::table.td><x-wirekit::stack gap="1"><span class="text-sm font-semibold text-slate-800">{{ $contract->employees->name }}</span><span class="text-xs text-slate-400">{{ $contract->contract_number }}</span></x-wirekit::stack></x-wirekit::table.td>
                                        <x-wirekit::table.td><span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">{{ $contract->end_date->format('d M Y') }}</span></x-wirekit::table.td>
                                        <x-wirekit::table.td>@can('show-contract')<x-wirekit::button type="button" size="sm" href="{{ route('contract.show', [$contract->employee_id, $contract->id]) }}" wire:navigate>Detail</x-wirekit::button>@endcan</x-wirekit::table.td>
                                    </x-wirekit::table.row>
                                @empty
                                    <x-wirekit::table.row><x-wirekit::table.td colspan="3" class="py-10 text-center text-sm text-slate-500">Tidak ada kontrak yang mendekati tanggal berakhir.</x-wirekit::table.td></x-wirekit::table.row>
                                @endforelse
                            </x-wirekit::table.body>
                        </x-wirekit::table>
                    </div>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endif
        <x-wirekit::card>
            <x-wirekit::card.header><x-wirekit::stack gap="1"><h2 class="text-lg font-semibold text-slate-900">Quick Actions</h2><p class="text-sm text-slate-500">Akses fitur yang tersedia.</p></x-wirekit::stack></x-wirekit::card.header>
            <x-wirekit::card.body>
                <x-wirekit::stack gap="sm">
                    @if ($admin)
                        @can('view-user')
                            <x-wirekit::button href="{{ route('user.view') }}" wire:navigate intent="neutral" surface="outline"><x-wirekit::icon name="user" /> Manajemen Akun HRD</x-wirekit::button>
                        @endcan
                    @else
                        @can('view-employee')<x-wirekit::button href="{{ route('employee.view') }}" wire:navigate intent="neutral" surface="outline"><x-wirekit::icon name="user" /> Data Karyawan</x-wirekit::button>@endcan
                        @can('view-contract')<x-wirekit::button href="{{ route('contract.view') }}" wire:navigate intent="neutral" surface="outline"><x-wirekit::icon name="archive" /> Kontrak Kerja</x-wirekit::button>@endcan
                        @can('view-position')<x-wirekit::button href="{{ route('position.view') }}" wire:navigate intent="neutral" surface="outline"><x-wirekit::icon name="briefcase" /> Jabatan</x-wirekit::button>@endcan
                        @can('view-benefit')<x-wirekit::button href="{{ route('benefit.view') }}" wire:navigate intent="neutral" surface="outline"><x-wirekit::icon name="banknotes" /> Benefit</x-wirekit::button>@endcan
                        @can('view-type-leave')<x-wirekit::button href="{{ route('leave.type.view') }}" wire:navigate intent="neutral" surface="outline"><x-wirekit::icon name="book" /> Jenis Cuti</x-wirekit::button>@endcan
                    @endif
                    <x-wirekit::button href="{{ route('my-profile') }}" wire:navigate intent="neutral" surface="outline"><x-wirekit::icon name="user" /> Profil Saya</x-wirekit::button>
                </x-wirekit::stack>
            </x-wirekit::card.body>
        </x-wirekit::card>
    </div>
</x-wirekit::stack>
