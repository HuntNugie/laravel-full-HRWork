<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <a href="{{ route('benefit.view') }}" wire:navigate
                class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">
                <span aria-hidden="true">&larr;</span>
                Kembali
            </a>

            <span class="text-sm font-medium text-[#30AFFF]">
                Payroll
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Tunjangan Transport
            </h1>

            <p class="text-sm text-slate-500">
                Detail informasi tunjangan dan employee yang menerima tunjangan ini.
            </p>

        </x-wirekit::stack>


        <div class="flex items-center gap-2">

            <livewire:components.main.benefit.form-edit>

                <x-wirekit::button type="button" class="px-3 py-1.5 text-sm">
                    <x-wirekit::icon name="pencil" />
                    Edit Benefit
                </x-wirekit::button>

            </livewire:components.main.benefit.form-edit>

        </div>

    </div>


    {{-- =====================================================
    BENEFIT INFORMATION
====================================================== --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Main Information --}}
        <x-wirekit::card class="lg:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Benefit Information
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi mengenai tunjangan.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="space-y-6">

                    {{-- Benefit Name --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Benefit Name
                        </p>

                        <p class="mt-1 text-lg font-semibold text-slate-800">
                            {{ $benefit->name }}
                        </p>

                    </div>


                    {{-- Description --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Description
                        </p>

                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            {{ $benefit->description }}
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Summary --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Summary
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan penggunaan benefit.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="space-y-5">

                    {{-- Total Employees --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Total Employees
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-800">
                           {{count($benefit->contracts)}}
                        </p>

                    </div>


                    {{-- Created At --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Created At
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $benefit->created_at->format('d F Y') }}
                        </p>

                    </div>


                    {{-- Last Updated --}}
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Last Updated
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                          {{$benefit->updated_at->diffForHumans()}}
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>





    {{-- =====================================================
        EMPLOYEE LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Employees Receiving This Benefit
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar employee yang mendapatkan tunjangan transport.
                    </p>

                </x-wirekit::stack>


                <div class="w-full sm:w-64">

                    <x-wirekit::input placeholder="Cari employee" name="search" class="text-black" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="name">
                                Employee
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="position">
                                Position
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="amount">
                                Benefit Amount
                            </x-wirekit::table.th>


                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        {{-- Employee 1 --}}
                        @forelse ($benefit->contracts as $ben)

                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">
                                        <span class="text-sm font-semibold text-sky-600">
                                            NP
                                        </span>
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                           {{$ben->employees->user->name}}
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                           {{$ben->employees->employee_code}}
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                <span class="text-sm text-slate-700">
                                   {{$ben->employees?->position->name ?? "Belum punya"}}
                                </span>
                            </x-wirekit::table.td>



                            <x-wirekit::table.td>
                                <span class="text-sm font-medium text-slate-700">
                                    Rp{{ number_format($ben->benefits()->first()->pivot->amount) }}
                                </span>
                            </x-wirekit::table.td>



                            <x-wirekit::table.td>
                                <x-wirekit::button type="button" class="px-3 py-1.5 text-xs" href="{{ route('employee.show',$ben->employees->id) }}" wire:navigate>
                                    Detail
                                </x-wirekit::button>
                            </x-wirekit::table.td>

                        </x-wirekit::table.row>

                        @empty
                        <x-wirekit::table.row>
                            <x-wirekit::table.td colspan="5" class="text-center text-sm text-slate-500">
                                Tidak ada employee yang menggunakan benefit ini.
                            </x-wirekit::table.td>
                        </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
