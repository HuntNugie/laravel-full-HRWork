<x-wirekit::stack gap="md">

    {{-- =========================================================
        1. PAGE HEADING
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Menampilkan sapaan user
        - Menampilkan tanggal / informasi hari ini
        - Memberikan konteks bahwa ini adalah dashboard employee
    ========================================================== --}}
    <x-wirekit::stack gap="sm">

        <span class="text-sm font-medium text-[#30AFFF]">
            Dashboard
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Good Morning, Nugie
        </h1>

        <p class="text-sm text-slate-500">
            Berikut ringkasan aktivitas dan informasi kamu hari ini.
        </p>

    </x-wirekit::stack>


    {{-- =========================================================
        2. TODAY'S OVERVIEW
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Ringkasan cepat kondisi employee
        - Attendance hari ini
        - Sisa cuti
        - Payroll terakhir / periode berjalan
        - Status pekerjaan employee

        Bagian ini harus bisa dipahami dalam beberapa detik.
    ========================================================== --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Attendance --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Attendance
                        </span>

                        <div
                            class="flex size-9 items-center justify-center
                                   rounded-lg bg-emerald-50">
                            <x-wirekit::icon name="check-circle" class="size-5 text-emerald-600" />
                        </div>

                    </div>

                    <div>

                        <p class="text-xl font-semibold text-slate-900">
                            Present
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Clock in 08:03 AM
                        </p>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Leave Balance --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Leave Balance
                        </span>

                        <div
                            class="flex size-9 items-center justify-center
                                   rounded-lg bg-sky-50">
                            <x-wirekit::icon name="calendar" class="size-5 text-sky-600" />
                        </div>

                    </div>

                    <div>

                        <p class="text-xl font-semibold text-slate-900">
                            8 Days
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Remaining leave
                        </p>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Payroll --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Payroll
                        </span>

                        <div
                            class="flex size-9 items-center justify-center
                                   rounded-lg bg-violet-50">
                            <x-wirekit::icon name="wallet" class="size-5 text-violet-600" />
                        </div>

                    </div>

                    <div>

                        <p class="text-xl font-semibold text-slate-900">
                            Rp 5.250.000
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            August 2026
                        </p>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Work Status --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Work Status
                        </span>

                        <div
                            class="flex size-9 items-center justify-center
                                   rounded-lg bg-amber-50">
                            <x-wirekit::icon name="briefcase" class="size-5 text-amber-600" />
                        </div>

                    </div>

                    <div>

                        <p class="text-xl font-semibold text-slate-900">
                            Active
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Full-time employee
                        </p>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        3. MY ATTENDANCE
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Status attendance hari ini
        - Jam masuk
        - Jam keluar
        - Status keterlambatan
        - Shortcut ke halaman attendance

        Ini fokus pada attendance milik employee sendiri.
    ========================================================== --}}
    <div class="grid gap-6 lg:grid-cols-2">

        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            My Attendance
                        </h2>

                        <p class="text-sm text-slate-500">
                            Ringkasan kehadiran kamu hari ini.
                        </p>

                    </x-wirekit::stack>

                    <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                        View
                    </x-wirekit::button>

                </div>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    <div class="rounded-lg border border-emerald-200
                               bg-emerald-50 p-4">

                        <div class="flex items-center justify-between gap-4">

                            <div>

                                <p class="text-sm font-semibold text-emerald-700">
                                    Present Today
                                </p>

                                <p class="mt-1 text-xs text-emerald-600">
                                    Thursday, 05 September 2026
                                </p>

                            </div>

                            <span
                                class="inline-flex items-center rounded-full
                                       bg-white px-2.5 py-1
                                       text-xs font-medium text-emerald-600">
                                On Time
                            </span>

                        </div>

                    </div>

                    <div class="grid grid-cols-2 gap-4">

                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Clock In
                            </p>

                            <p class="mt-1 text-base font-semibold text-slate-800">
                                08:03 AM
                            </p>

                        </div>

                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Clock Out
                            </p>

                            <p class="mt-1 text-base font-semibold text-slate-800">
                                —
                            </p>

                        </div>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- =====================================================
            4. MY LEAVE
            -----------------------------------------------------
            Nanti digunakan untuk:
            - Menampilkan sisa cuti
            - Cuti yang sudah digunakan
            - Pengajuan cuti terakhir
            - Status pengajuan cuti
        ====================================================== --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            My Leave
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi cuti dan pengajuan terakhir.
                        </p>

                    </x-wirekit::stack>

                    <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                        View
                    </x-wirekit::button>

                </div>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    <div class="grid grid-cols-2 gap-4">

                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Remaining
                            </p>

                            <p class="mt-1 text-2xl font-semibold text-slate-900">
                                8
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                days
                            </p>

                        </div>

                        <div>

                            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                Used
                            </p>

                            <p class="mt-1 text-2xl font-semibold text-slate-900">
                                4
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                days
                            </p>

                        </div>

                    </div>

                    <x-wirekit::divider />

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Latest Request
                        </p>

                        <div class="mt-2 flex items-center justify-between gap-4">

                            <div>

                                <p class="text-sm font-medium text-slate-800">
                                    Annual Leave
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    20 – 21 August 2026
                                </p>

                            </div>

                            <span
                                class="inline-flex items-center rounded-full
                                       bg-emerald-50 px-2.5 py-1
                                       text-xs font-medium text-emerald-600">
                                Approved
                            </span>

                        </div>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        5. MY PAYROLL
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Payroll terakhir
        - Total pembayaran
        - Status pembayaran
        - Informasi komponen gaji sederhana
        - Shortcut ke riwayat payroll
    ========================================================== --}}
    <div class="grid gap-6 lg:grid-cols-2">

        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            My Payroll
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi payroll terbaru.
                        </p>

                    </x-wirekit::stack>

                    <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                        View
                    </x-wirekit::button>

                </div>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            August 2026
                        </p>

                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            Rp 5.250.000
                        </p>

                        <span
                            class="mt-2 inline-flex items-center rounded-full
                                   bg-emerald-50 px-2.5 py-1
                                   text-xs font-medium text-emerald-600">
                            Paid
                        </span>

                    </div>

                    <x-wirekit::divider />

                    <div class="grid grid-cols-2 gap-4">

                        <div>

                            <p class="text-xs text-slate-400">
                                Base Salary
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                Rp 4.800.000
                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-slate-400">
                                Allowance
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                Rp 450.000
                            </p>

                        </div>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- =====================================================
            6. MY EMPLOYMENT
            -----------------------------------------------------
            Nanti digunakan untuk:
            - Position / jabatan
            - Division
            - Team
            - Status employee
            - Informasi pekerjaan utama
        ====================================================== --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            My Employment
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi pekerjaan kamu.
                        </p>

                    </x-wirekit::stack>

                    <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                        View
                    </x-wirekit::button>

                </div>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Position
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            Software Developer
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Division
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            Technology
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Team
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            Backend Development
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Status
                        </p>

                        <span
                            class="mt-1 inline-flex items-center rounded-full
                                   bg-emerald-50 px-2.5 py-1
                                   text-xs font-medium text-emerald-600">
                            Active
                        </span>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        7. ACTION REQUIRED
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Menampilkan hal yang perlu dilakukan employee
        - Dokumen yang belum lengkap
        - Approval / request yang perlu ditindaklanjuti
        - Reminder penting

        Ini bersifat situasional:
        kalau tidak ada action, section ini bisa kosong / disembunyikan.
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Action Required
                </h2>

                <p class="text-sm text-slate-500">
                    Hal-hal yang membutuhkan perhatian kamu.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <x-wirekit::stack gap="sm">

                <div class="flex items-center justify-between gap-4 py-3">

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="flex size-9 shrink-0 items-center justify-center
                                   rounded-full bg-amber-50">
                            <x-wirekit::icon name="exclamation-triangle" class="size-5 text-amber-600" />
                        </div>

                        <div class="min-w-0">

                            <p class="truncate text-sm font-medium text-slate-800">
                                Complete employee information
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Your emergency contact information is incomplete.
                            </p>

                        </div>

                    </div>

                    <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                        Complete
                    </x-wirekit::button>

                </div>

                <x-wirekit::divider />

                <div class="flex items-center justify-between gap-4 py-3">

                    <div class="flex min-w-0 items-center gap-3">

                        <div
                            class="flex size-9 shrink-0 items-center justify-center
                                   rounded-full bg-sky-50">
                            <x-wirekit::icon name="document-text" class="size-5 text-sky-600" />
                        </div>

                        <div class="min-w-0">

                            <p class="truncate text-sm font-medium text-slate-800">
                                Review company policy
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                New employee handbook is available.
                            </p>

                        </div>

                    </div>

                    <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                        Review
                    </x-wirekit::button>

                </div>

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =========================================================
        8. ADDITIONAL ACCESS
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Fitur tambahan berdasarkan permission
        - BUKAN dashboard berbeda
        - Satu dashboard tetap dipakai seluruh employee

        Contoh nanti:
        @can('view-employee') → Employee Management
        @can('view-team')     → Team Management
        @can('view-report')   → Reports

        Jadi role tambahan hanya menentukan widget/fitur yang muncul.
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Additional Access
                </h2>

                <p class="text-sm text-slate-500">
                    Fitur tambahan yang tersedia berdasarkan akses kamu.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                {{-- Employee Management --}}
                <div
                    class="rounded-lg border border-slate-200
                           p-4 transition hover:border-[#30AFFF]">

                    <x-wirekit::stack gap="sm">

                        <div
                            class="flex size-9 items-center justify-center
                                   rounded-lg bg-sky-50">
                            <x-wirekit::icon name="users" class="size-5 text-sky-600" />
                        </div>

                        <div>

                            <h3 class="text-sm font-semibold text-slate-800">
                                Employee Management
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Manage employee information and accounts.
                            </p>

                        </div>

                        <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                            Open
                        </x-wirekit::button>

                    </x-wirekit::stack>

                </div>


                {{-- Team Management --}}
                <div
                    class="rounded-lg border border-slate-200
                           p-4 transition hover:border-[#30AFFF]">

                    <x-wirekit::stack gap="sm">

                        <div
                            class="flex size-9 items-center justify-center
                                   rounded-lg bg-violet-50">
                            <x-wirekit::icon name="user-group" class="size-5 text-violet-600" />
                        </div>

                        <div>

                            <h3 class="text-sm font-semibold text-slate-800">
                                Team Management
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                View and manage your team.
                            </p>

                        </div>

                        <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                            Open
                        </x-wirekit::button>

                    </x-wirekit::stack>

                </div>


                {{-- Reports --}}
                <div
                    class="rounded-lg border border-slate-200
                           p-4 transition hover:border-[#30AFFF]">

                    <x-wirekit::stack gap="sm">

                        <div
                            class="flex size-9 items-center justify-center
                                   rounded-lg bg-emerald-50">
                            <x-wirekit::icon name="chart-bar" class="size-5 text-emerald-600" />
                        </div>

                        <div>

                            <h3 class="text-sm font-semibold text-slate-800">
                                Reports
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Access reports available to you.
                            </p>

                        </div>

                        <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                            Open
                        </x-wirekit::button>

                    </x-wirekit::stack>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =========================================================
        9. ANNOUNCEMENTS
        ---------------------------------------------------------
        Nanti digunakan untuk:
        - Pengumuman perusahaan
        - Informasi dari HR
        - Event / kegiatan perusahaan
        - Update kebijakan
        - Informasi penting lainnya

        Idealnya hanya menampilkan beberapa pengumuman terbaru.
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Announcements
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi terbaru dari perusahaan.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <x-wirekit::stack gap="sm">

                <div class="py-3">

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0">

                            <h3 class="text-sm font-semibold text-slate-800">
                                Company Town Hall
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Town Hall bulanan akan dilaksanakan pada
                                10 September 2026.
                            </p>

                        </div>

                        <span class="shrink-0 text-xs text-slate-400">
                            2 days ago
                        </span>

                    </div>

                </div>

                <x-wirekit::divider />

                <div class="py-3">

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0">

                            <h3 class="text-sm font-semibold text-slate-800">
                                Updated Employee Handbook
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Employee handbook versi terbaru sekarang tersedia.
                            </p>

                        </div>

                        <span class="shrink-0 text-xs text-slate-400">
                            5 days ago
                        </span>

                    </div>

                </div>

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
