<x-wirekit::stack gap="md">

    {{-- =========================================================
        PAGE HEADING
    ========================================================== --}}
    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            Human Resources
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            HR Dashboard
        </h1>

        <p class="text-sm text-slate-500">
            Ringkasan kondisi karyawan, kehadiran, cuti, payroll,
            dan aktivitas pekerjaan dalam sistem HRWork.
        </p>

    </x-wirekit::stack>


    {{-- =========================================================
        STATISTICS
    ========================================================== --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Employees --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Total Employees
                        </span>

                        <span
                            class="h-2.5 w-2.5 rounded-full
                                   bg-[#30AFFF]"
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
                        81 dari 84 employee hadir
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Leave Requests --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Leave Requests
                        </span>

                        <span
                            class="h-2.5 w-2.5 rounded-full
                                   bg-[#FFA239]"
                        ></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        7
                    </span>

                    <span class="text-xs text-amber-600">
                        Menunggu proses HR
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Performance --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Work Performance
                        </span>

                        <span
                            class="h-2.5 w-2.5 rounded-full
                                   bg-[#92EEFF]"
                        ></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        86%
                    </span>

                    <span class="text-xs text-emerald-600">
                        Rata-rata performa pekerjaan
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        EMPLOYEE + LEAVE
    ========================================================== --}}
    <div class="grid gap-6 xl:grid-cols-3">

        {{-- Employee Overview --}}
        <x-wirekit::card class="xl:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Employee Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Komposisi dan kondisi karyawan berdasarkan struktur organisasi.
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
                            WireCharts akan menampilkan distribusi employee
                            berdasarkan division, team, atau position.
                        </span>

                    </x-wirekit::stack>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Leave Requests --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Pending Leave
                    </h2>

                    <p class="text-sm text-slate-500">
                        Pengajuan cuti yang membutuhkan tindakan.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Andi Pratama
                            </p>

                            <p class="text-xs text-slate-400">
                                Cuti tahunan · 14–16 Agustus
                            </p>

                        </div>

                        <span
                            class="rounded-full bg-[#FFF3DE]
                                   px-2.5 py-1 text-xs font-medium
                                   text-[#C77A00]"
                        >
                            Pending
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Candra Wijaya
                            </p>

                            <p class="text-xs text-slate-400">
                                Cuti sakit · 15 Agustus
                            </p>

                        </div>

                        <span
                            class="rounded-full bg-[#FFF3DE]
                                   px-2.5 py-1 text-xs font-medium
                                   text-[#C77A00]"
                        >
                            Pending
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Dinda Putri
                            </p>

                            <p class="text-xs text-slate-400">
                                Cuti tahunan · 18–20 Agustus
                            </p>

                        </div>

                        <span
                            class="rounded-full bg-[#FFF3DE]
                                   px-2.5 py-1 text-xs font-medium
                                   text-[#C77A00]"
                        >
                            Pending
                        </span>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        Review Leave Requests
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        ATTENDANCE + PAYROLL
    ========================================================== --}}
    <div class="grid gap-6 xl:grid-cols-2">

        {{-- Attendance Overview --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Attendance Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Kondisi kehadiran employee hari ini.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div
                    class="flex min-h-[260px] items-center justify-center
                           rounded-xl border border-dashed
                           border-slate-200 bg-slate-50"
                >

                    <x-wirekit::stack
                        gap="1"
                        class="text-center"
                    >

                        <span class="text-sm font-medium text-slate-500">
                            Attendance Chart
                        </span>

                        <span class="text-xs text-slate-400">
                            WireCharts akan menampilkan hadir,
                            terlambat, izin, sakit, dan tidak hadir.
                        </span>

                    </x-wirekit::stack>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Payroll Overview --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Payroll Overview
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan payroll periode berjalan.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Payroll Period
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            Agustus 2026
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Employees
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            84
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Total Payroll
                        </span>

                        <span class="text-sm font-semibold text-slate-900">
                            Rp 386.500.000
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Status
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
                        class="w-full bg-[#30AFFF]
                               text-white hover:bg-sky-500"
                    >
                        Open Payroll

                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        WORK MANAGEMENT
    ========================================================== --}}
    <div class="grid gap-6 xl:grid-cols-3">

        {{-- Team Performance --}}
        <x-wirekit::card class="xl:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Team Performance
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan performa pekerjaan berdasarkan Team.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div
                    class="flex min-h-[280px] items-center justify-center
                           rounded-xl border border-dashed
                           border-slate-200 bg-slate-50"
                >

                    <x-wirekit::stack
                        gap="1"
                        class="text-center"
                    >

                        <span class="text-sm font-medium text-slate-500">
                            Team Performance Chart
                        </span>

                        <span class="text-xs text-slate-400">
                            WireCharts akan menampilkan completion rate
                            dan performa setiap Team.
                        </span>

                    </x-wirekit::stack>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Work Management Summary --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Work Management
                    </h2>

                    <p class="text-sm text-slate-500">
                        Kondisi pekerjaan saat ini.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="4">

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Total Projects
                        </span>

                        <span class="font-semibold text-slate-900">
                            18
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Active Tasks
                        </span>

                        <span class="font-semibold text-slate-900">
                            42
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Completed Tasks
                        </span>

                        <span class="font-semibold text-emerald-600">
                            31
                        </span>

                    </div>


                    <div class="flex items-center justify-between">

                        <span class="text-sm text-slate-500">
                            Overdue Tasks
                        </span>

                        <span class="font-semibold text-[#FF5656]">
                            4
                        </span>

                    </div>


                    <x-wirekit::button
                        type="button"
                        class="w-full"
                    >
                        View Work Management
                    </x-wirekit::button>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        RECENT HR ACTIVITY
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Recent HR Activity
                </h2>

                <p class="text-sm text-slate-500">
                    Aktivitas terbaru yang berkaitan dengan pengelolaan SDM.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <x-wirekit::stack gap="4">

                {{-- Employee Added --}}
                <div class="flex items-start gap-3">

                    <span
                        class="mt-1.5 h-2.5 w-2.5 shrink-0
                               rounded-full bg-[#30AFFF]"
                    ></span>

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Employee baru berhasil ditambahkan.
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            10 menit lalu
                        </p>

                    </div>

                </div>


                {{-- Leave --}}
                <div class="flex items-start gap-3">

                    <span
                        class="mt-1.5 h-2.5 w-2.5 shrink-0
                               rounded-full bg-[#FFA239]"
                    ></span>

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Pengajuan cuti baru menunggu proses HR.
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            32 menit lalu
                        </p>

                    </div>

                </div>


                {{-- Attendance --}}
                <div class="flex items-start gap-3">

                    <span
                        class="mt-1.5 h-2.5 w-2.5 shrink-0
                               rounded-full bg-[#92EEFF]"
                    ></span>

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Rekap attendance berhasil diperbarui.
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            1 jam lalu
                        </p>

                    </div>

                </div>


                {{-- Payroll --}}
                <div class="flex items-start gap-3">

                    <span
                        class="mt-1.5 h-2.5 w-2.5 shrink-0
                               rounded-full bg-[#C4F7CA]"
                    ></span>

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Payroll periode Agustus sedang diproses.
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            2 jam lalu
                        </p>

                    </div>

                </div>


                {{-- Performance --}}
                <div class="flex items-start gap-3">

                    <span
                        class="mt-1.5 h-2.5 w-2.5 shrink-0
                               rounded-full bg-[#30AFFF]"
                    ></span>

                    <div>

                        <p class="text-sm font-medium text-slate-800">
                            Data performa Team berhasil diperbarui.
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            3 jam lalu
                        </p>

                    </div>

                </div>

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>