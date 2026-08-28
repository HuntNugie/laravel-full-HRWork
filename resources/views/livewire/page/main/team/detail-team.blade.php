<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ======================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <a href="{{ route('team.view') }}" wire:navigate
                class="mb-3 inline-flex items-center text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">

                ← Kembali

            </a>

            <span class="text-sm font-medium text-[#30AFFF]">
                Manajemen Organisasi
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                {{ $team->name }} Team
            </h1>

            <p class="text-sm text-slate-500">
                Detail informasi team, supervisor, dan employee yang tergabung dalam team ini.
            </p>

        </x-wirekit::stack>


        <div class="flex items-center gap-2">

            <livewire:components.main.team.form-edit>
                <x-wirekit::button type="button" class="px-3 py-1.5 text-sm">

                    <x-wirekit::icon name="pencil" />

                    Edit Team

                </x-wirekit::button>

            </livewire:components.main.team.form-edit>
        </div>

    </div>


    {{-- =====================================================
        TEAM INFORMATION
    ======================================================= --}}

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- =================================================
            MAIN INFORMATION
        ================================================== --}}

        <x-wirekit::card class="lg:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Team Information
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi utama mengenai team.
                    </p>



                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                    {{-- Team Name --}}

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Team
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $team->name }}
                        </p>

                    </div>


                    {{-- Division --}}

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Division
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $team->divisi->name }}
                        </p>

                    </div>


                    {{-- Supervisor --}}

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Supervisor
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $team->supervisor->user->name ?? 'Belum ada supervisor' }}
                        </p>


                    </div>


                    {{-- Status --}}

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Status
                        </p>

                        <span
                            class="mt-1 inline-flex items-center rounded-full  px-2.5 py-1 text-xs font-medium {{ $team->is_active === 'active' ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }}">
                            {{ $team->is_active }}
                        </span>

                    </div>
                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Division
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $team->divisi->name }}
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- =================================================
            SUMMARY
        ================================================== --}}

        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Summary
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan team.
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
                            {{ count($team->nonSupervisors) }}
                        </p>

                    </div>


                    {{-- Supervisor --}}

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Supervisor
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $team->supervisor->user->name ?? 'Belum ada supervisor' }}
                        </p>


                    </div>


                    {{-- Created At --}}

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Created At
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $team->created_at->format('d F Y') }}
                        </p>

                    </div>



                    {{-- Last Updated --}}

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Last Updated
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $team->updated_at->diffForHumans() }}
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =====================================================
        SUPERVISOR
    ======================================================= --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Supervisor
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi employee yang bertanggung jawab sebagai supervisor team.
                </p>

                <livewire:components.main.team.form-assign-supervisor :team="$team">
                    <x-wirekit::button type="button" class="mt-3 px-3 py-1.5 text-sm">
                        <x-wirekit::icon name="user" />
                        {{ $team->supervisor ? 'Ganti supervisor' : 'Tambahkan supervisor' }}
                    </x-wirekit::button>
                </livewire:components.main.team.form-assign-supervisor>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        @if ($team->supervisor)
            <x-wirekit::card.body>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-3">

                        <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-sky-100">

                            <span class="text-sm font-semibold text-sky-600">
                                AP
                            </span>

                        </div>

                        <div class="min-w-0">

                            <p class="truncate text-sm font-semibold text-slate-800">
                                {{ $team->supervisor->user->name }}
                            </p>



                        </div>

                    </div>


                    <div class="grid grid-cols-2 gap-6 sm:grid-cols-3">

                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Kode Pegawai
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                {{ $team->supervisor->employee_code }}
                            </p>

                        </div>





                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Status
                            </p>

                            <span
                                class="mt-1 inline-flex items-center rounded-full  px-2.5 py-1 text-xs font-medium {{ $team->supervisor->status_employee === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                {{ $team->supervisor->status_employee }}
                            </span>

                        </div>

                    </div>

                </div>

            </x-wirekit::card.body>
        @else
            <x-wirekit::card.body>
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="flex size-11 items-center justify-center rounded-full bg-slate-100">
                        <span class="text-lg text-slate-400">—</span>
                    </div>

                    <p class="mt-3 text-sm font-medium text-slate-700">
                        Belum ada supervisor
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Team ini belum memiliki supervisor.
                    </p>
                </div>
            </x-wirekit::card.body>
        @endif

    </x-wirekit::card>


    {{-- =====================================================
        EMPLOYEE LIST
    ======================================================= --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Team Members
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar employee yang tergabung dalam {{ $team->name }} Team.
                    </p>

                </x-wirekit::stack>


                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">

                    <div class="w-full sm:w-64">

                        <x-wirekit::input placeholder="Cari employee" name="search" class="text-black" />

                    </div>

                    <livewire:components.main.team.form-assign-employee :teamId="$team->id">
                        <x-wirekit::button type="button">
                            <x-wirekit::icon name="plus" /> Masukkan Karyawan
                        </x-wirekit::button>

                    </livewire:components.main.team.form-assign-employee>
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

                            <x-wirekit::table.th sortable column="employee_code">
                                Employee Code
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

                        @forelse ($team->nonSupervisors as $employee)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">

                                            <span class="text-sm font-semibold text-sky-600">
                                                RA
                                            </span>

                                        </div>

                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-800">
                                                {{ $employee->user->name }}
                                            </p>


                                        </div>

                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $employee->position->name }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $employee->employee_code }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-emerald-50 text-emerald-600">
                                        Active
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    @can('show-employee')
                                        <x-wirekit::button type="button" class="px-3 py-1.5 text-xs"
                                            href="{{ route('employee.show', $employee->id) }}" wire:navigate>
                                            Detail
                                        </x-wirekit::button>
                                    @endcan

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="5" class="py-8 text-center">
                                    <span class="text-sm text-slate-500">
                                        Tidak ada employee dalam team ini.
                                    </span>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse




                        {{-- Empty State --}}

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


</x-wirekit::stack>
