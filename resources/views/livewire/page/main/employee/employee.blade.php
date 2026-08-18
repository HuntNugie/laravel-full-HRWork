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


    <x-wirekit::button class="bg-[#30AFFF] text-white hover:bg-sky-500">
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

                <x-wirekit::input
                    placeholder="Cari nama employee"
                    name="search"
                    class="text-black"
                />

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
                            Status
                        </x-wirekit::table.th>

                        <x-wirekit::table.th>
                            Actions
                        </x-wirekit::table.th>

                    </x-wirekit::table.row>

                </x-wirekit::table.head>


                <x-wirekit::table.body>

                    {{-- =================================================
                    EMPLOYEE 1
                    ================================================== --}}
                    <x-wirekit::table.row>

                        {{-- Employee --}}
                        <x-wirekit::table.td>

                            <div class="flex items-center gap-3">

                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">
                                    <span class="text-sm font-semibold text-sky-600">
                                        AP
                                    </span>
                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        Andi Pratama
                                    </p>

                                    <p class="truncate text-xs text-slate-400">
                                        EMP-001
                                    </p>

                                </div>

                            </div>

                        </x-wirekit::table.td>


                        {{-- Position --}}
                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-700">
                                Software Engineer
                            </span>

                        </x-wirekit::table.td>


                        {{-- Team --}}
                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-700">
                                Development
                            </span>

                        </x-wirekit::table.td>


                        {{-- Email --}}
                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-600">
                                andi@inovindo.co.id
                            </span>

                        </x-wirekit::table.td>


                        {{-- Status --}}
                        <x-wirekit::table.td>

                            <span class="inline-flex items-center rounded-full
                                bg-emerald-50 px-2.5 py-1
                                text-xs font-medium text-emerald-600">
                                Active
                            </span>

                        </x-wirekit::table.td>


                        {{-- Actions --}}
                        <x-wirekit::table.td>

                            <x-wirekit::button
                                type="button"
                                class="px-3 py-1.5 text-xs"
                            >
                                Detail
                            </x-wirekit::button>

                        </x-wirekit::table.td>

                    </x-wirekit::table.row>


                    {{-- =================================================
                    EMPLOYEE 2
                    ================================================== --}}
                    <x-wirekit::table.row>

                        <x-wirekit::table.td>

                            <div class="flex items-center gap-3">

                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-violet-100">
                                    <span class="text-sm font-semibold text-violet-600">
                                        SR
                                    </span>
                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        Siti Rahma
                                    </p>

                                    <p class="truncate text-xs text-slate-400">
                                        EMP-002
                                    </p>

                                </div>

                            </div>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-700">
                                UI/UX Designer
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-700">
                                Design
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-600">
                                siti@inovindo.co.id
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="inline-flex items-center rounded-full
                                bg-emerald-50 px-2.5 py-1
                                text-xs font-medium text-emerald-600">
                                Active
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <x-wirekit::button
                                type="button"
                                class="px-3 py-1.5 text-xs"
                            >
                                Detail
                            </x-wirekit::button>

                        </x-wirekit::table.td>

                    </x-wirekit::table.row>


                    {{-- =================================================
                    EMPLOYEE 3
                    ================================================== --}}
                    <x-wirekit::table.row>

                        <x-wirekit::table.td>

                            <div class="flex items-center gap-3">

                                <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-amber-100">
                                    <span class="text-sm font-semibold text-amber-600">
                                        RF
                                    </span>
                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        Rizky Fadillah
                                    </p>

                                    <p class="truncate text-xs text-slate-400">
                                        EMP-003
                                    </p>

                                </div>

                            </div>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-700">
                                HR Staff
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-700">
                                Human Resource
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="text-sm text-slate-600">
                                rizky@inovindo.co.id
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <span class="inline-flex items-center rounded-full
                                bg-slate-100 px-2.5 py-1
                                text-xs font-medium text-slate-500">
                                Inactive
                            </span>

                        </x-wirekit::table.td>


                        <x-wirekit::table.td>

                            <x-wirekit::button
                                type="button"
                                class="px-3 py-1.5 text-xs"
                            >
                                Detail
                            </x-wirekit::button>

                        </x-wirekit::table.td>

                    </x-wirekit::table.row>

                </x-wirekit::table.body>

            </x-wirekit::table>

        </div>

    </x-wirekit::card.body>

</x-wirekit::card>


</x-wirekit::stack>
