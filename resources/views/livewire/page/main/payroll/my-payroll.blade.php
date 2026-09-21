<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <p class="text-sm font-medium text-[#30AFFF]">
                Kompensasi
            </p>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Slip Gaji Saya
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Lihat riwayat penggajian dan slip gaji Anda.
            </p>

        </div>

    </div>


    @if ($latestPayroll)

        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-3">

                            <p class="text-sm font-medium text-slate-500">
                                Slip Gaji Terbaru
                            </p>

                            <x-wirekit::badge
                                :intent="$this->statusIntent($latestPayroll->status)"
                            >
                                {{ $this->statusLabel($latestPayroll->status) }}
                            </x-wirekit::badge>

                        </div>

                        <h2 class="mt-2 text-2xl font-semibold text-slate-900">
                            {{ $latestPayroll->period?->name ?? 'Periode Penggajian' }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $latestPayroll->period?->start_date?->translatedFormat('d M Y') }}
                            —
                            {{ $latestPayroll->period?->end_date?->translatedFormat('d M Y') }}
                        </p>

                    </div>


                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center lg:justify-end">

                        <div class="rounded-2xl bg-slate-50 px-5 py-4">

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Take Home Pay
                            </p>

                            <p class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $this->money($latestPayroll->net_amount) }}
                            </p>

                        </div>


                        <x-wirekit::button
                            type="button"
                            href="{{ route('payroll.my.show', $latestPayroll->id) }}"
                            wire:navigate
                        >
                            <x-wirekit::icon name="document-text" class="size-4" />
                            Lihat Slip
                        </x-wirekit::button>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    @else

        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex flex-col items-center justify-center py-12 text-center">

                    <div class="flex size-12 items-center justify-center rounded-xl bg-slate-100">

                        <x-wirekit::icon name="credit-card" class="size-6 text-slate-500" />

                    </div>

                    <p class="mt-4 text-sm font-semibold text-slate-700">
                        Belum ada slip gaji
                    </p>

                    <p class="mt-1 max-w-md text-sm text-slate-400">
                        Slip gaji akan muncul setelah payroll Anda selesai diproses.
                    </p>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    @endif


    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Riwayat Penggajian
                </h2>

                <p class="text-sm text-slate-500">
                    Daftar slip gaji yang sudah selesai diproses.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table
                    hoverable
                    table-label="Riwayat penggajian"
                >

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Periode
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Tanggal Pembayaran
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Take Home Pay
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

                        @forelse ($payrolls as $payroll)

                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $payroll->period?->name ?? '—' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $payroll->period?->start_date?->translatedFormat('d M Y') }}
                                            —
                                            {{ $payroll->period?->end_date?->translatedFormat('d M Y') }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $payroll->paid_at?->translatedFormat('d M Y') ?? $payroll->period?->payment_date?->translatedFormat('d M Y') ?? '—' }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="right">

                                    <span class="text-sm font-semibold text-slate-800">
                                        {{ $this->money($payroll->net_amount) }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <x-wirekit::badge :intent="$this->statusIntent($payroll->status)">
                                        {{ $this->statusLabel($payroll->status) }}
                                    </x-wirekit::badge>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="right">

                                    <x-wirekit::button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        href="{{ route('payroll.my.show', $payroll->id) }}"
                                        wire:navigate
                                    >
                                        Detail
                                    </x-wirekit::button>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="5">

                                    <div class="py-10 text-center">

                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada riwayat payroll.
                                        </p>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Belum ditemukan payroll yang dapat ditampilkan.
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>


            @if ($payrolls->hasPages())

                <div class="mt-5 border-t border-slate-100 pt-4">

                    {{ $payrolls->links() }}

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
