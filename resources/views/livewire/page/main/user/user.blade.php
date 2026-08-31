<x-wirekit::stack gap="md">

    {{-- =========================================================
        PAGE HEADING
    ========================================================== --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                Role & Sistem Akses
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                User Management
            </h1>

            <p class="text-sm text-slate-500">
                Kelola status akun, role, dan keamanan user yang terdaftar
                dalam sistem HRWork.
            </p>

        </x-wirekit::stack>

    </div>


    {{-- =========================================================
        SUMMARY
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

                        <span class="h-2.5 w-2.5 rounded-full bg-[#30AFFF]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                      {{$this->totalUser()}}
                    </span>

                    <span class="text-xs text-slate-400">
                        Akun terdaftar
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Active --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Active Accounts
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#C4F7CA]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        {{ $this->totalActive }}
                    </span>

                    <span class="text-xs text-emerald-600">
                        Akun aktif
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Pending --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Pending Activation
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#FFA239]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                     {{$this->totalPending}}
                    </span>

                    <span class="text-xs text-amber-600">
                        Menunggu aktivasi
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Inactive --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Inactive Accounts
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#FF5656]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        {{ $this->totalInactive }}
                    </span>

                    <span class="text-xs text-red-600">
                        Akun tidak aktif
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        USER MANAGEMENT
    ========================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        User Accounts
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar akun yang dibuat melalui proses employee management.
                    </p>

                </x-wirekit::stack>


                <div class="w-full lg:w-72">

                    <x-wirekit::input placeholder="Cari nama, email, atau employee code" name="search" wire:model.live.debounce.400ms="search"
                        class="text-black" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="name">
                                User
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Employee
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Roles
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Account Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="updated_at">
                                Last Updated
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        {{-- =================================================
                            USER 1
                        ================================================== --}}
                        @forelse ($users as $user )
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">

                                            <span class="text-sm font-semibold text-sky-600">
                                                BS
                                            </span>

                                        </div>

                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-800">
                                                {{ $user->name }}
                                            </p>

                                            <p class="truncate text-xs text-slate-400">
                                                {{ $user->email }}
                                            </p>

                                        </div>

                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <div>

                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $user->employees->employee_code ?? 'Tidak ada kode pagawai' }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            {{ $user->employees->position->name ?? 'Belum ada jabatan' }}
                                        </p>

                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <div class="flex flex-wrap gap-1.5">

                                        @if ($user->getRoleNames()->isNotEmpty())
                                            @foreach ($user->getRoleNames() as $role)
                                                <span @class([
                                                    'rounded-full px-2.5 py-1 text-xs font-medium',
                                                    'text-olive-600 bg-olive-100' => $role === 'Employee',
                                                    'text-purple-600 bg-purple-100' => $role === 'super-admin',
                                                    'text-sky-600 bg-sky-100' => $role === 'Supervisor',
                                                    'text-red-600 bg-red-100' => $role === 'HR',
                                                    'text-blue-600 bg-blue-100' => $role === 'Administrator',
                                                ])>
                                                    {{ $role }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span
                                                class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-500">
                                                Belum ada role
                                            </span>
                                        @endif



                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span
                                        @class(["inline-flex items-center rounded-full
                                            px-2.5 py-1
                                           text-xs font-medium ",
                                           "bg-emerald-50 text-emerald-600" => $user->status === "active",
                                           "bg-yellow-50 text-yellow-600" => $user->status === "pending",
                                           "bg-red-50 text-red-600" => $user->status === "inactive",
                                           ])>
                                        {{ $user->status }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-500">
                                       {{$user->updated_at->diffForHumans()}}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <x-wirekit::button type="button" href="{{ route('user.show',$user->id) }}" wire:navigate class="px-3 py-1.5 text-xs">
                                        Detail
                                    </x-wirekit::button>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty
                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="6" class="py-10 text-center">

                                    <span class="text-sm text-slate-500">
                                        Tidak ada user yang ditemukan.
                                    </span>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse



                        {{-- =================================================
                            EMPTY STATE
                        ================================================== --}}

                        {{--

                        --}}

                    </x-wirekit::table.body>

                </x-wirekit::table>
                {{ $users->links() }}
            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
