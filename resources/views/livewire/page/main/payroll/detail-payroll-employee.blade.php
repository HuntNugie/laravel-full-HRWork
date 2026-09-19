<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

        <x-wirekit::stack gap="xs">

            <a href="{{ route('payroll.show', $period) }}" wire:navigate
                class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">
                <span aria-hidden="true">&larr;</span>
                Kembali ke Detail Periode
            </a>

            <span class="text-sm font-medium text-[#30AFFF]">
                Penggajian
            </span>

            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Detail Payroll
                </h1>

                <x-wirekit::badge :intent="$this->statusIntent()">
                    {{ $this->statusLabel() }}
                </x-wirekit::badge>

            </div>

            <p class="text-sm text-slate-500">
                Rincian payroll
                <span class="font-medium text-slate-700">
                    {{ $employee->user?->name ?? 'Karyawan tidak ditemukan' }}
                </span>
                untuk periode
                {{ $period->start_date->format('d M Y') }}
                —
                {{ $period->end_date->format('d M Y') }}.
            </p>

        </x-wirekit::stack>


        {{-- =================================================
            HEADER ACTION
        ================================================== --}}

        <div class="flex flex-wrap gap-2">

            <x-wirekit::button type="button" variant="outline" disabled
                title="Aksi akan tersedia pada tahap berikutnya">
                Edit Payroll
            </x-wirekit::button>

        </div>

    </div>


    {{-- =====================================================
        EMPLOYEE INFORMATION
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Karyawan
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi karyawan dan kontrak yang digunakan
                    saat payroll dibuat.
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
                        Status Payroll
                    </span>

                    <div class="mt-2">
                        <x-wirekit::badge :intent="$this->statusIntent()">
                            {{ $this->statusLabel() }}
                        </x-wirekit::badge>
                    </div>

                </div>

            </div>


            <div class="mt-6 border-t border-slate-100 pt-6">

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                    <div>

                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Periode
                        </span>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $period->name }}
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


                    <div>

                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Diproses Pada
                        </span>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $payroll->processed_at?->format('d M Y H:i') ?? '-' }}
                        </p>

                    </div>


                    <div>

                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Dibayar Pada
                        </span>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $payroll->paid_at?->format('d M Y H:i') ?? '-' }}
                        </p>

                    </div>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        ATTENDANCE SUMMARY
    ====================================================== --}}

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <x-wirekit::card>

            <x-wirekit::card.body>

                <p class="text-sm text-slate-500">
                    Hari Kerja
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ number_format($payroll->working_days) }}
                </p>

            </x-wirekit::card.body>

        </x-wirekit::card>


        <x-wirekit::card>

            <x-wirekit::card.body>

                <p class="text-sm text-slate-500">
                    Hadir
                </p>

                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ number_format($payroll->present_days) }}
                </p>

            </x-wirekit::card.body>

        </x-wirekit::card>


        <x-wirekit::card>

            <x-wirekit::card.body>

                <p class="text-sm text-slate-500">
                    Terlambat
                </p>

                <p class="mt-1 text-2xl font-bold text-amber-600">
                    {{ number_format($payroll->late_days) }}
                </p>

            </x-wirekit::card.body>

        </x-wirekit::card>


        <x-wirekit::card>

            <x-wirekit::card.body>

                <p class="text-sm text-slate-500">
                    Tidak Hadir
                </p>

                <p class="mt-1 text-2xl font-bold text-red-600">
                    {{ number_format($payroll->absent_days) }}
                </p>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =====================================================
        LEAVE SUMMARY
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Ringkasan Ketidakhadiran
                </h2>

                <p class="text-sm text-slate-500">
                    Rekap hari yang memengaruhi perhitungan payroll.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

                <div>

                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Cuti Dibayar
                    </span>

                    <p class="mt-1 text-lg font-bold text-slate-800">
                        {{ number_format($payroll->paid_leave_days) }} hari
                    </p>

                </div>


                <div>

                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Cuti Tidak Dibayar
                    </span>

                    <p class="mt-1 text-lg font-bold text-slate-800">
                        {{ number_format($payroll->unpaid_leave_days) }} hari
                    </p>

                </div>


                <div>

                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Hari Dibayar
                    </span>

                    <p class="mt-1 text-lg font-bold text-[#30AFFF]">
                        {{ number_format($payroll->paid_days) }} hari
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        EARNINGS
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Penghasilan
                </h2>

                <p class="text-sm text-slate-500">
                    Seluruh komponen penghasilan payroll karyawan.
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
                                Sumber
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Jumlah
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($this->earningItems as $item)
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

                                    <x-wirekit::badge :intent="$item->source === 'system' ? 'info' : 'secondary'">
                                        {{ $item->source === 'system' ? 'Sistem' : 'Manual' }}
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

                                <x-wirekit::table.td colspan="3">

                                    <div class="py-8 text-center text-sm text-slate-400">
                                        Belum ada komponen penghasilan.
                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>


            <div class="mt-5 flex justify-end border-t border-slate-100 pt-5">

                <div class="text-right">

                    <p class="text-sm text-slate-500">
                        Total Penghasilan
                    </p>

                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ $this->money($payroll->gross_amount) }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        DEDUCTIONS
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Potongan
                </h2>

                <p class="text-sm text-slate-500">
                    Seluruh komponen potongan payroll karyawan.
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
                                Sumber
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Jumlah
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($this->deductionItems as $item)
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

                                    <x-wirekit::badge :intent="$item->source === 'system' ? 'info' : 'secondary'">
                                        {{ $item->source === 'system' ? 'Sistem' : 'Manual' }}
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="right">

                                    <span class="text-sm font-semibold text-red-600">
                                        {{ $this->money($item->amount) }}
                                    </span>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="3">

                                    <div class="py-8 text-center text-sm text-slate-400">
                                        Belum ada potongan.
                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>


            <div class="mt-5 flex justify-end border-t border-slate-100 pt-5">

                <div class="text-right">

                    <p class="text-sm text-slate-500">
                        Total Potongan
                    </p>

                    <p class="mt-1 text-xl font-bold text-red-600">
                        {{ $this->money($payroll->deduction_amount) }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        PAYROLL TOTAL
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-3">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Penghasilan
                    </p>

                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ $this->money($payroll->gross_amount) }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-slate-500">
                        Total Potongan
                    </p>

                    <p class="mt-1 text-xl font-bold text-red-600">
                        {{ $this->money($payroll->deduction_amount) }}
                    </p>

                </div>


                <div>

                    <p class="text-sm text-slate-500">
                        Gaji Bersih
                    </p>

                    <p class="mt-1 text-2xl font-bold text-[#30AFFF]">
                        {{ $this->money($payroll->net_amount) }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        NOTES
    ====================================================== --}}

    @if ($payroll->notes)
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Catatan
                    </h2>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <p class="text-sm leading-6 text-slate-600">
                    {{ $payroll->notes }}
                </p>

            </x-wirekit::card.body>

        </x-wirekit::card>
    @endif

</x-wirekit::stack>
