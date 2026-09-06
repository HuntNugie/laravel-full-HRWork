<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                Hari Libur
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola tanggal yang ditetapkan sebagai hari libur perusahaan.
            </p>
        </div>

    </x-wirekit::stack>


    {{-- =====================================================
        HOLIDAY CARD
    ====================================================== --}}
    <x-wirekit::card>

        {{-- =================================================
            HEADER
        ================================================== --}}
        <x-wirekit::card.header>

            <x-wirekit::row justify="between" align="center" gap="md">

                <x-wirekit::stack gap="xs">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Hari Libur {{ now()->year }}
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar tanggal yang tidak termasuk dalam hari kerja.
                    </p>

                </x-wirekit::stack>


                @can('create-holiday')
                    <livewire:components.main.holiday.form-add>
                        <x-wirekit::button size="sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>

                            Tambah Hari Libur
                        </x-wirekit::button>
                    </livewire:components.main.holiday.form-add>
                @endcan


            </x-wirekit::row>

        </x-wirekit::card.header>


        {{-- =================================================
            FILTER
        ================================================== --}}
        <x-wirekit::card.body>

            <x-wirekit::row gap="sm" align="end">


                <div class="w-full max-w-sm">

                    <x-wirekit::input label="Cari" name="search" placeholder="Cari hari libur..."
                        wire:model.live.debounce.400ms="search" />

                </div>

            </x-wirekit::row>

        </x-wirekit::card.body>


        <x-wirekit::divider />


        {{-- =================================================
            HOLIDAY LIST
        ================================================== --}}
        <x-wirekit::card.body>

            <x-wirekit::stack gap="sm" class="wk-scrollbar overflow-auto max-h-150">

                {{-- =========================================
                    HOLIDAY 1
                ========================================== --}}
                @forelse ($holidays as $holiday)
                    <div
                        class="group rounded-xl border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50/50">

                        <x-wirekit::row justify="between" align="center" gap="md">

                            <div class="flex min-w-0 items-center gap-4">

                                {{-- DATE --}}
                                <div
                                    class="flex size-12 shrink-0 flex-col items-center justify-center rounded-xl bg-[#92EEFF]">

                                    <span class="text-[10px] font-medium uppercase text-cyan-700">
                                        {{ $holiday->date->format('M') }}
                                    </span>

                                    <span class="text-lg font-semibold leading-5 text-cyan-900">
                                        {{ $holiday->date->day }}
                                    </span>

                                </div>


                                {{-- INFO --}}
                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <p class="text-sm font-semibold text-slate-900">
                                            {{ $holiday->name }}
                                        </p>


                                    </div>

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $holiday->date->translatedFormat('l, d F Y') }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $holiday->description ?? 'Tidak ada deskripsi' }}

                                    </p>

                                </div>

                            </div>


                            {{-- ACTION --}}

                            <x-wirekit::stack gap="sm">
                                @can('update-holiday')
                                    <livewire:components.main.holiday.form-edit :holiday="$holiday">
                                        <x-wirekit::button intent="primary">
                                            Edit
                                        </x-wirekit::button>
                                    </livewire:components.main.holiday.form-edit>
                                @endcan

                                @can('delete-holiday')
                                    <x-wirekit::button intent="danger">
                                        Hapus
                                    </x-wirekit::button>
                                @endcan


                            </x-wirekit::stack>

                        </x-wirekit::row>

                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-300 mb-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-medium text-slate-500">
                            Tidak ada hari libur terdaftar
                        </p>
                        <p class="mt-1 text-xs text-slate-400">
                            Tambahkan hari libur untuk memulai
                        </p>
                    </div>
                @endforelse





            </x-wirekit::stack>

        </x-wirekit::card.body>


        {{-- =================================================
            FOOTER
        ================================================== --}}
        <x-wirekit::card.footer>

            <x-wirekit::row justify="between" align="center">

                <p class="text-sm text-slate-500">
                    {{ $holidays->count() }} hari libur terdaftar pada tahun {{ now()->year }}.
                </p>

            </x-wirekit::row>

        </x-wirekit::card.footer>

    </x-wirekit::card>


</div>
