<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                Payroll
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Benefits
            </h1>

            <p class="text-sm text-slate-500">
                Kelola jenis tunjangan yang tersedia dalam organisasi.
            </p>

        </x-wirekit::stack>

        @can('create-benefit')
            <livewire:components.main.benefit.form-add>
                <x-wirekit::button class="bg-[#30AFFF] text-white hover:bg-sky-500">
                    <x-wirekit::icon name="plus" />
                    Benefit
                </x-wirekit::button>
            </livewire:components.main.benefit.form-add>
        @endcan

    </div>


    {{-- =====================================================
        BENEFIT LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Benefit List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar jenis tunjangan yang tersedia dalam organisasi.
                    </p>

                </x-wirekit::stack>

                {{-- Search --}}
                <div class="w-full sm:w-64">
                    <x-wirekit::input placeholder="Cari nama benefit" name="search" wire:model.live.debounce.500ms="search" class="text-black" />
                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>
                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="name">
                                Benefit
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Description
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Contracts
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>
                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                   @forelse ($benefits as $benefit)
                    {{-- Benefit 1 --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800">
                                       {{$benefit->name}}
                                    </p>
                                </div>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td>
                                <p class="max-w-md truncate text-sm text-slate-500">
                                    {{ $benefit->description }}
                                </p>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td>
                                <span class="text-sm text-slate-700">
                                   {{ $benefit->contracts_count  }}
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td>
                                <x-wirekit::button type="button" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>
                            </x-wirekit::table.td>

                        </x-wirekit::table.row>

                   @empty
                        <x-wirekit::table.row>
                            <x-wirekit::table.td colspan="5">
                                <div class="py-8 text-center text-sm text-slate-500">
                                    Belum ada data benefit.
                                </div>
                            </x-wirekit::table.td>
                        </x-wirekit::table.row>
                   @endforelse




                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
