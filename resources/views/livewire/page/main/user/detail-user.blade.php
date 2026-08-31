<x-wirekit::stack gap="md">

    {{-- =========================================================
        PAGE HEADING
    ========================================================== --}}

    <x-wirekit::stack gap="sm">

        <a href="{{ route('user.view') }}" wire:navigate
            class="inline-flex w-fit items-center text-sm font-medium
                   text-slate-500 transition hover:text-[#30AFFF]">
            ← Kembali ke Users
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Role & Sistem Akses
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            User Detail
        </h1>

        <p class="text-sm text-slate-500">
            Informasi akun, status akses, role, dan keamanan user.
        </p>

    </x-wirekit::stack>


    {{-- =========================================================
        ACCOUNT HEADER
    ========================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-4">

                    {{-- Avatar --}}
                    <div
                        class="flex size-14 shrink-0 items-center justify-center
                               rounded-full bg-sky-100">
                        <span class="text-lg font-semibold text-sky-600">
                            BS
                        </span>
                    </div>

                    <div class="min-w-0">

                        <h2 class="truncate text-xl font-semibold text-slate-900">
                            {{ $user->name }}
                        </h2>

                        <p class="mt-1 truncate text-sm text-slate-500">
                            {{ $user->email }}
                        </p>

                        <div class="mt-2 flex flex-wrap items-center gap-2">

                            <span
                                class="inline-flex items-center rounded-full
                                       bg-emerald-50 px-2.5 py-1
                                       text-xs font-medium text-emerald-600">
                                Active
                            </span>

                            <span
                                class="inline-flex items-center rounded-full
                                       bg-slate-100 px-2.5 py-1
                                       text-xs font-medium text-slate-600">
                                {{ $user->employees->employee_code }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- Account Action --}}
                <div class="flex shrink-0">

                    <x-wirekit::button type="button" class="bg-red-500 text-white hover:bg-red-600">
                        Deactivate Account
                    </x-wirekit::button>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =========================================================
        ACCOUNT + ROLES
    ========================================================== --}}

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Account Information --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Account Information
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi dasar akun user.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Name
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $user->name }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Email
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $user->email }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Account Status
                        </p>
                        <span @class([
                            "inline-flex items-center rounded-full
                                                                                            px-2.5 py-1
                                                                                           text-xs font-medium ",
                            'bg-emerald-50 text-emerald-600' => $user->status === 'active',
                            'bg-yellow-50 text-yellow-600' => $user->status === 'pending',
                            'bg-red-50 text-red-600' => $user->status === 'inactive',
                        ])>
                            {{ $user->status }}
                        </span>


                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Created At
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $user->created_at->format('d F Y') }}
                        </p>

                    </div>




                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Roles --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Roles
                        </h2>

                        <p class="text-sm text-slate-500">
                            Role yang dimiliki user.
                        </p>

                    </x-wirekit::stack>


                    <x-wirekit::button type="button" size="sm">
                        Edit Roles
                    </x-wirekit::button>

                </div>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    {{-- Base Role --}}
                    <div>

                        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-400">
                            Role
                        </p>

                        <span
                            class="inline-flex items-center rounded-full
                                   bg-slate-100 px-3 py-1.5
                                   text-xs font-medium text-slate-700">
                            Employee
                        </span>

                    </div>





                    {{-- Permission Info --}}
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-sm font-medium text-slate-800">
                                    Effective Permissions
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Permission user berasal dari role yang dimilikinya.
                                </p>

                            </div>

                            <x-wirekit::button type="button" size="sm">
                                View Permissions
                            </x-wirekit::button>

                        </div>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        EMPLOYEE + SECURITY
    ========================================================== --}}

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Employee Information --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Employee Information
                        </h2>

                        <p class="text-sm text-slate-500">
                            Data employee yang terhubung dengan akun ini.
                        </p>

                    </x-wirekit::stack>

                    <x-wirekit::button type="button" size="sm">
                        View Employee
                    </x-wirekit::button>

                </div>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Employee Code
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $user->employees->employee_code }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Position
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $user->employees?->position->name ?? 'Belum ada jabatan' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Division
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $user->employees->team->divisi->name ?? 'Belum ada divisi' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Team
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $user->employees->team->name ?? 'Belum ada team' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Employee Status
                        </p>

                        <span
                            class="mt-1 inline-flex items-center rounded-full
                                   bg-emerald-50 px-2.5 py-1
                                   text-xs font-medium text-emerald-600">
                            {{ $user->employees->status_employee ?? 'Tidak ada status' }}
                        </span>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Security --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Security
                        </h2>

                        <p class="text-sm text-slate-500">
                            Pengaturan keamanan akun user.
                        </p>

                    </x-wirekit::stack>

                </div>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    {{-- Password --}}
                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Password
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Terakhir diubah 18 August 2026
                            </p>

                        </div>

                        <x-wirekit::button type="button" size="sm">
                            Change Password
                        </x-wirekit::button>

                    </div>


                    <div class="h-px bg-slate-100"></div>


                    {{-- Account Activity --}}
                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Account Activity
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Last login: 31 August 2026, 13:42
                            </p>

                        </div>

                        <x-wirekit::button type="button" size="sm">
                            View Activity
                        </x-wirekit::button>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        ACCOUNT STATUS INFORMATION
    ========================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-800">
                        Account Status
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Akun aktif dan dapat digunakan untuk login ke sistem HRWork.
                    </p>

                </div>

                <span @class([
                    "inline-flex items-center rounded-full
                                                            px-2.5 py-1
                                                           text-xs font-medium ",
                    'bg-emerald-50 text-emerald-600' => $user->status === 'active',
                    'bg-yellow-50 text-yellow-600' => $user->status === 'pending',
                    'bg-red-50 text-red-600' => $user->status === 'inactive',
                ])>
                    {{ $user->status }}
                </span>


            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
