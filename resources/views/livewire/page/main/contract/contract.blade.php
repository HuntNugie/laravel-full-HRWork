<x-wirekit::stack gap="md">

    {{-- =====================================================
    HEADER
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Contracts
            </h1>

            <p class="text-sm text-slate-500">
                Kelola dan pantau seluruh kontrak kerja karyawan.
            </p>

        </x-wirekit::stack>

    </div>


    {{-- =====================================================
    CONTRACT LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Contract List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar kontrak kerja seluruh karyawan.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}
                <div class="w-full sm:w-72">

                    <x-wirekit::input placeholder="Cari nomor contract atau nama employee"
                        wire:model.live.debounce.500ms="search" name="search" class="text-black" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="contract_number">
                                Contract
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="employee">
                                Employee
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="position">
                                Position
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="type">
                                Type
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="start_date">
                                Start Date
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="end_date">
                                End Date
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="status">
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        {{-- =================================================
                        STATIC DATA
                        ================================================== --}}

                        {{-- CONTRACT 1 --}}
                        @foreach ($employees as $employee)
                            <x-wirekit::table.row>

                                {{-- Contract --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::stack gap="1">

                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $employee->latestEmployeeContract?->contract_number }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            Kontrak Kerja
                                        </p>

                                    </x-wirekit::stack>

                                </x-wirekit::table.td>


                                {{-- Employee --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">
                                            @if ($employee->user->getFirstMediaUrl('avatar'))
                                                <img src="{{ $employee->user->getFirstMediaUrl('avatar') }}"
                                                    alt="gambar dari {{ $employee->user->name }}"
                                                    class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                                            @else
                                                <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                                    class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                                            @endif
                                        </div>

                                        <x-wirekit::stack gap="1">

                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $employee->user->name }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                {{ $employee?->employee_code ?? 'Tidak ada' }}
                                            </p>

                                        </x-wirekit::stack>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- Position --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $employee?->position?->name ?? 'Tidak ada jabatan' }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Type --}}
                                <x-wirekit::table.td>

                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                        {{ $employee->latestEmployeeContract->employement_type }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Start --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-600">
                                        {{ $employee->latestEmployeeContract->start_date->format('d-m-Y') }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- End --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-600">
                                        {{ $employee->latestEmployeeContract?->end_date?->format('d-m-Y') ?? 'Pegawai tetap' }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Status --}}
                                <x-wirekit::table.td>

                                    <span @class([
                                        'inline-flex items-center rounded-full  px-2.5 py-1 text-xs font-medium',
                                        'text-emerald-600 bg-emerald-50' =>
                                            $employee->latestEmployeeContract?->status === 'active',
                                        'text-red-600 bg-red-50' =>
                                            $employee->latestEmployeeContract?->status === 'terminated',
                                        'text-yellow-600 bg-yellow-50' =>
                                            $employee->latestEmployeeContract?->status === 'expired',
                                        'text-slate-600 bg-slate-50' =>
                                            $employee->latestEmployeeContract?->status === 'draft',
                                    ])>
                                        {{ $employee->latestEmployeeContract?->status }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Actions --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-2">

                                        @can('show-contract')
                                            <x-wirekit::button type="button" class="px-3 py-1.5 text-xs">
                                                Detail
                                            </x-wirekit::button>
                                        @endcan

                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforeach






                    </x-wirekit::table.body>

                </x-wirekit::table>


                {{-- PAGINATION --}}


                {{ $employees->links() }}


            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
