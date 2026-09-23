<div class="space-y-6">
    <x-wirekit::stack gap="sm">
        <div>
            <p class="text-sm font-medium text-[#30AFFF]">Kedisiplinan Saya</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Surat Peringatan Saya</h1>
            <p class="mt-1 text-sm text-slate-500">
                Riwayat Surat Peringatan yang telah diterbitkan untuk kamu.
            </p>
        </div>
    </x-wirekit::stack>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Diterbitkan', 'value' => $this->summary['total'], 'description' => 'Seluruh Surat Peringatan'],
            ['label' => 'SP1', 'value' => $this->summary['sp1'], 'description' => 'Surat tingkat pertama'],
            ['label' => 'SP2', 'value' => $this->summary['sp2'], 'description' => 'Surat tingkat kedua'],
            ['label' => 'SP3', 'value' => $this->summary['sp3'], 'description' => 'Surat tingkat ketiga'],
        ] as $item)
            <x-wirekit::card>
                <x-wirekit::card.body>
                    <p class="text-sm font-medium text-slate-500">{{ $item['label'] }}</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $item['value'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $item['description'] }}</p>
                </x-wirekit::card.body>
            </x-wirekit::card>
        @endforeach
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Daftar Surat Peringatan</h2>
                <p class="text-sm text-slate-500">Hanya Surat Peringatan yang berstatus diterbitkan yang ditampilkan.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="overflow-x-auto">
                <x-wirekit::table hoverable>
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>
                            <x-wirekit::table.th>Nomor Surat</x-wirekit::table.th>
                            <x-wirekit::table.th>Level</x-wirekit::table.th>
                            <x-wirekit::table.th>Tanggal</x-wirekit::table.th>
                            <x-wirekit::table.th>Alasan</x-wirekit::table.th>
                            <x-wirekit::table.th align="right">Aksi</x-wirekit::table.th>
                        </x-wirekit::table.row>
                    </x-wirekit::table.head>

                    <x-wirekit::table.body>
                        @forelse ($this->warningLetters as $warningLetter)
                            <x-wirekit::table.row>
                                <x-wirekit::table.td>
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $warningLetter->letter_number ?? '—' }}
                                    </p>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <x-wirekit::badge intent="warning">
                                        {{ $warningLetter->warning_level }}
                                    </x-wirekit::badge>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    {{ $warningLetter->issued_date?->translatedFormat('d F Y') ?? '—' }}
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <p class="max-w-sm truncate text-sm text-slate-700" title="{{ $warningLetter->reason }}">
                                        {{ $warningLetter->reason }}
                                    </p>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td align="right">
                                    @can('show-warning-letter-my')
                                        <x-wirekit::button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            href="{{ route('warning-letter.my.show', $warningLetter) }}"
                                            wire:navigate
                                        >
                                            <x-wirekit::icon name="eye" />
                                            Detail
                                        </x-wirekit::button>
                                    @endcan
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5">
                                    <div class="py-10 text-center">
                                        <div class="mx-auto flex size-11 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                            <x-wirekit::icon name="check-circle" class="size-5" />
                                        </div>
                                        <p class="mt-3 text-sm font-semibold text-slate-700">Belum ada Surat Peringatan</p>
                                        <p class="mt-1 text-sm text-slate-400">
                                            Saat ini belum ada Surat Peringatan yang diterbitkan untuk kamu.
                                        </p>
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse
                    </x-wirekit::table.body>
                </x-wirekit::table>
            </div>

            @if ($this->warningLetters->hasPages())
                <div class="mt-5">
                    {{ $this->warningLetters->links() }}
                </div>
            @endif
        </x-wirekit::card.body>
    </x-wirekit::card>
</div>
