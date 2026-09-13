<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            Layanan Karyawan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Pengajuan Izin & Sakit
        </h1>

        <p class="text-sm text-slate-500">
            Kelola dan tinjau pengajuan izin dan sakit karyawan.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        FILTER
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">

                <x-wirekit::segmented-control label="Status Pengajuan" name="segment" :options="[
                    '' => 'Semua',
                    'pending' => 'Menunggu',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                ]" value=""
                    size="sm" wire:model.live='segment' />
                <x-wirekit::segmented-control label="Jenis Pengajuan" name="typeLeave" :options="[
                    '' => 'Semua',
                    'sakit' => 'Sakit',
                    'izin' => 'Izin',
                ]" value=""
                    size="sm" wire:model.live='typeLeave' />

                <div class="w-full md:max-w-xs">
                    <x-wirekit::input label="Cari karyawan" type="text" wire:model.live.debounce.500ms='search'
                        name="search" placeholder="Cari nama atau kode pegawai" />
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        SUBMISSION LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Daftar Pengajuan
                </h2>

                <p class="text-sm text-slate-500">
                    Daftar pengajuan izin dan sakit karyawan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar max-h-[600px] overflow-auto">

                <x-wirekit::table hoverable table-label="Daftar pengajuan izin dan sakit">

                    {{-- =================================================
                        HEADER
                    ================================================== --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Karyawan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Jenis
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tanggal pengajuan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    {{-- =================================================
                        BODY
                    ================================================== --}}
                    <x-wirekit::table.body>

                        @foreach ($absences as $absence)
                            <x-wirekit::table.row>

                                {{-- Karyawan --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div class="size-9 shrink-0 overflow-hidden rounded-full bg-slate-100">
                                            @if ($absence?->employees->user?->getFirstMediaUrl('avatar'))
                                                <img src="{{ $absence?->employees->user?->getFirstMediaUrl('avatar') }}"
                                                    alt="{{ $absence->employees->user->name }}"
                                                    class="size-full object-cover">
                                            @else
                                                <img src="{{ asset('assets/nonProfile.jpg') }}"
                                                    alt="{{ $absence->employees->user->name }}"
                                                    class="size-full object-cover">
                                            @endif
                                        </div>

                                        <div>

                                            <p class="text-sm font-medium text-slate-900">
                                                {{ $absence->employees->user->name }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ $absence->employees->employee_code }}
                                            </p>

                                        </div>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- Jenis --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::badge variant="info">
                                        {{ $absence->type }}
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                {{-- Periode --}}
                                <x-wirekit::table.td>

                                    <div class="text-sm text-slate-800">
                                        {{ $absence->date->format('d F Y') }}
                                    </div>

                                </x-wirekit::table.td>


                                {{-- Status --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::badge variant="warning">
                                        {{ $absence->status }}
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                {{-- Aksi --}}
                                <x-wirekit::table.td align="right">

                                    <livewire:components.main.absence.modal-detail :absence="$absence">
                                        <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs">
                                            Detail
                                        </x-wirekit::button>
                                    </livewire:components.main.absence.modal-detail>
                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforeach

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
