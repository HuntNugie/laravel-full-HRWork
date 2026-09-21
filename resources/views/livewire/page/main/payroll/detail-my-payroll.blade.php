<div class="space-y-6">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

        <x-wirekit::stack gap="xs">

            <a
                href="{{ route('payroll.my.view') }}"
                wire:navigate
                class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]"
            >
                <span aria-hidden="true">&larr;</span>
                Kembali ke Slip Gaji
            </a>

            <span class="text-sm font-medium text-[#30AFFF]">
                Kompensasi
            </span>

            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Slip Gaji
                </h1>

                <x-wirekit::badge :intent="$this->statusIntent()">
                    {{ $this->statusLabel() }}
                </x-wirekit::badge>

            </div>

            <p class="text-sm text-slate-500">
                {{ $period->name }}
                ·
                {{ $period->start_date?->translatedFormat('d M Y') }}
                —
                {{ $period->end_date?->translatedFormat('d M Y') }}
            </p>

        </x-wirekit::stack>


        <x-wirekit::button
            type="button"
            variant="outline"
            href="{{ route('payroll.print.slip', [
                'period' => $period->id,
                'payroll' => $payroll->id,
            ]) }}"
            target="_blank"
        >
            <x-wirekit::icon name="printer" class="size-4" />
            Cetak Slip
        </x-wirekit::button>

    </div>


    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Penggajian
                </h2>

                <p class="text-sm text-slate-500">
                    Ringkasan payroll untuk periode ini.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Karyawan
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $employee->user?->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Kode Karyawan
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $employee->employee_code ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Jabatan
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $payroll->position_name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Pembayaran
                    </p>

                    <p class="mt-1 text-sm text-slate-700">
                        {{ $payroll->paid_at?->translatedFormat('d M Y') ?? $period->payment_date?->translatedFormat('d M Y') ?? '—' }}
                    </p>
                </div>

            </div>


            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

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
                            Hari Dibayar
                        </p>

                        <p class="mt-1 text-2xl font-bold text-[#30AFFF]">
                            {{ number_format($payroll->paid_days) }}
                        </p>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                <x-wirekit::card>

                    <x-wirekit::card.body>

                        <p class="text-sm text-slate-500">
                            Cuti Dibayar
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-900">
                            {{ number_format($payroll->paid_leave_days) }}
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

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Penghasilan
                </h2>

                <p class="text-sm text-slate-500">
                    Rincian komponen penghasilan pada slip gaji ini.
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
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $item->name }}
                                        </p>

                                        @if ($item->description)
                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $item->description }}
                                            </p>
                                        @endif
                                    </div>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <x-wirekit::badge :intent="$item->source === 'system' ? 'info' : 'neutral'">
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


    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Potongan
                </h2>

                <p class="text-sm text-slate-500">
                    Rincian komponen potongan pada slip gaji ini.
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

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $item->name }}
                                        </p>

                                        @if ($item->description)
                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $item->description }}
                                            </p>
                                        @endif
                                    </div>

                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <x-wirekit::badge :intent="$item->source === 'system' ? 'info' : 'neutral'">
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
                                        Belum ada komponen potongan.
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

                    <p class="mt-1 text-xl font-bold text-slate-900">
                        {{ $this->money($payroll->deduction_amount) }}
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Total Diterima
                    </p>

                    <p class="mt-1 text-3xl font-bold text-slate-900">
                        {{ $this->money($payroll->net_amount) }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $payroll->status === 'paid'
                            ? 'Payroll sudah dibayarkan.'
                            : 'Payroll sudah selesai diproses.' }}
                    </p>

                </div>

                <x-wirekit::badge
                    :intent="$this->statusIntent()"
                    size="lg"
                >
                    {{ $this->statusLabel() }}
                </x-wirekit::badge>

            </div>

            @if ($payroll->notes)

                <div class="mt-6 border-t border-slate-100 pt-5">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Catatan
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-slate-700">
                        {{ $payroll->notes }}
                    </p>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
