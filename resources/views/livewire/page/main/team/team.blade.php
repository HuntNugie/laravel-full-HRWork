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
                Teams
            </h1>

            <p class="text-sm text-slate-500">
                Kelola team yang terdapat dalam struktur organisasi.
            </p>

        </x-wirekit::stack>


        @can('create-team')

            <x-wirekit::button
                type="button"
                class="bg-[#30AFFF] text-white hover:bg-sky-500"
            >
                Add Team
            </x-wirekit::button>

        @endcan

    </div>


    {{-- =====================================================
    TEAM LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Team List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar team yang terdaftar dalam organisasi.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}
                <div class="w-full sm:w-64">

                    <x-wirekit::input
                        placeholder="Cari nama team"
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

                            <x-wirekit::table.th
                                sortable
                                column="name"
                            >
                                Team
                            </x-wirekit::table.th>

                            <x-wirekit::table.th
                                sortable
                                column="division"
                            >
                                Division
                            </x-wirekit::table.th>

                            <x-wirekit::table.th
                                sortable
                                column="manager"
                            >
                                Manager
                            </x-wirekit::table.th>

                            <x-wirekit::table.th
                                sortable
                                column="members"
                            >
                                Members
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


                    <x-wirekit::table.body>

                        {{-- =================================================
                        TEAM 1
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center
                                               justify-center rounded-lg
                                               bg-[#92EEFF]/40"
                                    >
                                        <x-wirekit::icon
                                            name="user-group"
                                            class="size-5 text-sky-600"
                                        />
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            Web Development
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                            Tim pengembangan aplikasi web
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                Development
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <div class="flex items-center gap-2">

                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center
                                               justify-center rounded-full
                                               bg-[#D8FFC5] text-xs font-semibold
                                               text-emerald-700"
                                    >
                                        A
                                    </div>

                                    <span class="text-sm text-slate-700">
                                        Andi Pratama
                                    </span>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                8 employees
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-emerald-50 px-2.5 py-1
                                           text-xs font-medium text-emerald-600"
                                >
                                    Active
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                @can('show-team')

                                    <x-wirekit::button
                                        type="button"
                                        class="px-3 py-1.5 text-xs"
                                    >
                                        Detail
                                    </x-wirekit::button>

                                @endcan

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                        TEAM 2
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center
                                               justify-center rounded-lg
                                               bg-[#92EEFF]/40"
                                    >
                                        <x-wirekit::icon
                                            name="device-phone-mobile"
                                            class="size-5 text-sky-600"
                                        />
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            Mobile Development
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                            Tim pengembangan aplikasi mobile
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                Development
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <div class="flex items-center gap-2">

                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center
                                               justify-center rounded-full
                                               bg-[#D8FFC5] text-xs font-semibold
                                               text-emerald-700"
                                    >
                                        B
                                    </div>

                                    <span class="text-sm text-slate-700">
                                        Budi Santoso
                                    </span>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                5 employees
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-emerald-50 px-2.5 py-1
                                           text-xs font-medium text-emerald-600"
                                >
                                    Active
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                @can('show-team')

                                    <x-wirekit::button
                                        type="button"
                                        class="px-3 py-1.5 text-xs"
                                    >
                                        Detail
                                    </x-wirekit::button>

                                @endcan

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                        TEAM 3
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center
                                               justify-center rounded-lg
                                               bg-[#92EEFF]/40"
                                    >
                                        <x-wirekit::icon
                                            name="server"
                                            class="size-5 text-sky-600"
                                        />
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            DevOps
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                            Infrastruktur dan deployment
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                Development
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <div class="flex items-center gap-2">

                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center
                                               justify-center rounded-full
                                               bg-[#D8FFC5] text-xs font-semibold
                                               text-emerald-700"
                                    >
                                        C
                                    </div>

                                    <span class="text-sm text-slate-700">
                                        Candra Wijaya
                                    </span>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                4 employees
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-red-50 px-2.5 py-1
                                           text-xs font-medium text-red-600"
                                >
                                    Inactive
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                @can('show-team')

                                    <x-wirekit::button
                                        type="button"
                                        class="px-3 py-1.5 text-xs"
                                    >
                                        Detail
                                    </x-wirekit::button>

                                @endcan

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- =================================================
                        TEAM 4
                        ================================================== --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center
                                               justify-center rounded-lg
                                               bg-[#92EEFF]/40"
                                    >
                                        <x-wirekit::icon
                                            name="megaphone"
                                            class="size-5 text-sky-600"
                                        />
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            Digital Marketing
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                            Tim pemasaran digital
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                Marketing
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <div class="flex items-center gap-2">

                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center
                                               justify-center rounded-full
                                               bg-[#D8FFC5] text-xs font-semibold
                                               text-emerald-700"
                                    >
                                        D
                                    </div>

                                    <span class="text-sm text-slate-700">
                                        Dinda Putri
                                    </span>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                6 employees
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-emerald-50 px-2.5 py-1
                                           text-xs font-medium text-emerald-600"
                                >
                                    Active
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                @can('show-team')

                                    <x-wirekit::button
                                        type="button"
                                        class="px-3 py-1.5 text-xs"
                                    >
                                        Detail
                                    </x-wirekit::button>

                                @endcan

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>