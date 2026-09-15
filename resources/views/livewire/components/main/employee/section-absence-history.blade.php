    {{-- =====================================================
    ABSENCE HISTORY
====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="xs">

                    <h2 class="text-base font-semibold text-slate-900">
                        Riwayat Izin & Sakit
                    </h2>

                    <p class="text-sm text-slate-500">
                        Riwayat pengajuan izin dan sakit Anda.
                    </p>
                    <button type="button"
                        class="self-start rounded-md border border-white-300 px-3 py-2 text-sm font-medium text-white  bg-red-500 hover:bg-white-50"
                        wire:click="$set('from', null); $set('to', null); $set('segment', '')">
                        Reset
                    </button>

                </x-wirekit::stack>


                {{-- FILTER --}}
                <div class="flex flex-col gap-4">


                    {{-- RENTANG TANGGAL --}}
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                        <x-wirekit::input type="date" label="Dari" wire:model.live='from' />

                        <x-wirekit::input type="date" label="Sampai" wire:model.live='to' />

                    </div>


                    {{-- STATUS --}}
                    <x-wirekit::segmented-control label="Status" name="absence-status" :options="[
                        '' => 'Semua',
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]" value=""
                        size="sm" wire:model.live='segment' />



                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar max-h-[500px] overflow-auto">

                <x-wirekit::table hoverable table-label="Riwayat izin dan sakit">

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Jenis
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Periode
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Alasan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Diterima/Ditolak oleh
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tanggal Diterima/ditolak
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($absences as $absence)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>
                                    <x-wirekit::badge variant="info">
                                        {{ $absence->type }}
                                    </x-wirekit::badge>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">
                                        {{ $absence->date->format('d F Y') }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">
                                        {{ $absence->reason }}
                                    </span>
                                </x-wirekit::table.td>



                                <x-wirekit::table.td>
                                    @if ($absence->status === 'pending')
                                        <x-wirekit::badge variant="outline" intent="warning">
                                            Menunggu
                                        </x-wirekit::badge>
                                    @elseif ($absence->status === 'approved')
                                        <x-wirekit::badge variant="outline" intent="success">
                                            Pengajuan Di terima
                                        </x-wirekit::badge>
                                    @else
                                        <x-wirekit::badge variant="outline" intent="danger">
                                            Pengajuan Di tolak
                                        </x-wirekit::badge>
                                    @endif
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-600">
                                        {{ $absence->approver->name }}
                                    </span>
                                </x-wirekit::table.td>
                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-600">
                                        {{ $absence->approved_at->format('d-m-Y') }}
                                    </span>
                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5">
                                    <div class="py-8 text-center text-sm text-slate-500">
                                        Belum ada riwayat izin atau sakit.
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse





                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>
