<x-wirekit::stack gap="md">

    {{-- =====================================================
        HEADER
    ====================================================== --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

        <x-wirekit::stack gap="xs">

            <a href="{{ route('payroll.employee.show', [
                'period' => $period->id,
                'payroll' => $payroll->id,
            ]) }}"
                wire:navigate
                class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">
                <span aria-hidden="true">&larr;</span>
                Kembali ke Detail Payroll
            </a>

            <span class="text-sm font-medium text-[#30AFFF]">
                Penggajian
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Edit Payroll
            </h1>

            <p class="text-sm text-slate-500">
                Kelola penyesuaian manual payroll
                {{ $employee->user?->name ?? '-' }}
                untuk periode {{ $period->name }}.
            </p>

        </x-wirekit::stack>


        <x-wirekit::badge intent="warning">
            Draft
        </x-wirekit::badge>

    </div>


    {{-- =====================================================
        EMPLOYEE INFO
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Payroll
                </h2>

                <p class="text-sm text-slate-500">
                    Data dasar payroll yang sedang diedit.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Karyawan
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $employee->user?->name ?? '-' }}
                    </p>
                </div>


                <div>
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Kode Karyawan
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $employee->employee_code ?? '-' }}
                    </p>
                </div>


                <div>
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jabatan
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $payroll->position_name ?? '-' }}
                    </p>
                </div>


                <div>
                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Gaji Harian
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $this->money($payroll->salary_daily) }}
                    </p>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        ATTENDANCE SNAPSHOT
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Ringkasan Kehadiran
                </h2>

                <p class="text-sm text-slate-500">
                    Snapshot hasil perhitungan payroll.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <p class="text-sm text-slate-500">
                        Hari Kerja
                    </p>

                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ number_format($payroll->working_days) }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-slate-500">
                        Hadir
                    </p>

                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ number_format($payroll->present_days) }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-slate-500">
                        Terlambat
                    </p>

                    <p class="mt-1 text-xl font-bold text-amber-600">
                        {{ number_format($payroll->late_days) }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-slate-500">
                        Hari Dibayar
                    </p>

                    <p class="mt-1 text-xl font-bold text-[#30AFFF]">
                        {{ number_format($payroll->paid_days) }}
                    </p>
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        SYSTEM ITEMS
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Komponen Sistem
                </h2>

                <p class="text-sm text-slate-500">
                    Komponen hasil perhitungan otomatis dan tidak dapat diedit
                    secara manual.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Komponen
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tipe
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Sumber
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Jumlah
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($this->systemItems as $item)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $item->name }}
                                    </p>

                                    @if ($item->description)
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->description }}
                                        </p>
                                    @endif

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    @if ($item->type === 'earning')
                                        <x-wirekit::badge intent="success">
                                            Penghasilan
                                        </x-wirekit::badge>
                                    @else
                                        <x-wirekit::badge intent="danger">
                                            Potongan
                                        </x-wirekit::badge>
                                    @endif

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <x-wirekit::badge intent="info">
                                        Sistem
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="right">

                                    <span class="text-sm font-semibold text-slate-800">
                                        {{ $this->money($item->amount) }}
                                    </span>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="4">

                                    <p class="py-6 text-center text-sm text-slate-400">
                                        Belum ada komponen sistem.
                                    </p>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        MANUAL ITEMS
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Penyesuaian Manual
                </h2>

                <p class="text-sm text-slate-500">
                    Tambahkan atau koreksi penghasilan dan potongan manual.
                </p>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Komponen
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tipe
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Nominal
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($this->manualItems as $item)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $item->name }}
                                    </p>

                                    @if ($item->description)
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->description }}
                                        </p>
                                    @endif

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    @if ($item->type === 'earning')
                                        <x-wirekit::badge intent="success">
                                            Penghasilan
                                        </x-wirekit::badge>
                                    @else
                                        <x-wirekit::badge intent="danger">
                                            Potongan
                                        </x-wirekit::badge>
                                    @endif

                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="right">

                                    <span class="text-sm font-semibold text-slate-800">
                                        {{ $this->money($item->amount) }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="right">

                                    <div class="flex justify-end gap-2">

                                        <x-wirekit::button type="button" variant="outline" class="px-3 py-1.5 text-xs"
                                            wire:click="startEditItem({{ $item->id }})">
                                            <x-wirekit::icon name="pencil" />
                                            Edit
                                        </x-wirekit::button>


                                        <x-wirekit::button type="button" variant="outline" intent="danger"
                                            class="px-3 py-1.5 text-xs" wire:click="deleteItem({{ $item->id }})"
                                            wire:confirm="Hapus penyesuaian manual ini?">
                                            <x-wirekit::icon name="trash" />
                                            Hapus
                                        </x-wirekit::button>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="4">

                                    <div class="py-8 text-center">

                                        <x-wirekit::icon name="document-text" class="mx-auto size-6 text-slate-400" />

                                        <p class="mt-2 text-sm text-slate-500">
                                            Belum ada penyesuaian manual.
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
        ITEM FORM
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">

                    {{ $editingItemId ? 'Edit Penyesuaian Manual' : 'Tambah Penyesuaian Manual' }}

                </h2>

                <p class="text-sm text-slate-500">
                    Komponen ini akan disimpan sebagai item manual.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <x-wirekit::form wire:submit="saveItem">

                <x-wirekit::stack gap="md">

                    <div class="grid gap-4 lg:grid-cols-2">

                        <x-wirekit::select label="Tipe" wire:model="itemType">
                            <option value="earning">
                                Penghasilan
                            </option>

                            <option value="deduction">
                                Potongan
                            </option>
                        </x-wirekit::select>


                        <x-wirekit::input label="Nama Komponen" wire:model="itemName"
                            placeholder="Contoh: Bonus Kinerja" />

                    </div>


                    <x-wirekit::input type="number" min="0.01" step="0.01" label="Nominal"
                        wire:model="itemAmount" placeholder="Contoh: 500000" />


                    <x-wirekit::textarea label="Keterangan" wire:model="itemDescription" rows="4"
                        placeholder="Jelaskan alasan penyesuaian..." />


                    <div class="flex justify-end gap-2">

                        @if ($editingItemId)
                            <x-wirekit::button type="button" variant="outline" wire:click="cancelEditItem">
                                Batal Edit
                            </x-wirekit::button>
                        @endif


                        <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            loading-target="saveItem">
                            <x-wirekit::icon name="{{ $editingItemId ? 'check' : 'plus' }}" />

                            {{ $editingItemId ? 'Simpan Perubahan' : 'Tambah Item' }}
                        </x-wirekit::button>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::form>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        NOTES
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Catatan Payroll
                </h2>

                <p class="text-sm text-slate-500">
                    Catatan tambahan yang berkaitan dengan payroll karyawan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <x-wirekit::form wire:submit="saveNotes">

                <x-wirekit::stack gap="md">

                    <x-wirekit::textarea label="Catatan" wire:model="notes" rows="5"
                        placeholder="Tambahkan catatan payroll..." />


                    <div class="flex justify-end">

                        <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            loading-target="saveNotes">
                            <x-wirekit::icon name="check" />
                            Simpan Catatan
                        </x-wirekit::button>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::form>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        TOTAL
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-3">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Penghasilan
                    </p>

                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ $this->money($this->totalEarning) }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-slate-500">
                        Total Potongan
                    </p>

                    <p class="mt-1 text-xl font-bold text-red-600">
                        {{ $this->money($this->totalDeduction) }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-slate-500">
                        Gaji Bersih
                    </p>

                    <p class="mt-1 text-2xl font-bold text-[#30AFFF]">
                        {{ $this->money($this->totalNet) }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
