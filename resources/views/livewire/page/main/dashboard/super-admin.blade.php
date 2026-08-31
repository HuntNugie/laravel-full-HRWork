<x-wirekit::stack gap="md">

    {{-- =========================================================
        PAGE HEADING
    ========================================================== --}}

    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            System Administration
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Super Admin Dashboard
        </h1>

        <p class="text-sm text-slate-500">
            Ringkasan keseluruhan sistem HRWork, pengguna, organisasi,
            keamanan akses, dan aktivitas terbaru.
        </p>

    </x-wirekit::stack>


    {{-- =========================================================
        SYSTEM STATISTICS
    ========================================================== --}}

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Users --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Total Users
                        </span>

                        <span
                            class="h-2.5 w-2.5 rounded-full
                                   bg-[#30AFFF]"
                        ></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        96
                    </span>

                    <span class="text-xs text-slate-400">
                        Akun terdaftar di sistem
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Active Employees --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Active Employees
                        </span>

                        <span
                            class="h-2.5 w-2.5 rounded-full
                                   bg-[#C4F7CA]"
                        ></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        84
                    </span>

                    <span class="text-xs text-emerald-600">
                        Employee aktif
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Roles --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Roles
                        </span>

                        <span
                            class="h-2.5 w-2.5 rounded-full
                                   bg-[#92EEFF]"
                        ></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        6
                    </span>

                    <span class="text-xs text-slate-400">
                        Role aktif dalam sistem
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Pending Actions --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Pending Actions
                        </span>

                        <span
                            class="h-2.5 w-2.5 rounded-full
                                   bg-[#FFA239]"
                        ></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        12
                    </span>

                    <span class="text-xs text-amber-600">
                        Membutuhkan perhatian
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        USER ACCESS + ORGANIZATION
    ========================================================== --}}

    <div class="grid gap-6 xl:grid-cols-3">

        {{-- User & Access Overview --}}
        <x-wirekit::card class="xl:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        User & Access Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Distribusi akun berdasarkan role dan status akses
                        dalam sistem.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    {{-- Employee --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-700">
                                Employee
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                78
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full w-[81%] rounded-full bg-[#30AFFF]"
                            ></div>

                        </div>

                    </div>


                    {{-- HR --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-700">
                                HR
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                5
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full w-[35%] rounded-full bg-[#92EEFF]"
                            ></div>

                        </div>

                    </div>


                    {{-- Administrator --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-700">
                                Administrator
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                2
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full w-[20%] rounded-full bg-[#C4F7CA]"
                            ></div>

                        </div>

                    </div>


                    {{-- Super Admin --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-700">
                                Super Admin
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                1
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div
                                class="h-full w-[10%] rounded-full bg-[#FFA239]"
                            ></div>

                        </div>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        Manage Users & Roles
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Organization Summary --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Organization
                    </h2>

                    <p class="text-sm text-slate-500">
                        Struktur organisasi yang terdaftar.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Divisions
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            8
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Teams
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            14
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Positions
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            27
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Unassigned Employees
                        </span>

                        <span class="text-sm font-semibold text-[#C77A00]">
                            3
                        </span>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        Open Organization
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        SYSTEM + HR MODULES
    ========================================================== --}}

    <div class="grid gap-6 xl:grid-cols-2">

        {{-- System Overview --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        System Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Kondisi modul utama dan konfigurasi sistem.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <span
                                class="h-2.5 w-2.5 rounded-full
                                       bg-[#C4F7CA]"
                            ></span>

                            <span class="text-sm font-medium text-slate-700">
                                User Management
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <span
                                class="h-2.5 w-2.5 rounded-full
                                       bg-[#C4F7CA]"
                            ></span>

                            <span class="text-sm font-medium text-slate-700">
                                Role & Permission
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <span
                                class="h-2.5 w-2.5 rounded-full
                                       bg-[#C4F7CA]"
                            ></span>

                            <span class="text-sm font-medium text-slate-700">
                                HR Management
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <span
                                class="h-2.5 w-2.5 rounded-full
                                       bg-[#C4F7CA]"
                            ></span>

                            <span class="text-sm font-medium text-slate-700">
                                Attendance
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-3">

                            <span
                                class="h-2.5 w-2.5 rounded-full
                                       bg-[#FFA239]"
                            ></span>

                            <span class="text-sm font-medium text-slate-700">
                                Payroll
                            </span>

                        </div>

                        <span class="text-xs font-medium text-amber-600">
                            Processing
                        </span>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        System Settings
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- HR Module Overview --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        HR Module Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan aktivitas penting pada modul HR.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Active Employees
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            84
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Active Contracts
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            79
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Pending Leave
                        </span>

                        <span class="text-sm font-semibold text-[#C77A00]">
                            7
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Attendance Today
                        </span>

                        <span class="text-sm font-semibold text-emerald-600">
                            96%
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Payroll Status
                        </span>

                        <span
                            class="rounded-full bg-[#FFF3DE]
                                   px-2.5 py-1 text-xs font-medium
                                   text-[#C77A00]"
                        >
                            Processing
                        </span>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        Open HR Management
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        SECURITY + ACTIVITY
    ========================================================== --}}

    <div class="grid gap-6 xl:grid-cols-3">

        {{-- Security Overview --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Security Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan keamanan dan akses sistem.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Active Sessions
                        </span>

                        <span class="font-semibold text-slate-900">
                            18
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Failed Logins
                        </span>

                        <span class="font-semibold text-[#FF5656]">
                            3
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Inactive Users
                        </span>

                        <span class="font-semibold text-[#C77A00]">
                            4
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Permission Changes
                        </span>

                        <span class="font-semibold text-slate-900">
                            6
                        </span>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        Security Logs
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Recent System Activity --}}
        <x-wirekit::card class="xl:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Recent System Activity
                    </h2>

                    <p class="text-sm text-slate-500">
                        Aktivitas terbaru pada sistem HRWork.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    {{-- Activity 1 --}}
                    <div class="flex items-start gap-3">

                        <span
                            class="mt-1.5 h-2.5 w-2.5 shrink-0
                                   rounded-full bg-[#30AFFF]"
                        ></span>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Role Supervisor ditambahkan ke user Budi Santoso.
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                12 menit lalu · Administrator
                            </p>

                        </div>

                    </div>


                    {{-- Activity 2 --}}
                    <div class="flex items-start gap-3">

                        <span
                            class="mt-1.5 h-2.5 w-2.5 shrink-0
                                   rounded-full bg-[#92EEFF]"
                        ></span>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Team baru berhasil dibuat pada Division Technology.
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                28 menit lalu · HR
                            </p>

                        </div>

                    </div>


                    {{-- Activity 3 --}}
                    <div class="flex items-start gap-3">

                        <span
                            class="mt-1.5 h-2.5 w-2.5 shrink-0
                                   rounded-full bg-[#C4F7CA]"
                        ></span>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Payroll periode Agustus berhasil diproses.
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                1 jam lalu · HR
                            </p>

                        </div>

                    </div>


                    {{-- Activity 4 --}}
                    <div class="flex items-start gap-3">

                        <span
                            class="mt-1.5 h-2.5 w-2.5 shrink-0
                                   rounded-full bg-[#FFA239]"
                        ></span>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Permission employee.update diperbarui.
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                2 jam lalu · Super Admin
                            </p>

                        </div>

                    </div>


                    {{-- Activity 5 --}}
                    <div class="flex items-start gap-3">

                        <span
                            class="mt-1.5 h-2.5 w-2.5 shrink-0
                                   rounded-full bg-[#30AFFF]"
                        ></span>

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Employee baru berhasil ditambahkan ke sistem.
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                3 jam lalu · HR
                            </p>

                        </div>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        View System Activity
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        QUICK ACTIONS
    ========================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Quick Actions
                </h2>

                <p class="text-sm text-slate-500">
                    Akses cepat ke konfigurasi utama sistem.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

                <x-wirekit::button
                    type="button"
                    class="w-full"
                >
                    Manage Users
                </x-wirekit::button>


                <x-wirekit::button
                    type="button"
                    class="w-full"
                >
                    Manage Roles
                </x-wirekit::button>


                <x-wirekit::button
                    type="button"
                    class="w-full"
                >
                    Organization
                </x-wirekit::button>


                <x-wirekit::button
                    type="button"
                    class="w-full"
                >
                    System Settings
                </x-wirekit::button>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
