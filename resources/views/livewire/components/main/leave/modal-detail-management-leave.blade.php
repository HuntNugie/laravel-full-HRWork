<x-wirekit::modal name="management-leave-detail-{{ $request->id }}" size="xl">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <div class="flex items-start justify-between gap-4">

            <x-wirekit::stack gap="xs">

                <h2 class="text-lg font-semibold text-slate-900">
                    Detail Pengajuan Cuti
                </h2>

                <p class="text-sm text-slate-500">
                    Tinjau informasi pengajuan sebelum mengambil keputusan.
                </p>

            </x-wirekit::stack>


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

    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::modal.body>

        <div class="grid gap-4 lg:grid-cols-2">


            {{-- =================================================
                KARYAWAN
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Karyawan
                </p>

                <div class="mt-4 flex items-center gap-3">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                        <x-wirekit::icon name="user" class="size-5 text-slate-500" />
                    </div>

                    <div class="min-w-0">

                        <p class="truncate text-sm font-semibold text-slate-800">
                            {{ $request->employees?->user?->name ?? '-' }}
                        </p>

                        <p class="mt-0.5 text-xs text-slate-400">
                            {{ $request->employees?->employee_code ?? '-' }}
                        </p>

                    </div>

                </div>

            </div>


            {{-- =================================================
                PERIODE
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Periode Cuti
                </p>

                <div class="mt-4 flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($request->start_date)->translatedFormat('d F Y') }}
                        </p>

                        <p class="mt-1 text-sm text-slate-400">
                            s.d.
                            {{ \Carbon\Carbon::parse($request->end_date)->translatedFormat('d F Y') }}
                        </p>

                    </div>


                    <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                        <x-wirekit::icon name="calendar" class="size-5 text-sky-500" />
                    </div>

                </div>

                <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-3">

                    <span class="text-sm text-slate-500">
                        Durasi
                    </span>

                    <span class="text-sm font-semibold text-slate-800">
                        {{ $request->total_days }} Hari
                    </span>

                </div>

            </div>


            {{-- =================================================
                JENIS CUTI
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Jenis Cuti
                </p>

                <div class="mt-4 flex items-center gap-3">

                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                        <x-wirekit::icon name="calendar" class="size-5 text-sky-500" />
                    </div>

                    <div>

                        <p class="text-sm font-semibold text-slate-800">
                            {{ $request->leaveType?->name ?? '-' }}
                        </p>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Berdasarkan entitlement contract karyawan.
                        </p>

                    </div>

                </div>

            </div>


            {{-- =================================================
                CONTRACT
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Contract
                </p>

                <div class="mt-4 space-y-3">

                    <div class="flex items-center justify-between gap-4">

                        <span class="text-sm text-slate-500">
                            Nomor
                        </span>

                        <span class="text-sm font-medium text-slate-800">
                            {{ $request->employeeContract?->contract_number ?? '-' }}
                        </span>

                    </div>

                    <div class="flex items-center justify-between gap-4">

                        <span class="text-sm text-slate-500">
                            Jabatan
                        </span>

                        <span class="text-sm font-medium text-slate-800">
                            {{ $request->employeeContract?->position_name ?? '-' }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- =================================================
                JATAH CUTI
            ================================================== --}}
            <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">
                            Informasi Jatah
                        </p>

                        <p class="mt-1 text-xs text-amber-700">
                            Pemeriksaan kembali dilakukan saat approve.
                        </p>

                    </div>

                    <x-wirekit::icon name="information-circle" class="size-5 shrink-0 text-amber-500" />

                </div>

                <div class="mt-4 grid grid-cols-2 gap-4">

                    <div>

                        <p class="text-xs text-slate-500">
                            Jatah Contract
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            —
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-slate-500">
                            Sudah Digunakan
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            —
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-slate-500">
                            Sisa Jatah
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            —
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-slate-500">
                            Pengajuan
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $request->total_days }} Hari
                        </p>

                    </div>

                </div>

            </div>


            {{-- =================================================
                ALASAN
            ================================================== --}}
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Alasan Pengajuan
                </p>

                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ $request->reason ?: 'Tidak ada alasan yang diberikan.' }}
                </p>

            </div>


            {{-- =================================================
                ALASAN PENOLAKAN
            ================================================== --}}
            @if ($request->status === 'rejected')
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-4 lg:col-span-2">

                    <div class="flex items-start gap-3">

                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                            <x-wirekit::icon name="x-circle" class="size-5 text-rose-500" />
                        </div>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Alasan Penolakan
                            </p>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                {{ $request->rejection_reason ?: 'Tidak ada alasan penolakan.' }}
                            </p>

                        </div>

                    </div>

                </div>
            @endif


            {{-- =================================================
                INFORMASI APPROVAL
            ================================================== --}}
            @if ($request->status === 'approved')

                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 lg:col-span-2">

                    <div class="flex items-start gap-3">

                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                            <x-wirekit::icon name="check-circle" class="size-5 text-emerald-500" />
                        </div>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Pengajuan Disetujui
                            </p>

                            @if ($request->approved_at)
                                <p class="mt-1 text-sm text-slate-500">
                                    Disetujui pada
                                    {{ \Carbon\Carbon::parse($request->approved_at)->translatedFormat('d F Y H:i') }}
                                </p>
                            @else
                                <p class="mt-1 text-sm text-slate-500">
                                    Pengajuan ini telah disetujui.
                                </p>
                            @endif

                        </div>

                    </div>

                </div>

            @endif


            {{-- =================================================
                INFORMASI PEMBATALAN
            ================================================== --}}
            @if ($request->status === 'cancelled')

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:col-span-2">

                    <div class="flex items-start gap-3">

                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                            <x-wirekit::icon name="minus-circle" class="size-5 text-slate-500" />
                        </div>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Pengajuan Dibatalkan
                            </p>

                            @if ($request->cancelled_at)
                                <p class="mt-1 text-sm text-slate-500">
                                    Dibatalkan pada
                                    {{ \Carbon\Carbon::parse($request->cancelled_at)->translatedFormat('d F Y H:i') }}
                                </p>
                            @else
                                <p class="mt-1 text-sm text-slate-500">
                                    Pengajuan ini telah dibatalkan oleh karyawan.
                                </p>
                            @endif

                        </div>

                    </div>

                </div>

            @endif

        </div>

    </x-wirekit::modal.body>


    {{-- =====================================================
        FOOTER
    ====================================================== --}}
    <x-wirekit::modal.footer>

        <x-wirekit::row justify="end" gap="sm">

            <x-wirekit::modal.close>

                <x-wirekit::button type="button" variant="outline">
                    Tutup
                </x-wirekit::button>

            </x-wirekit::modal.close>


            @can('process-leave')

                @if ($request->status === 'pending')
                    <livewire:components.main.leave.modal-reject-leave :request="$request" :key="'reject-leave-' . $request->id">
                        <x-wirekit::button type="button" variant="outline" intent="danger">
                            Tolak
                        </x-wirekit::button>
                    </livewire:components.main.leave.modal-reject-leave>


                    <livewire:components.main.leave.modal-approve-leave :request="$request" :key="'approve-leave-' . $request->id">
                        <x-wirekit::button type="button" intent="success">
                            Setujui
                        </x-wirekit::button>
                    </livewire:components.main.leave.modal-approve-leave>
                @endif

            @endcan

        </x-wirekit::row>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
