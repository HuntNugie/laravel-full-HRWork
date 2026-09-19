<x-wirekit::modal name="preview-warning-letter-indication-{{ $employeeId }}" size="xl">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">
            <h2 class="text-lg font-semibold text-slate-900">
                Detail Indikasi Pelanggaran
            </h2>

            <p class="text-sm text-slate-500">
                Periksa data indikasi sebelum menetapkan Surat Peringatan.
            </p>
        </x-wirekit::stack>
    </x-wirekit::modal.header>

    {{-- BODY --}}
    <x-wirekit::modal.body>
        @if ($this->indication)

            <x-wirekit::stack gap="md">

                {{-- IDENTITAS --}}
                <x-wirekit::card>
                    <x-wirekit::card.body>
                        <x-wirekit::stack gap="xs">

                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-slate-500">
                                        Karyawan
                                    </p>

                                    <h3 class="text-base font-semibold text-slate-900">
                                        {{ $this->indication['employee']->user?->name ?? '-' }}
                                    </h3>

                                    <p class="text-sm text-slate-500">
                                        {{ $this->indication['employee']->employee_code ?? '-' }}
                                    </p>
                                </div>

                                <x-wirekit::badge intent="warning">
                                    Unpresent
                                </x-wirekit::badge>
                            </div>

                        </x-wirekit::stack>
                    </x-wirekit::card.body>
                </x-wirekit::card>

                {{-- RINGKASAN --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <x-wirekit::card>
                        <x-wirekit::card.body>
                            <p class="text-sm text-slate-500">
                                Jumlah Kejadian
                            </p>

                            <p class="text-2xl font-semibold text-slate-900">
                                {{ $this->indication['unpresent_count'] }}
                            </p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                    <x-wirekit::card>
                        <x-wirekit::card.body>
                            <p class="text-sm text-slate-500">
                                Batas Indikasi
                            </p>

                            <p class="text-2xl font-semibold text-slate-900">
                                {{ $this->indication['threshold'] }}
                            </p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                    <x-wirekit::card>
                        <x-wirekit::card.body>
                            <p class="text-sm text-slate-500">
                                Periode
                            </p>

                            <p class="text-sm font-medium text-slate-900">
                                {{ \Carbon\Carbon::parse($this->indication['period_start'])->translatedFormat('d F Y') }}
                                -
                                {{ \Carbon\Carbon::parse($this->indication['period_end'])->translatedFormat('d F Y') }}
                            </p>
                        </x-wirekit::card.body>
                    </x-wirekit::card>

                </div>

                {{-- DETAIL KEJADIAN --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>
                        <x-wirekit::stack gap="xs">
                            <h3 class="font-semibold text-slate-900">
                                Detail Kejadian
                            </h3>

                            <p class="text-sm text-slate-500">
                                Hari kerja tanpa data presensi.
                            </p>
                        </x-wirekit::stack>
                    </x-wirekit::card.header>

                    <x-wirekit::card.body class="wk-scrollbar overflow-auto max-h-64">

                        <x-wirekit::table>

                            <x-wirekit::table.row>
                                <x-wirekit::table.th>
                                    No
                                </x-wirekit::table.th>

                                <x-wirekit::table.th>
                                    Tanggal
                                </x-wirekit::table.th>

                                <x-wirekit::table.th>
                                    Status
                                </x-wirekit::table.th>

                                <x-wirekit::table.th>
                                    Keterangan
                                </x-wirekit::table.th>
                            </x-wirekit::table.row>

                            @foreach ($this->indication['dates'] as $index => $date)
                                <x-wirekit::table.row>

                                    <x-wirekit::table.td>
                                        {{ $index + 1 }}
                                    </x-wirekit::table.td>

                                    <x-wirekit::table.td>
                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                                    </x-wirekit::table.td>

                                    <x-wirekit::table.td>
                                        <x-wirekit::badge intent="danger">
                                            Unpresent
                                        </x-wirekit::badge>
                                    </x-wirekit::table.td>

                                    <x-wirekit::table.td>
                                        Tidak terdapat presensi.
                                    </x-wirekit::table.td>

                                </x-wirekit::table.row>
                            @endforeach

                        </x-wirekit::table>

                    </x-wirekit::card.body>
                </x-wirekit::card>

                {{-- PERINGATAN --}}
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-medium text-amber-900">
                        Perlu pemeriksaan HR
                    </p>

                    <p class="mt-1 text-sm text-amber-700">
                        Indikasi ini merupakan hasil perhitungan sistem.
                        Penetapan Surat Peringatan tetap menjadi keputusan HR.
                    </p>
                </div>

            </x-wirekit::stack>
        @else
            <div class="py-10 text-center">
                <p class="text-sm text-slate-500">
                    Data indikasi tidak ditemukan.
                </p>
            </div>

        @endif
    </x-wirekit::modal.body>

    {{-- FOOTER --}}
    <x-wirekit::modal.footer>
        <x-wirekit::row justify="end" gap="sm">

            <x-wirekit::modal.close>
                <x-wirekit::button type="button" variant="outline">
                    Tutup
                </x-wirekit::button>
            </x-wirekit::modal.close>

            @can('show-warning-letter')
                @if ($this->indication)
                    <x-wirekit::button type="button" wire:click="setAsWarningLetter">
                        Tetapkan sebagai SP
                    </x-wirekit::button>
                @endif
            @endcan

        </x-wirekit::row>
    </x-wirekit::modal.footer>

</x-wirekit::modal>
