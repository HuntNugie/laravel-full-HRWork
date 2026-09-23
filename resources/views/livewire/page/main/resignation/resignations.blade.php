<x-wirekit::stack gap="md">

    <x-wirekit::stack gap="sm">
        <span class="text-sm font-medium text-[#30AFFF]">SDM</span>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Resignation</h1>
        <p class="text-sm text-slate-500">Kelola pengajuan pengunduran diri dan proses exit karyawan.</p>
    </x-wirekit::stack>

    <div class="grid gap-4 md:grid-cols-4">
        @foreach ([
            ['label' => 'Menunggu Review', 'key' => 'submitted', 'intent' => 'warning'],
            ['label' => 'Dalam Notice', 'key' => 'approved', 'intent' => 'info'],
            ['label' => 'Selesai', 'key' => 'completed', 'intent' => 'success'],
            ['label' => 'Ditolak', 'key' => 'rejected', 'intent' => 'danger'],
        ] as $item)
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <p class="text-sm font-medium text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $this->summary[$item['key']] }}</p>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endforeach
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Daftar Pengajuan</h2>
                    <p class="text-sm text-slate-500">Semua riwayat resignation karyawan.</p>
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
                            'all' => 'Semua status',
                            'submitted' => 'Menunggu review',
                            'approved' => 'Dalam notice',
                            'rejected' => 'Ditolak',
                            'cancelled' => 'Dibatalkan',
                            'completed' => 'Selesai',
                        ]"
                        wire:model.live="status"
                    />

                    <div class="flex items-end justify-end">
                        <x-wirekit::button type="button" surface="outline" intent="neutral" wire:click="resetFilters">
                            Reset Filter
                        </x-wirekit::button>
                    </div>
                </div>
            </div>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="overflow-x-auto">
                <x-wirekit::table>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Employee</x-wirekit::table.th>
                            <x-wirekit::table.th>Position</x-wirekit::table.th>
                            <x-wirekit::table.th>Tanggal Pengajuan</x-wirekit::table.th>
                            <x-wirekit::table.th>Last Working Day</x-wirekit::table.th>
                            <x-wirekit::table.th>Status</x-wirekit::table.th>
                            <x-wirekit::table.th align="right">Aksi</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($this->resignations as $resignation)
                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $resignation->employee?->user?->name ?? '—' }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ $resignation->employee?->employee_code ?? '—' }}
                                    </p>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    {{ $resignation->employee?->position?->name ?? $resignation->employeeContract?->position_name ?? '—' }}
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    {{ $resignation->submitted_at?->translatedFormat('d M Y') ?? '—' }}
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    {{ $resignation->approved_last_working_date?->translatedFormat('d M Y') ?? $resignation->proposed_last_working_date?->translatedFormat('d M Y') }}
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <x-wirekit::badge :intent="match($resignation->status) {
                                        'submitted' => 'warning',
                                        'approved' => 'info',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary',
                                        'completed' => 'success',
                                        default => 'secondary',
                                    }">
                                        {{ ucfirst($resignation->status) }}
                                    </x-wirekit::badge>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td align="right">
                                    <x-wirekit::button
                                        type="button"
                                        href="{{ route('resignation.show', $resignation) }}"
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
                                <x-wirekit::table.td colspan="6">
                                    <div class="py-10 text-center">
                                        <p class="text-sm font-semibold text-slate-700">Belum ada pengajuan resign.</p>
                                        <p class="mt-1 text-sm text-slate-400">Pengajuan employee akan muncul di sini.</p>
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>

                {{ $this->resignations->links() }}
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
