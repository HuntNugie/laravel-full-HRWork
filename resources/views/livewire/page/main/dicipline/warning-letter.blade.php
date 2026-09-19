<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <x-wirekit::stack gap="1">

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Kedisiplinan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Surat Peringatan
        </h1>

        <p class="text-sm text-slate-500">
            Kelola dan terbitkan surat peringatan berdasarkan
            pelanggaran karyawan.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        SUMMARY
    ====================================================== --}}
    <div class="grid gap-4 md:grid-cols-3">

        {{-- DRAFT --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Draft
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-900">
                            {{ $this->summary['draft'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Belum diterbitkan
                        </p>

                    </div>

                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                        <x-wirekit::icon name="file" class="size-5" />
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- ISSUED --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Diterbitkan
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-900">
                            {{ $this->summary['issued'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Surat resmi
                        </p>

                    </div>

                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <x-wirekit::icon name="check" class="size-5" />
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- CANCELLED --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Dibatalkan
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-900">
                            {{ $this->summary['cancelled'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Tidak berlaku
                        </p>

                    </div>

                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-500">
                        <x-wirekit::icon name="close" class="size-5" />
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>

    {{-- =====================================================
    INDIKASI PELANGGARAN
====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-base font-semibold text-slate-900">
                    Indikasi Pelanggaran
                </h2>

                <p class="text-sm text-slate-500">
                    Karyawan yang terindikasi memenuhi aturan disiplin
                    berdasarkan data presensi bulan berjalan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="wk-scrollbar overflow-auto max-h-64">

                <x-wirekit::table>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Employee
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Pelanggaran
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Periode
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Kejadian
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Batas
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($this->unpresentIndications as $indication)
                            <x-wirekit::table.row>

                                {{-- EMPLOYEE --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::stack gap="1">

                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $indication['employee']->user?->name ?? '—' }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            {{ $indication['employee']->employee_code ?? '—' }}
                                        </p>

                                    </x-wirekit::stack>

                                </x-wirekit::table.td>


                                {{-- VIOLATION --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::badge intent="warning">
                                        Unpresent
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                {{-- PERIOD --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::stack gap="1">

                                        <p class="text-sm text-slate-700">
                                            {{ \Carbon\Carbon::parse($indication['period_start'])->translatedFormat('d M Y') }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            s.d.
                                            {{ \Carbon\Carbon::parse($indication['period_end'])->translatedFormat('d M Y') }}
                                        </p>

                                    </x-wirekit::stack>

                                </x-wirekit::table.td>


                                {{-- COUNT --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm font-semibold text-slate-800">
                                        {{ $indication['unpresent_count'] }} kali
                                    </span>

                                </x-wirekit::table.td>


                                {{-- THRESHOLD --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $indication['threshold'] }} kali
                                    </span>

                                </x-wirekit::table.td>


                                {{-- ACTION --}}
                                <x-wirekit::table.td align="right">

                                    @can('show-warning-letter')
                                        <livewire:components.main.dicipline.modal-preview-warning-letter-indication
                                            :employee-id="$indication['employee']->id" :key="'preview-warning-letter-indication-' .
                                                $indication['employee']->id">
                                            <x-wirekit::button type="button" variant="outline">
                                                Preview
                                            </x-wirekit::button>
                                        </livewire:components.main.dicipline.modal-preview-warning-letter-indication>
                                    @endcan

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="6">

                                    <div class="py-8 text-center">

                                        <div
                                            class="mx-auto flex size-11 items-center justify-center rounded-xl bg-slate-50">

                                            <x-wirekit::icon name="check-circle" class="size-5 text-slate-400" />

                                        </div>

                                        <p class="mt-3 text-sm font-semibold text-slate-700">
                                            Tidak ada indikasi pelanggaran
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Tidak ada karyawan yang memenuhi batas
                                            pelanggaran Unpresent bulan berjalan.
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
    {{-- =====================================================
        WARNING LETTER TABLE
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-base font-semibold text-slate-900">
                        Daftar Surat Peringatan
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar surat peringatan yang dibuat untuk karyawan.
                    </p>

                </x-wirekit::stack>


                @can('create-warning-letter')
                    <livewire:components.main.dicipline.modal-create-warning-letter>

                        <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                            <x-wirekit::icon name="plus" />
                            Buat Surat Peringatan
                        </x-wirekit::button>

                    </livewire:components.main.dicipline.modal-create-warning-letter>
                @endcan

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            {{-- =================================================
                FILTER
            ================================================== --}}
            <div class="mb-5 grid gap-3 md:grid-cols-3">

                {{-- SEARCH --}}
                <div class="md:col-span-1">

                    <x-wirekit::input label="Cari Karyawan" placeholder="Nama atau kode employee..."
                        wire:model.live.debounce.300ms="search" />

                </div>


                {{-- LEVEL --}}
                <div>

                    <x-wirekit::select label="Level" wire:model.live="level">

                        <option value="all">
                            Semua Level
                        </option>

                        <option value="SP1">
                            SP1
                        </option>

                        <option value="SP2">
                            SP2
                        </option>

                        <option value="SP3">
                            SP3
                        </option>

                    </x-wirekit::select>

                </div>


                {{-- STATUS --}}
                <div>

                    <x-wirekit::select label="Status" wire:model.live="status">

                        <option value="all">
                            Semua Status
                        </option>

                        <option value="draft">
                            Draft
                        </option>

                        <option value="issued">
                            Diterbitkan
                        </option>

                        <option value="cancelled">
                            Dibatalkan
                        </option>

                    </x-wirekit::select>

                </div>

            </div>


            {{-- =================================================
                TABLE
            ================================================== --}}
            <div class="overflow-x-auto">

                <x-wirekit::table>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Employee
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Level
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tanggal
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Alasan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($this->warningLetters as $warning)
                            <x-wirekit::table.row>

                                {{-- EMPLOYEE --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-semibold text-sky-700">
                                            {{ strtoupper(substr($warning->employee?->user?->name ?? 'U', 0, 1)) }}
                                        </div>

                                        <x-wirekit::stack gap="1">

                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $warning->employee?->user?->name ?? '—' }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $warning->employee?->employee_code ?? '—' }}
                                            </p>

                                        </x-wirekit::stack>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- LEVEL --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::badge intent="warning">
                                        {{ $warning->warning_level }}
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                {{-- TANGGAL --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $warning->issued_date?->translatedFormat('d F Y') ?? '—' }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- ALASAN --}}
                                <x-wirekit::table.td>

                                    <p class="max-w-xs truncate text-sm text-slate-700" title="{{ $warning->reason }}">
                                        {{ $warning->reason }}
                                    </p>

                                </x-wirekit::table.td>


                                {{-- STATUS --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::badge :intent="$this->statusIntent($warning->status)">
                                        {{ $this->statusLabel($warning->status) }}
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                {{-- ACTION --}}
                                <x-wirekit::table.td align="right">

                                    <div class="flex items-center justify-end gap-2">

                                        @can('show-warning-letter')
                                            <x-wirekit::button type="button" variant="outline"
                                                href="{{ route('discipline.warning-letter.show', $warning) }}"
                                                wire:navigate>
                                                <x-wirekit::icon name="eye" />
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
                                            class="mx-auto flex size-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                            <x-wirekit::icon name="file" class="size-5" />
                                        </div>

                                        <p class="mt-3 text-sm font-semibold text-slate-700">
                                            Belum ada Surat Peringatan
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Belum ada data yang sesuai dengan filter.
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>


            {{-- PAGINATION --}}
            @if ($this->warningLetters->hasPages())
                <div class="mt-5">
                    {{ $this->warningLetters->links() }}
                </div>
            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
