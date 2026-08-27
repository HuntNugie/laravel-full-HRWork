<x-wirekit::stack gap="md">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                Organization
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Employees
            </h1>

            <p class="text-sm text-slate-500">
                Kelola data karyawan yang terdapat dalam organisasi.
            </p>

        </x-wirekit::stack>


        <x-wirekit::button class="bg-[#30AFFF] text-white hover:bg-sky-500" href="{{ route('employee.create') }}"
            wire:navigate>
            <x-wirekit::icon name="user-add" />
            Employee
        </x-wirekit::button>

    </div>


    {{-- =====================================================
    EMPLOYEE LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Employee List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar karyawan yang terdaftar dalam organisasi.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}
                <div class="w-full sm:w-64">

                    <x-wirekit::input placeholder="Cari nama employee" wire:model.live.debounce.500ms="search" name="search" class="text-black" />

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

                            <x-wirekit::table.th sortable column="team">
                                Team
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="email">
                                Email
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="status">
                                Status Pegawai
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($employees as $employee)
                            <x-wirekit::table.row>

                                {{-- Employee --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">
                                            <span class="text-sm font-semibold text-sky-600">
                                                AP
                                            </span>
                                        </div>

                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-800">
                                              {{$employee->user->name}}
                                            </p>

                                            <p class="truncate text-xs text-slate-400">
                                                {{ $employee->employee_code }}
                                            </p>

                                        </div>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- Position --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $employee->position?->name ?? "Belum di ketahui" }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Team --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $employee->team?->name ?? "Belum di ketahui"}}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Email --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-600">
                                        {{ $employee->user->email }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Status --}}
                                <x-wirekit::table.td>

                                    <span
                                        class="inline-flex items-center rounded-full
                              px-2.5 py-1
                                text-xs font-medium   {{ $employee->statusHistory()->latest()->first()?->new_status == 'active' ? 'bg-emerald-50  text-emerald-600' : 'bg-red-50  text-red-600' }}">
                                        {{ $employee->statusHistory()->latest()->first()->new_status ?? "Belum di ketahui"}}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Actions --}}
                                <x-wirekit::table.td>

                                    <x-wirekit::button type="button" class="px-3 py-1.5 text-xs" href="{{ route('employee.show',$employee->id) }}" wire:navigate>
                                        Detail
                                    </x-wirekit::button>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="6">
                                    <div class="flex flex-col items-center justify-center gap-2 py-10 text-center">
                                        <p class="text-sm font-medium text-slate-700">
                                            Belum ada data karyawan.
                                        </p>

                                        <p class="text-sm text-slate-500">
                                            Silakan tambahkan karyawan terlebih dahulu.
                                        </p>
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse


                    </x-wirekit::table.body>

                </x-wirekit::table>
                {{ $employees->links() }}

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


</x-wirekit::stack>
