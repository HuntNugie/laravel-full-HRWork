<x-wirekit::modal name="detail-absence">

    {{-- =====================================================
        TRIGGER
    ====================================================== --}}
    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Detail Pengajuan
            </h2>

            <p class="text-sm text-slate-500">
                Informasi lengkap mengenai pengajuan izin atau sakit.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    {{-- =====================================================
        BODY
    ====================================================== --}}
    <x-wirekit::modal.body>

        <div class="space-y-6">

            {{-- =================================================
                EMPLOYEE
            ================================================== --}}
            <div class="flex items-center gap-3">

                <div class="size-12 shrink-0 overflow-hidden rounded-full bg-slate-100">
                    @if ($absence?->employees?->user?->getFirstMediaUrl('avatar'))
                        <img src="{{ $absence?->employees?->user?->getFirstMediaUrl('avatar') }}" alt="Nugie Kurniawan"
                            class="size-full object-cover">
                    @else
                        <img src="{{ asset('assets/nonProfile.jpg') }}" alt="Nugie Kurniawan"
                            class="size-full object-cover">
                    @endif
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-900">
                        {{ $absence?->employees?->user->name }}
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ $absence?->employees?->employee_code }}
                    </p>
                </div>

            </div>


            {{-- =================================================
                STATUS
            ================================================== --}}
            <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status Pengajuan
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $absence->status }}
                    </p>
                </div>

                @if ($absence->status === 'pending')
                    <x-wirekit::badge variant="warning" intent="warning">
                        Menunggu
                    </x-wirekit::badge>
                @elseif ($absence->status === 'approved')
                    <x-wirekit::badge variant="success" intent="success">
                        Disetujui
                    </x-wirekit::badge>
                @else
                    <x-wirekit::badge variant="danger" intent="danger">
                        Di tolak
                    </x-wirekit::badge>
                @endif

            </div>


            {{-- =================================================
                DETAIL PENGAJUAN
            ================================================== --}}
            <div class="grid gap-4 sm:grid-cols-2">

                {{-- Jenis --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Jenis Pengajuan
                    </span>

                    <div class="mt-1">
                        <x-wirekit::badge variant="info">
                            {{ $absence->type }}
                        </x-wirekit::badge>
                    </div>

                </div>



                {{-- Tanggal Mulai --}}
                <div>

                    <span class="text-xs font-medium text-slate-400">
                        Tanggal Pengajuan
                    </span>

                    <p class="mt-1 text-sm font-medium text-slate-800">
                        {{ $absence->date->format('d F Y') }}
                    </p>

                </div>

            </div>


            {{-- =================================================
                ALASAN
            ================================================== --}}
            <div>

                <span class="text-xs font-medium text-slate-400">
                    Alasan
                </span>

                <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="text-sm leading-6 text-slate-700">
                        {{ $absence->reason }}
                    </p>

                </div>

            </div>
        </div>

    </x-wirekit::modal.body>


    {{-- =====================================================
        FOOTER
    ====================================================== --}}
    <x-wirekit::modal.footer>

        <x-wirekit::row justify="between" gap="sm">




            <x-wirekit::row gap="sm">


                @if ($absence->status === 'pending')
                    <x-wirekit::button type="button" variant="danger" intent="danger" wire:click='reject'>
                        Tolak Pengajuan
                    </x-wirekit::button>

                    <x-wirekit::button type="button" intent="success" wire:click='approve'>
                        Setujui Pengajuan
                    </x-wirekit::button>
                @endif
            </x-wirekit::row>

        </x-wirekit::row>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
