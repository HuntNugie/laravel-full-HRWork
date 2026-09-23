<x-wirekit::stack gap="md">

    <x-wirekit::stack gap="sm">
        <span class="text-sm font-medium text-[#30AFFF]">SDM</span>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen PHK</h1>
                <p class="text-sm text-slate-500">Kelola proses pemutusan hubungan kerja karyawan secara terstruktur.</p>
            </div>

            <x-wirekit::button
                href="{{ route('termination.create') }}"
                class="bg-[#30AFFF] text-white hover:bg-sky-500"
                wire:navigate
            >
                Buat PHK
            </x-wirekit::button>
        </div>
    </x-wirekit::stack>

    <div class="grid gap-4 md:grid-cols-4">
        @foreach ([
            ['label' => 'Menunggu Persetujuan', 'key' => 'submitted', 'intent' => 'warning'],
            ['label' => 'Disetujui', 'key' => 'approved', 'intent' => 'info'],
            ['label' => 'Selesai', 'key' => 'completed', 'intent' => 'success'],
            ['label' => 'Ditolak', 'key' => 'rejected', 'intent' => 'danger'],
        ] as $item)
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <p class="text-sm font-medium text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $summary[$item['key']] }}</p>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endforeach
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Daftar PHK</h2>
                    <p class="text-sm text-slate-500">Seluruh riwayat proses PHK karyawan.</p>
                </div>

                <div class="grid gap-3 md:grid-cols-3">
                    <x-wirekit::input
                        name="search"
                        placeholder="Cari nama, email, atau employee code"
                        wire:model.live.debounce.400ms="search"
                    />

                    <x-wirekit::select
                        name="status"
                        label="Status"
                        hideLabel
                        :options="[
                            '' => 'Semua status',
                            'submitted' => 'Menunggu persetujuan',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                            'cancelled' => 'Dibatalkan',
                            'completed' => 'Selesai',
                        ]"
                        wire:model.live="status"
                    />

                    <x-wirekit::select
                        name="reasonType"
                        label="Alasan"
                        hideLabel
                        :options="array_merge(['' => 'Semua alasan'], $reasonTypes)"
                        wire:model.live="reasonType"
                    />
                </div>

                <div class="flex justify-end">
                    <x-wirekit::button
                        type="button"
                        surface="outline"
                        intent="neutral"
                        size="sm"
                        wire:click="resetFilters"
                    >
                        Reset Filter
                    </x-wirekit::button>
                </div>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="overflow-x-auto">
                <x-wirekit::table>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Employee</x-wirekit::table.th>
                            <x-wirekit::table.th>Alasan</x-wirekit::table.th>
                            <x-wirekit::table.th>Tanggal Efektif</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th align="right">Aksi</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($terminations as $termination)
                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $termination->employee?->user?->name ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $termination->employee?->employee_code ?? '—' }}
                                    </p>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <p class="text-sm text-slate-700">
                                        {{ $reasonTypes[$termination->reason_type] ?? ucfirst($termination->reason_type) }}
                                    </p>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    {{ $termination->approved_effective_date?->translatedFormat('d M Y') ?? $termination->proposed_effective_date?->translatedFormat('d M Y') }}
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <x-wirekit::badge :intent="match($termination->status) {
                                        'submitted' => 'warning',
                                        'approved' => 'info',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary',
                                        'completed' => 'success',
                                        default => 'secondary',
                                    }">
                                        {{ ucfirst($termination->status) }}
                                    </x-wirekit::badge>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td align="right">
                                    <x-wirekit::button
                                        type="button"
                                        href="{{ route('termination.show', $termination) }}"
                                        wire:navigate
                                        surface="outline"
                                        size="sm"
                                    >
                                        Detail
                                    </x-wirekit::button>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5">
                                    <div class="py-10 text-center">
                                        <p class="text-sm font-semibold text-slate-700">Belum ada data PHK.</p>
                                        <p class="mt-1 text-sm text-slate-400">Proses PHK yang dibuat akan muncul di sini.</p>
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>

                {{ $terminations->links() }}
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
