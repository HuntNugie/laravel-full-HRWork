<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <p class="text-sm font-medium text-[#30AFFF]">
                Kompensasi
            </p>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Penggajian
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola periode penggajian dan proses penggajian karyawan.
            </p>

        </div>


        @can('create-period-payroll')
            <livewire:components.main.payroll.modal-create-payroll-period>

                <x-wirekit::button type="button">

                    <x-wirekit::icon name="plus" class="size-4" />

                    Buat Periode

                </x-wirekit::button>

                </livewire:components.main.payroll.modal-create-payroll>
            @endcan

    </div>


    {{-- =====================================================
        FILTER
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto]">

                {{-- BULAN --}}
                <x-wirekit::select label="Bulan" wire:model.live="month">

                    <option value="all">
                        Semua Bulan
                    </option>

                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>

                </x-wirekit::select>


                {{-- TAHUN --}}
                <x-wirekit::select label="Tahun" wire:model.live="year">

                    @for ($yearOption = now()->year; $yearOption >= now()->year - 5; $yearOption--)
                        <option value="{{ $yearOption }}">
                            {{ $yearOption }}
                        </option>
                    @endfor

                </x-wirekit::select>


                {{-- STATUS --}}
                <x-wirekit::select label="Status" wire:model.live="status">

                    <option value="all">
                        Semua Status
                    </option>

                    <option value="draft">
                        Draft
                    </option>

                    <option value="processing">
                        Sedang Diproses
                    </option>

                    <option value="processed">
                        Selesai Diproses
                    </option>

                    <option value="paid">
                        Dibayar
                    </option>

                    <option value="cancelled">
                        Dibatalkan
                    </option>

                </x-wirekit::select>


                {{-- RESET --}}
                <div class="flex items-end">

                    <x-wirekit::button type="button" variant="outline" wire:click="resetFilters" class="w-full">
                        Reset
                    </x-wirekit::button>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        PAYROLL PERIOD TABLE
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="xs">

                <h2 class="text-lg font-semibold text-slate-900">
                    Periode Penggajian
                </h2>

                <p class="text-sm text-slate-500">
                    Daftar periode penggajian yang telah dibuat.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar overflow-x-auto">

                <x-wirekit::table hoverable table-label="Daftar periode penggajian">

                    {{-- HEADER --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Periode
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tanggal Periode
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tanggal Pembayaran
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Dibuat
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    {{-- BODY --}}
                    <x-wirekit::table.body>

                        @forelse ($payrollPeriods as $period)
                            <x-wirekit::table.row>

                                {{-- PERIODE --}}
                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm font-medium text-slate-900">
                                            {{ $period->name }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            #{{ $period->id }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- TANGGAL --}}
                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm text-slate-700">
                                            {{ $period->start_date?->translatedFormat('d M Y') }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-400">
                                            s.d.
                                            {{ $period->end_date?->translatedFormat('d M Y') }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- PAYMENT DATE --}}
                                <x-wirekit::table.td>

                                    @if ($period->payment_date)
                                        <span class="text-sm text-slate-700">
                                            {{ $period->payment_date->translatedFormat('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            Belum ditentukan
                                        </span>
                                    @endif

                                </x-wirekit::table.td>


                                {{-- STATUS --}}
                                <x-wirekit::table.td>

                                    @if ($period->status === 'draft')
                                        <x-wirekit::badge intent="warning">
                                            Draft
                                        </x-wirekit::badge>
                                    @elseif ($period->status === 'processing')
                                        <x-wirekit::badge intent="primary">
                                            Sedang Diproses
                                        </x-wirekit::badge>
                                    @elseif ($period->status === 'processed')
                                        <x-wirekit::badge intent="success">
                                            Selesai Diproses
                                        </x-wirekit::badge>
                                    @elseif ($period->status === 'paid')
                                        <x-wirekit::badge intent="success">
                                            Dibayar
                                        </x-wirekit::badge>
                                    @elseif ($period->status === 'cancelled')
                                        <x-wirekit::badge intent="secondary">
                                            Dibatalkan
                                        </x-wirekit::badge>
                                    @endif

                                </x-wirekit::table.td>


                                {{-- CREATED --}}
                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm text-slate-700">
                                            {{ $period->created_at?->translatedFormat('d M Y') }}
                                        </p>

                                        @if ($period->creator)
                                            <p class="mt-0.5 text-xs text-slate-400">
                                                {{ $period->creator->name }}
                                            </p>
                                        @endif

                                    </div>

                                </x-wirekit::table.td>


                                {{-- ACTION --}}
                                <x-wirekit::table.td align="right">

                                    <div class="flex justify-end">

                                        @can('show-payroll')
                                            <x-wirekit::button type="button" variant="outline" size="sm"
                                                href="{{ route('payroll.show', $period->id) }}" wire:navigate>
                                                Detail
                                            </x-wirekit::button>
                                        @endcan

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="6">

                                    <div class="py-10 text-center">

                                        <div
                                            class="mx-auto flex size-11 items-center justify-center rounded-lg bg-slate-100">

                                            <x-wirekit::icon name="calendar" class="size-5 text-slate-500" />

                                        </div>

                                        <p class="mt-3 text-sm font-medium text-slate-700">
                                            Belum ada periode penggajian
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Belum ditemukan periode penggajian
                                            berdasarkan filter yang dipilih.
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>


            {{-- PAGINATION --}}
            @if ($payrollPeriods->hasPages())
                <div class="mt-5 border-t border-slate-100 pt-4">

                    {{ $payrollPeriods->links() }}

                </div>
            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
