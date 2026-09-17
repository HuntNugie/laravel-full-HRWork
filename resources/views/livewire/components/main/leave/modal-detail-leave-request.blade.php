<x-wirekit::modal name="detail-leave">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Detail Pengajuan Cuti
            </h2>

            <p class="text-sm text-slate-500">
                Informasi lengkap mengenai pengajuan cuti Anda.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::modal.body>

        <x-wirekit::stack gap="md">


            {{-- =================================================
                IDENTITAS PENGAJUAN
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 bg-white p-4">

                <div class="flex items-start justify-between gap-4">

                    <div class="min-w-0">

                        <p class="text-base font-semibold text-slate-900">
                            {{ $request->leaveType?->name ?? 'Jenis Cuti' }}
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Diajukan
                            {{ $request->created_at?->translatedFormat('d F Y') }}
                        </p>

                    </div>


                    {{-- STATUS --}}
                    <div class="shrink-0">

                        @if ($request->status === 'pending')
                            <x-wirekit::badge intent="warning">
                                Menunggu
                            </x-wirekit::badge>
                        @elseif ($request->status === 'approved')
                            <x-wirekit::badge intent="success">
                                Disetujui
                            </x-wirekit::badge>
                        @elseif ($request->status === 'rejected')
                            <x-wirekit::badge intent="danger">
                                Ditolak
                            </x-wirekit::badge>
                        @elseif ($request->status === 'cancelled')
                            <x-wirekit::badge intent="secondary">
                                Dibatalkan
                            </x-wirekit::badge>
                        @endif

                    </div>

                </div>

            </div>


            {{-- =================================================
                PERIODE CUTI
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Periode Cuti
                </p>


                <div class="mt-4 flex items-start gap-3">

                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white">
                        <x-wirekit::icon name="calendar" class="size-5 text-sky-500" />
                    </div>


                    <div>

                        <p class="text-sm font-semibold text-slate-800">

                            {{ \Carbon\Carbon::parse($request->start_date)->translatedFormat('d F Y') }}

                        </p>

                        <p class="mt-0.5 text-sm text-slate-400">

                            s.d.
                            {{ \Carbon\Carbon::parse($request->end_date)->translatedFormat('d F Y') }}

                        </p>

                    </div>

                </div>


                <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4">

                    <span class="text-sm text-slate-500">
                        Durasi
                    </span>

                    <span class="text-sm font-semibold text-slate-800">
                        {{ $request->total_days }} Hari
                    </span>

                </div>

            </div>


            {{-- =================================================
                INFORMASI PENGAJUAN
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Informasi Pengajuan
                </p>


                <div class="mt-4 space-y-3">

                    <div class="flex items-center justify-between gap-4">

                        <span class="text-sm text-slate-500">
                            Jenis Cuti
                        </span>

                        <span class="text-sm font-medium text-slate-800">
                            {{ $request->leaveType?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4">

                        <span class="text-sm text-slate-500">
                            Status
                        </span>

                        <span class="text-sm font-medium text-slate-800">
                            @if ($request->status === 'pending')
                                Menunggu
                            @elseif ($request->status === 'approved')
                                Disetujui
                            @elseif ($request->status === 'rejected')
                                Ditolak
                            @elseif ($request->status === 'cancelled')
                                Dibatalkan
                            @else
                                -
                            @endif
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4">

                        <span class="text-sm text-slate-500">
                            Tanggal Pengajuan
                        </span>

                        <span class="text-sm font-medium text-slate-800">
                            {{ $request->created_at?->translatedFormat('d F Y') }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- =================================================
                ALASAN
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Alasan
                </p>

                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ $request->reason ?: 'Tidak ada alasan yang diberikan.' }}
                </p>

            </div>


            {{-- =================================================
                STATUS INFORMATION
            ================================================== --}}

            @if ($request->status === 'pending')

                <div class="flex items-start gap-3 rounded-xl border border-sky-100 bg-sky-50 p-4">

                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                        <x-wirekit::icon name="information-circle" class="size-5 text-sky-500" />
                    </div>


                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Menunggu Persetujuan
                        </p>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Pengajuan cuti Anda sedang menunggu
                            persetujuan dari pihak yang berwenang.
                        </p>

                    </div>

                </div>
            @elseif ($request->status === 'approved')
                <div class="flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4">

                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                        <x-wirekit::icon name="check-circle" class="size-5 text-emerald-500" />
                    </div>


                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Pengajuan Disetujui
                        </p>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Pengajuan cuti ini telah disetujui.
                        </p>

                    </div>

                </div>
            @elseif ($request->status === 'rejected')
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                            <x-wirekit::icon name="x-circle" class="size-5 text-rose-500" />
                        </div>


                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Pengajuan Ditolak
                            </p>

                            @if ($request->rejection_reason)
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    {{ $request->rejection_reason }}
                                </p>
                            @else
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Pengajuan cuti ini telah ditolak.
                                </p>
                            @endif

                        </div>

                    </div>

                </div>
            @elseif ($request->status === 'cancelled')
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                            <x-wirekit::icon name="minus-circle" class="size-5 text-slate-500" />
                        </div>


                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Pengajuan Dibatalkan
                            </p>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Pengajuan cuti ini telah dibatalkan.
                            </p>

                        </div>

                    </div>

                </div>

            @endif

        </x-wirekit::stack>

    </x-wirekit::modal.body>


    {{-- =====================================================
        FOOTER
    ====================================================== --}}
    <x-wirekit::modal.footer>

        <x-wirekit::row justify="end" gap="sm">

            <x-wirekit::modal.close>

                <x-wirekit::button variant="outline" type="button">
                    Tutup
                </x-wirekit::button>

            </x-wirekit::modal.close>


            {{-- BATALKAN --}}
            @can('cancel-leave')

                @if ($request->status === 'pending')
                    <livewire:components.main.leave.modal-cancel-leave :request="$request" :key="'cancel-from-detail-' . $request->id">

                        <x-wirekit::button type="button" variant="outline" intent="danger">
                            Batalkan Pengajuan
                        </x-wirekit::button>

                    </livewire:components.main.leave.modal-cancel-leave>
                @endif

            @endcan

        </x-wirekit::row>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
