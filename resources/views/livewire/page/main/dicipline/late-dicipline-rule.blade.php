<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <x-wirekit::stack gap="1">

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Kedisiplinan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Aturan Keterlambatan
        </h1>

        <p class="text-sm text-slate-500">
            Atur konsekuensi keterlambatan karyawan berdasarkan jumlah kejadian
            dalam periode tertentu.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        ATTENDANCE SETTINGS INFO
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                <div class="flex items-start gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-500">
                        <x-wirekit::icon name="clock" class="size-5" />
                    </div>

                    <div>

                        <p class="text-sm font-semibold text-slate-800">
                            Attendance Settings
                        </p>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Penentuan apakah attendance dianggap terlambat tetap
                            mengikuti pengaturan presensi.
                        </p>

                    </div>

                </div>


                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Toleransi Keterlambatan
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-800">
                        {{ $this->attendanceSetting?->late_tolerance_minutes ?? 0 }}
                        menit
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        ACTIVE RULE
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-base font-semibold text-slate-900">
                        Aturan Aktif
                    </h2>

                    <p class="text-sm text-slate-500">
                        Aturan yang saat ini digunakan untuk konsekuensi keterlambatan.
                    </p>

                </x-wirekit::stack>


                @can('edit-late-discipline-rule')
                    <livewire:components.main.dicipline.modal-edit-late-dicipline-rule>

                        <x-wirekit::button type="button" variant="outline">
                            Edit Aturan
                        </x-wirekit::button>

                    </livewire:components.main.dicipline.modal-edit-late-dicipline-rule>
                @endcan

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @forelse ($this->activeRules as $rule)
                <div class="rounded-xl border border-slate-200 bg-white">

                    {{-- Rule Header --}}
                    <div
                        class="flex flex-col gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start sm:justify-between">

                        <div class="flex items-start gap-4">

                            <div
                                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                                <x-wirekit::icon name="clock" class="size-5" />
                            </div>

                            <div>

                                <div class="flex flex-wrap items-center gap-2">

                                    <h3 class="text-sm font-semibold text-slate-900">
                                        {{ $rule->name }}
                                    </h3>

                                    <x-wirekit::badge intent="success">
                                        Aktif
                                    </x-wirekit::badge>

                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $rule->description ?: 'Tidak ada deskripsi aturan.' }}
                                </p>

                            </div>

                        </div>
                    </div>


                    {{-- Rule Configuration --}}
                    <div class="grid gap-4 p-5 sm:grid-cols-3">

                        <div class="rounded-xl bg-slate-50 p-4">

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Kelipatan
                            </p>

                            <p class="mt-1 text-lg font-semibold text-slate-800">
                                {{ $this->thresholdLabel($rule->threshold) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                keterlambatan
                            </p>

                        </div>


                        <div class="rounded-xl bg-slate-50 p-4">

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Konsekuensi
                            </p>

                            <p class="mt-1 text-lg font-semibold text-slate-800">
                                {{ $this->money($rule->action_amount) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                setiap kelipatan
                            </p>

                        </div>


                        <div class="rounded-xl bg-slate-50 p-4">

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Periode
                            </p>

                            <p class="mt-1 text-lg font-semibold text-slate-800">
                                {{ $this->periodLabel($rule->period_type) }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                perhitungan keterlambatan
                            </p>

                        </div>

                    </div>


                    {{-- Preview --}}
                    <div class="border-t border-slate-100 p-5">

                        <p class="text-sm font-medium text-slate-700">
                            Preview Perhitungan
                        </p>

                        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

                            @php
                                $threshold = max((int) $rule->threshold, 1);
                            @endphp

                            {{-- Preview 1 --}}
                            <div class="rounded-lg border border-slate-200 px-4 py-3">

                                <p class="text-xs text-slate-400">
                                    @if ($threshold === 1)
                                        1 kali
                                    @else
                                        1–{{ $threshold - 1 }} kali
                                    @endif
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $this->money($this->previewAmount($rule, $threshold - 1)) }}
                                </p>

                            </div>


                            {{-- Preview 2 --}}
                            <div class="rounded-lg border border-slate-200 px-4 py-3">

                                <p class="text-xs text-slate-400">
                                    {{ $threshold }}–{{ $threshold * 2 - 1 }} kali
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $this->money($this->previewAmount($rule, $threshold * 2 - 1)) }}
                                </p>

                            </div>


                            {{-- Preview 3 --}}
                            <div class="rounded-lg border border-slate-200 px-4 py-3">

                                <p class="text-xs text-slate-400">
                                    {{ $threshold * 2 }}–{{ $threshold * 3 - 1 }} kali
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $this->money($this->previewAmount($rule, $threshold * 3 - 1)) }}
                                </p>

                            </div>


                            {{-- Preview 4 --}}
                            <div class="rounded-lg border border-slate-200 px-4 py-3">

                                <p class="text-xs text-slate-400">
                                    {{ $threshold * 3 }}–{{ $threshold * 4 - 1 }} kali
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $this->money($this->previewAmount($rule, $threshold * 4 - 1)) }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

                @if (!$loop->last)
                    <div class="h-4"></div>
                @endif

            @empty

                <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center">

                    <div
                        class="mx-auto flex size-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <x-wirekit::icon name="clock" class="size-5" />
                    </div>

                    <p class="mt-3 text-sm font-semibold text-slate-700">
                        Belum ada aturan aktif
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Belum ada aturan keterlambatan yang sedang aktif.
                    </p>

                </div>
            @endforelse

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
