<x-wirekit::stack gap="md">

    {{-- =========================================================
        1. PAGE HEADING
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Identitas Super Admin Dashboard
        - Menjelaskan bahwa halaman berfokus pada administrasi
          dan kondisi sistem secara keseluruhan
    ========================================================== --}}
    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            System Administration
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Super Admin Dashboard
        </h1>

        <p class="text-sm text-slate-500">
            Ringkasan sistem, akses pengguna, organisasi, keamanan,
            dan aktivitas terbaru HRWork.
        </p>

    </x-wirekit::stack>


    {{-- =========================================================
        2. SYSTEM OVERVIEW
        ---------------------------------------------------------
        Nanti digunakan untuk statistik utama sistem:
        - Total Users
        - Active Employees
        - Total Roles
        - Pending Actions
    ========================================================== --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Users --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Total Users
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#30AFFF]"></span>

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

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Active Employees
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#C4F7CA]"></span>

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

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Roles
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#92EEFF]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        6
                    </span>

                    <span class="text-xs text-slate-400">
                        Role yang tersedia
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Pending Actions --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Pending Actions
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#FFA239]"></span>

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
        3. ACCESS & ORGANIZATION
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Melihat kondisi akun user berdasarkan status
        - Melihat struktur organisasi secara ringkas

        Tidak mengandalkan nama role tertentu karena role dapat
        bertambah di kemudian hari.
    ========================================================== --}}
    <div class="grid gap-6 xl:grid-cols-3">

        {{-- User & Access Overview --}}
        <x-wirekit::card class="xl:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="sm">

                    <h2 class="text-lg font-semibold text-slate-900">
                        User & Access Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan akun dan status akses pengguna.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="lg">

                    {{-- Active Users --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-700">
                                Active Users
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                84
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div class="h-full w-[88%] rounded-full bg-[#30AFFF]"></div>

                        </div>

                    </div>


                    {{-- Pending Users --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-700">
                                Pending Users
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                8
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div class="h-full w-[8%] rounded-full bg-[#92EEFF]"></div>

                        </div>

                    </div>


                    {{-- Inactive Users --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <span class="text-sm font-medium text-slate-700">
                                Inactive Users
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                4
                            </span>

                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">

                            <div class="h-full w-[4%] rounded-full bg-[#FFA239]"></div>

                        </div>

                    </div>


                    <x-wirekit::button type="button" class="w-full">
                        Manage Users & Roles
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Organization --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="sm">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Organization
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan struktur organisasi.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

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

                    <x-wirekit::button type="button" class="w-full">
                        Open Organization
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        4. SYSTEM HEALTH
        ---------------------------------------------------------
        Nanti digunakan untuk melihat kondisi modul utama sistem:
        - User Management
        - Role & Permission
        - HR Management
        - Attendance
        - Payroll

        Fokusnya adalah status sistem, bukan detail operasional.
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="sm">

                <h2 class="text-lg font-semibold text-slate-900">
                    System Health
                </h2>

                <p class="text-sm text-slate-500">
                    Kondisi modul utama dalam sistem HRWork.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">

                {{-- User Management --}}
                <div class="rounded-lg border border-slate-200 p-4">

                    <x-wirekit::stack gap="sm">

                        <div class="flex items-center gap-3">

                            <span class="h-2.5 w-2.5 rounded-full bg-[#C4F7CA]"></span>

                            <span class="text-sm font-medium text-slate-700">
                                User Management
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </x-wirekit::stack>

                </div>


                {{-- Role & Permission --}}
                <div class="rounded-lg border border-slate-200 p-4">

                    <x-wirekit::stack gap="sm">

                        <div class="flex items-center gap-3">

                            <span class="h-2.5 w-2.5 rounded-full bg-[#C4F7CA]"></span>

                            <span class="text-sm font-medium text-slate-700">
                                Role & Permission
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </x-wirekit::stack>

                </div>


                {{-- HR Management --}}
                <div class="rounded-lg border border-slate-200 p-4">

                    <x-wirekit::stack gap="sm">

                        <div class="flex items-center gap-3">

                            <span class="h-2.5 w-2.5 rounded-full bg-[#C4F7CA]"></span>

                            <span class="text-sm font-medium text-slate-700">
                                HR Management
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </x-wirekit::stack>

                </div>


                {{-- Attendance --}}
                <div class="rounded-lg border border-slate-200 p-4">

                    <x-wirekit::stack gap="sm">

                        <div class="flex items-center gap-3">

                            <span class="h-2.5 w-2.5 rounded-full bg-[#C4F7CA]"></span>

                            <span class="text-sm font-medium text-slate-700">
                                Attendance
                            </span>

                        </div>

                        <span class="text-xs font-medium text-emerald-600">
                            Operational
                        </span>

                    </x-wirekit::stack>

                </div>


                {{-- Payroll --}}
                <div class="rounded-lg border border-slate-200 p-4">

                    <x-wirekit::stack gap="sm">

                        <div class="flex items-center gap-3">

                            <span class="h-2.5 w-2.5 rounded-full bg-[#FFA239]"></span>

                            <span class="text-sm font-medium text-slate-700">
                                Payroll
                            </span>

                        </div>

                        <span class="text-xs font-medium text-amber-600">
                            Processing
                        </span>

                    </x-wirekit::stack>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =========================================================
        5. SECURITY OVERVIEW
        ---------------------------------------------------------
        Nanti digunakan untuk ringkasan keamanan sistem:
        - Active Sessions
        - Failed Logins
        - Inactive Users
        - Permission Changes

        Detail log keamanan dapat dibuka dari tombol di bawah.
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="sm">

                <h2 class="text-lg font-semibold text-slate-900">
                    Security Overview
                </h2>

                <p class="text-sm text-slate-500">
                    Ringkasan keamanan dan akses sistem.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Active Sessions
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-slate-900">
                        18
                    </p>

                </div>


                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Failed Logins
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-[#FF5656]">
                        3
                    </p>

                </div>


                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Inactive Users
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-[#C77A00]">
                        4
                    </p>

                </div>


                <div>

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Permission Changes
                    </p>

                    <p class="mt-1 text-2xl font-semibold text-slate-900">
                        6
                    </p>

                </div>

            </div>


            <div class="mt-5">

                <x-wirekit::button type="button" class="w-full">
                    Security Logs
                </x-wirekit::button>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =========================================================
        6. RECENT SYSTEM ACTIVITY
        ---------------------------------------------------------
        Nanti digunakan sebagai ringkasan aktivitas penting sistem:
        - Perubahan role
        - Perubahan permission
        - User baru
        - Perubahan organisasi
        - Aktivitas administratif penting

        Idealnya nanti berasal dari audit / activity log.
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="sm">

                <h2 class="text-lg font-semibold text-slate-900">
                    Recent System Activity
                </h2>

                <p class="text-sm text-slate-500">
                    Aktivitas terbaru yang terjadi dalam sistem HRWork.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <x-wirekit::stack gap="md">

                {{-- Activity 1 --}}
                <div class="flex items-start gap-3">

                    <span
                        class="mt-1.5 h-2.5 w-2.5 shrink-0
                               rounded-full bg-[#30AFFF]"></span>

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
                               rounded-full bg-[#92EEFF]"></span>

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
                               rounded-full bg-[#C4F7CA]"></span>

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
                               rounded-full bg-[#FFA239]"></span>

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
                               rounded-full bg-[#30AFFF]"></span>

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Employee baru berhasil ditambahkan ke sistem.
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            3 jam lalu · HR
                        </p>

                    </div>

                </div>


                <x-wirekit::button type="button" class="w-full">
                    View System Activity
                </x-wirekit::button>

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =========================================================
        7. QUICK ACTIONS
        ---------------------------------------------------------
        Nanti digunakan sebagai shortcut ke area administrasi
        yang paling sering digunakan:
        - Manage Users
        - Manage Roles
        - Organization
        - System Settings
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="sm">

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

                <x-wirekit::button type="button" class="w-full">
                    Manage Users
                </x-wirekit::button>


                <x-wirekit::button type="button" class="w-full">
                    Manage Roles
                </x-wirekit::button>


                <x-wirekit::button type="button" class="w-full">
                    Organization
                </x-wirekit::button>


                <x-wirekit::button type="button" class="w-full">
                    System Settings
                </x-wirekit::button>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
