<x-wirekit::stack gap="md">

    {{-- =====================================================
    PAGE HEADING
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                Organization
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Divisions
            </h1>

            <p class="text-sm text-slate-500">
                Kelola divisi yang terdapat dalam struktur organisasi.
            </p>

        </x-wirekit::stack>

        @can('create-divisi')

            <livewire:components.main.divisi.form-add />

        @endcan

    </div>


    {{-- =====================================================
    DIVISION LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Division List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar divisi yang terdaftar dalam organisasi.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}
                <div class="w-full sm:w-64">

                    <x-wirekit::input
                        placeholder="Cari nama divisi"
                        name="search"
                        wire:model.live.debounce.500ms="search"
                        class="text-black"
                    />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    {{-- =================================================
                    TABLE HEADER
                    ================================================== --}}
                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                          

                            <x-wirekit::table.th
                                sortable
                                column="name"
                            >
                                Division
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Description
                            </x-wirekit::table.th>

                            <x-wirekit::table.th
                                sortable
                                column="teams"
                            >
                                Teams
                            </x-wirekit::table.th>

                            <x-wirekit::table.th
                                sortable
                                column="status"
                            >
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    {{-- =================================================
                    TABLE BODY
                    ================================================== --}}
                    <x-wirekit::table.body>

                        @forelse ($divisis as $divisi)

                            <x-wirekit::table.row>

                            
                                {{-- Division --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 shrink-0
                                                   items-center justify-center
                                                   rounded-lg bg-[#92EEFF]/40"
                                        >

                                            <x-wirekit::icon
                                                name="building-office-2"
                                                class="size-5 text-sky-600"
                                            />

                                        </div>

                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-800">
                                                {{ $divisi->name }}
                                            </p>

                                        </div>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- Description --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-500">
                                        {{ $divisi->description ?: '-' }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Teams --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-600">
                                        {{ $divisi->team->count() }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Status --}}
                                <x-wirekit::table.td>

                                    <span
                                        class="inline-flex items-center rounded-full
                                               px-2.5 py-1 text-xs font-medium
                                               {{ $divisi->is_active === 'active'
                                                    ? 'bg-emerald-50 text-emerald-600'
                                                    : 'bg-red-50 text-red-600' }}"
                                    >
                                        {{ ucfirst($divisi->is_active) }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Actions --}}
                                <x-wirekit::table.td>

                                    <div class="flex justify-end">

                                        @can('show-divisi')

                                            <x-wirekit::button
                                                href="{{ route('divisi.name', $divisi->id) }}"
                                                wire:navigate
                                                type="button"
                                                class="px-3 py-1.5 text-xs"
                                            >
                                                Detail
                                            </x-wirekit::button>

                                        @endcan

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="6">

                                    <div class="py-8 text-center">

                                        <p class="text-sm text-slate-500">
                                            No division data available.
                                        </p>

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>


            {{-- Pagination --}}
            <div class="mt-4">

                {{ $divisis->links() }}

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>