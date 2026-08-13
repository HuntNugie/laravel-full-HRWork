{{-- resources/views/livewire/page/main/dashboard.blade.php --}}

<x-layouts::app title="Dashboard">

    <x-wirekit::app-shell>

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <x-slot:header>

            <x-wirekit::header>

                {{-- Mobile sidebar toggle --}}
                <x-wirekit::sidebar.toggle class="lg:hidden" />

                {{-- Brand --}}
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="flex items-center gap-2"
                >
                    <div
                        class="flex h-9 w-9 items-center justify-center
                               rounded-xl bg-[#30AFFF]"
                    >
                        <svg
                            viewBox="0 0 48 48"
                            class="h-5 w-5 text-white"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <rect
                                x="8"
                                y="8"
                                width="8"
                                height="32"
                                rx="4"
                                fill="currentColor"
                            />

                            <rect
                                x="20"
                                y="16"
                                width="8"
                                height="24"
                                rx="4"
                                fill="currentColor"
                                opacity=".82"
                            />

                            <rect
                                x="32"
                                y="22"
                                width="8"
                                height="18"
                                rx="4"
                                fill="currentColor"
                                opacity=".62"
                            />
                        </svg>
                    </div>

                    <span class="font-bold tracking-tight text-slate-900">
                        HRWork
                    </span>
                </a>


                <div class="flex-1"></div>


                {{-- Static user --}}
                <div class="hidden items-center gap-3 sm:flex">

                    <x-wirekit::stack gap="0" class="text-right">

                        <p class="text-sm font-semibold text-slate-900">
                            Nugie
                        </p>

                        <p class="text-xs text-slate-500">
                            Administrator
                        </p>

                    </x-wirekit::stack>


                    <div
                        class="flex h-9 w-9 items-center justify-center
                               rounded-full bg-[#92EEFF]/60"
                    >
                        <span class="text-sm font-semibold text-sky-700">
                            N
                        </span>
                    </div>

                </div>

            </x-wirekit::header>

        </x-slot:header>



        {{-- =========================================================
            SIDEBAR
        ========================================================== --}}
        <x-slot:sidebar>

            <x-wirekit::sidebar>

                <x-slot:header>

                    <x-wirekit::stack gap="1">

                        <span
                            class="text-xs font-medium uppercase
                                   tracking-wider text-slate-400"
                        >
                            Administration
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            Management Panel
                        </span>

                    </x-wirekit::stack>

                </x-slot:header>


                {{-- Overview --}}
                <x-wirekit::sidebar.group label="Overview">

                    <x-wirekit::sidebar.item
                        href="{{ route('dashboard') }}"
                        :active="request()->routeIs('dashboard')"
                        wire:navigate
                    >
                        Dashboard
                    </x-wirekit::sidebar.item>

                </x-wirekit::sidebar.group>


                {{-- Access Management --}}
                <x-wirekit::sidebar.group label="Access Management">

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Users
                    </x-wirekit::sidebar.item>

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Roles
                    </x-wirekit::sidebar.item>

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Permissions
                    </x-wirekit::sidebar.item>

                </x-wirekit::sidebar.group>


                {{-- HR Management --}}
                <x-wirekit::sidebar.group label="Human Resources">

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Employees
                    </x-wirekit::sidebar.item>

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Attendance
                    </x-wirekit::sidebar.item>

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Performance
                    </x-wirekit::sidebar.item>

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Training & TNA
                    </x-wirekit::sidebar.item>

                </x-wirekit::sidebar.group>


                {{-- System --}}
                <x-wirekit::sidebar.group label="System">

                    <x-wirekit::sidebar.item
                        href="#"
                        wire:navigate
                    >
                        Settings
                    </x-wirekit::sidebar.item>

                </x-wirekit::sidebar.group>


                {{-- Sidebar Footer --}}
                <x-slot:footer>

                    <x-wirekit::stack gap="3">

                        <div
                            class="rounded-xl border border-slate-200
                                   bg-slate-50 p-3"
                        >

                            <p class="truncate text-sm font-semibold text-slate-900">
                                Nugie
                            </p>

                            <p class="truncate text-xs text-slate-500">
                                nugie@example.com
                            </p>

                        </div>


                        {{-- Sementara statis --}}
                        <x-wirekit::button
                            type="button"
                            class="w-full bg-[#FF5656] text-white
                                   hover:bg-red-500"
                        >
                            Logout
                        </x-wirekit::button>

                    </x-wirekit::stack>

                </x-slot:footer>

            </x-wirekit::sidebar>

        </x-slot:sidebar>



        {{-- =========================================================
            MAIN CONTENT
        ========================================================== --}}
        <x-wirekit::main :container="true">

            <x-wirekit::stack gap="6">

                {{-- Page Heading --}}
                <x-wirekit::stack gap="1">

                    <span class="text-sm font-medium text-[#30AFFF]">
                        Administration
                    </span>

                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                        Dashboard
                    </h1>

                    <p class="text-sm text-slate-500">
                        Ringkasan aktivitas dan kondisi sistem HRWork.
                    </p>

                </x-wirekit::stack>



                {{-- =====================================================
                    STATISTICS
                ====================================================== --}}
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
                                    128
                                </span>

                                <span class="text-xs text-slate-400">
                                    Akun terdaftar
                                </span>

                            </x-wirekit::stack>

                        </x-wirekit::card.body>

                    </x-wirekit::card>


                    {{-- Employees --}}
                    <x-wirekit::card>

                        <x-wirekit::card.body>

                            <x-wirekit::stack gap="3">

                                <div class="flex items-center justify-between">

                                    <span class="text-sm font-medium text-slate-500">
                                        Employees
                                    </span>

                                    <span
                                        class="h-2.5 w-2.5 rounded-full
                                               bg-[#92EEFF]"
                                    ></span>

                                </div>

                                <span class="text-3xl font-bold text-slate-900">
                                    84
                                </span>

                                <span class="text-xs text-slate-400">
                                    Karyawan aktif
                                </span>

                            </x-wirekit::stack>

                        </x-wirekit::card.body>

                    </x-wirekit::card>


                    {{-- Pending Approval --}}
                    <x-wirekit::card>

                        <x-wirekit::card.body>

                            <x-wirekit::stack gap="3">

                                <div class="flex items-center justify-between">

                                    <span class="text-sm font-medium text-slate-500">
                                        Pending Approval
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
                                    Membutuhkan tindakan
                                </span>

                            </x-wirekit::stack>

                        </x-wirekit::card.body>

                    </x-wirekit::card>


                    {{-- Attendance --}}
                    <x-wirekit::card>

                        <x-wirekit::card.body>

                            <x-wirekit::stack gap="3">

                                <div class="flex items-center justify-between">

                                    <span class="text-sm font-medium text-slate-500">
                                        Attendance Today
                                    </span>

                                    <span
                                        class="h-2.5 w-2.5 rounded-full
                                               bg-[#C4F7CA]"
                                    ></span>

                                </div>

                                <span class="text-3xl font-bold text-slate-900">
                                    96%
                                </span>

                                <span class="text-xs text-emerald-600">
                                    Kehadiran hari ini
                                </span>

                            </x-wirekit::stack>

                        </x-wirekit::card.body>

                    </x-wirekit::card>

                </div>



                {{-- =====================================================
                    OVERVIEW + QUICK ACTION
                ====================================================== --}}
                <div class="grid gap-6 xl:grid-cols-3">

                    {{-- Overview --}}
                    <x-wirekit::card class="xl:col-span-2">

                        <x-wirekit::card.header>

                            <x-wirekit::stack gap="1">

                                <h2 class="text-lg font-semibold text-slate-900">
                                    Employee Overview
                                </h2>

                                <p class="text-sm text-slate-500">
                                    Ringkasan jumlah karyawan dalam sistem.
                                </p>

                            </x-wirekit::stack>

                        </x-wirekit::card.header>


                        <x-wirekit::card.body>

                            <div
                                class="flex min-h-[300px] items-center justify-center
                                       rounded-xl border border-dashed
                                       border-slate-200 bg-slate-50"
                            >

                                <x-wirekit::stack
                                    gap="1"
                                    class="text-center"
                                >

                                    <span class="text-sm font-medium text-slate-500">
                                        Employee Overview
                                    </span>

                                    <span class="text-xs text-slate-400">
                                        WireCharts akan ditempatkan di sini.
                                    </span>

                                </x-wirekit::stack>

                            </div>

                        </x-wirekit::card.body>

                    </x-wirekit::card>



                    {{-- Quick Actions --}}
                    <x-wirekit::card>

                        <x-wirekit::card.header>

                            <x-wirekit::stack gap="1">

                                <h2 class="text-lg font-semibold text-slate-900">
                                    Quick Actions
                                </h2>

                                <p class="text-sm text-slate-500">
                                    Akses cepat untuk administrator.
                                </p>

                            </x-wirekit::stack>

                        </x-wirekit::card.header>


                        <x-wirekit::card.body>

                            <x-wirekit::stack gap="3">

                                <x-wirekit::button
                                    type="button"
                                    class="w-full bg-[#30AFFF]
                                           text-white hover:bg-sky-500"
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
                                    Review Reports
                                </x-wirekit::button>

                            </x-wirekit::stack>

                        </x-wirekit::card.body>

                    </x-wirekit::card>

                </div>



                {{-- =====================================================
                    RECENT ACTIVITY
                ====================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Recent Activity
                            </h2>

                            <p class="text-sm text-slate-500">
                                Aktivitas terbaru dalam sistem.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <x-wirekit::stack gap="4">

                            <div class="flex items-start gap-3">

                                <span
                                    class="mt-1.5 h-2.5 w-2.5 shrink-0
                                           rounded-full bg-[#30AFFF]"
                                ></span>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        User baru berhasil ditambahkan.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        10 menit lalu
                                    </p>

                                </div>

                            </div>


                            <div class="flex items-start gap-3">

                                <span
                                    class="mt-1.5 h-2.5 w-2.5 shrink-0
                                           rounded-full bg-[#C4F7CA]"
                                ></span>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        Role Administrator diperbarui.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        35 menit lalu
                                    </p>

                                </div>

                            </div>


                            <div class="flex items-start gap-3">

                                <span
                                    class="mt-1.5 h-2.5 w-2.5 shrink-0
                                           rounded-full bg-[#FFA239]"
                                ></span>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        12 laporan menunggu approval.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        1 jam lalu
                                    </p>

                                </div>

                            </div>


                            <div class="flex items-start gap-3">

                                <span
                                    class="mt-1.5 h-2.5 w-2.5 shrink-0
                                           rounded-full bg-[#92EEFF]"
                                ></span>

                                <div>

                                    <p class="text-sm font-medium text-slate-800">
                                        Attendance berhasil diperbarui.
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        2 jam lalu
                                    </p>

                                </div>

                            </div>

                        </x-wirekit::stack>

                    </x-wirekit::card.body>

                </x-wirekit::card>

            </x-wirekit::stack>

        </x-wirekit::main>

    </x-wirekit::app-shell>

</x-layouts::app>