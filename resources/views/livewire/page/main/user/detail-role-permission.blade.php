<x-wirekit::stack gap="md">

    {{-- PAGE HEADING --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('user.show', $user->id) }}" wire:navigate
            class="inline-flex w-fit items-center text-sm font-medium
                   text-slate-500 transition hover:text-[#30AFFF]">
            ← Kembali ke Users
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Pengguna
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Detail Pengguna
        </h1>

        <p class="text-sm text-slate-500">
            Informasi pengguna serta role dan hak akses yang dimilikinya.
        </p>

    </x-wirekit::stack>


    {{-- USER INFORMATION --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                {{-- USER --}}
                <div class="flex items-center gap-4">

                    <div
                        class="flex size-14 shrink-0 items-center justify-center
                               rounded-full bg-sky-100">
                        <span class="text-lg font-semibold text-sky-600">
                            N
                        </span>
                    </div>

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-2">

                            <h2 class="text-xl font-semibold text-slate-900">
                                {{ $user->name }}
                            </h2>

                            <x-wirekit::badge variant="success">
                                Aktif
                            </x-wirekit::badge>

                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $user->email }}
                        </p>

                    </div>

                </div>


                {{-- SUMMARY --}}
                <div class="flex items-center gap-8">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Roles
                        </p>

                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            {{ count($user->roles) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Permissions
                        </p>

                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            {{ $user->getAllPermissions()->count() }}
                        </p>
                    </div>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- ROLE & PERMISSION --}}
    <x-wirekit::stack gap="sm">

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Role & Permission
            </h2>

            <p class="text-sm text-slate-500">
                Hak akses pengguna berdasarkan role yang dimilikinya.
            </p>

        </x-wirekit::stack>


        {{-- SEARCH --}}
        <div class="max-w-md pt-2">

            <x-wirekit::input type="search" name="search" wire:model.live.debounce.500ms='search'
                placeholder="Cari role atau permission..." />

        </div>


        {{-- ROLE CARDS --}}
        <div class="grid items-stretch gap-5 lg:grid-cols-2">


            @forelse ($roles as $role)
                <x-wirekit::card class="flex h-full flex-col">

                    <x-wirekit::card.header>

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex min-w-0 items-center gap-3">

                                <div
                                    class="flex size-10 shrink-0 items-center justify-center
                                       rounded-xl bg-sky-100">
                                    <x-wirekit::icon name="shield-check" class="size-5 text-sky-600" />
                                </div>

                                <div class="min-w-0">

                                    <h3 class="font-semibold text-slate-900">
                                        {{ strtoupper($role->name) }}
                                    </h3>

                                </div>

                            </div>


                            {{-- ROLE ACTION --}}
                            @can('show-role')
                                <x-wirekit::button type="button" size="sm" variant="ghost"
                                    href="{{ route('role.show', $role->id) }}" wire:navigate>
                                    Detail Role
                                </x-wirekit::button>
                            @endcan


                        </div>

                        <div class="mt-3">

                            <span
                                class="inline-flex items-center rounded-full
                                   bg-slate-100 px-2.5 py-1
                                   text-xs font-medium text-slate-600">
                                {{ $role->permissions->count() }} Permissions
                            </span>

                        </div>

                    </x-wirekit::card.header>


                    {{-- PERMISSION AREA --}}
                    <x-wirekit::card.body class="flex-1">

                        <div class="h-96 overflow-y-auto pr-2">

                            <x-wirekit::stack gap="lg">



                                @forelse ($this->getPermissions($role) as $group => $permission)
                                    <x-wirekit::stack gap="sm">

                                        <div class="flex items-center justify-between">

                                            <h4 class="text-sm font-semibold text-slate-800">
                                                {{ $group }}
                                            </h4>

                                            <span class="text-xs text-slate-400">
                                                {{ $permission->count() }} Permission
                                            </span>

                                        </div>

                                        <div class="flex flex-wrap gap-2">


                                            @foreach ($permission as $permis)
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-lg
                                               border border-slate-200 bg-slate-50
                                               px-2.5 py-1.5 text-xs font-medium
                                               text-slate-700">
                                                    <x-wirekit::icon name="check" class="size-3.5 text-emerald-500" />
                                                    {{ str_replace('-', ' ', $permis->name) }}
                                                </span>
                                            @endforeach
                                        </div>

                                    </x-wirekit::stack>
                                @empty
                                    <div
                                        class="flex min-h-[140px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                                        <x-wirekit::icon name="shield-exclamation" class="size-8 text-slate-300" />
                                        <p class="mt-3 text-sm font-medium text-slate-600">
                                            Belum ada permission
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Role ini belum memiliki hak akses.
                                        </p>
                                    </div>
                                @endforelse


                            </x-wirekit::stack>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>
            @empty
                <x-wirekit::card class="lg:col-span-2">
                    <x-wirekit::card.body>
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-wirekit::icon name="shield-check" class="size-10 text-slate-300" />
                            <h3 class="mt-3 text-sm font-semibold text-slate-900">
                                Belum ada role
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                Pengguna ini belum memiliki role atau hak akses.
                            </p>
                        </div>
                    </x-wirekit::card.body>
                </x-wirekit::card>
            @endforelse



        </div>

    </x-wirekit::stack>

</x-wirekit::stack>
