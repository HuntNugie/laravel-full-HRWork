<x-wirekit::stack gap="md">

    {{-- PAGE HEADING --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('role.view') }}" wire:navigate
            class="inline-flex w-fit items-center text-sm font-medium
                   text-slate-500 transition hover:text-[#30AFFF]">
            ← Kembali ke Roles
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Role & Sistem Akses
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Role Detail
        </h1>

        <p class="text-sm text-slate-500">
            Informasi role, permission, dan user yang menggunakan role ini.
        </p>

    </x-wirekit::stack>


    {{-- ROLE HEADER --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-4">

                    <div
                        class="flex size-14 shrink-0 items-center justify-center
                               rounded-full bg-sky-100">
                        <x-wirekit::icon name="shield-check" class="size-7 text-sky-600" />
                    </div>

                    <div class="min-w-0">

                        <h2 class="text-xl font-semibold text-slate-900">
                            {{ strtoupper($role->name) }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $role->name }}
                        </p>

                        <div class="mt-2 flex flex-wrap items-center gap-2">



                        </div>

                    </div>

                </div>

                <div class="flex shrink-0 items-center gap-2">

                    <x-wirekit::button type="button" size="sm" href="{{ route('role.edit', $role->id) }}"
                        wire:navigate>
                        Edit Role
                    </x-wirekit::button>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- ROLE INFORMATION + ACCESS SUMMARY --}}
    <div class="grid gap-6 lg:grid-cols-2">

        {{-- ROLE INFORMATION --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Role Information
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi dasar role dalam sistem.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Role Name
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $role->name }}
                        </p>
                    </div>



                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Updated At
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $role->updated_at->diffForHumans() }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Created At
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $role->created_at->format('d F Y') }}
                        </p>
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- ACCESS SUMMARY --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Access Summary
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan akses yang dimiliki role ini.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <div class="grid gap-4 sm:grid-cols-2">

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Permissions
                        </p>

                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $role->permissions->count() }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Assigned Users
                        </p>

                        <p class="mt-1 text-2xl font-semibold text-slate-900">
                            {{ $role->users->count() }}
                        </p>
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- PERMISSIONS --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex items-center justify-between gap-4">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Permissions
                    </h2>

                    <p class="text-sm text-slate-500">
                        Permission yang dimiliki oleh role ini.
                    </p>

                </x-wirekit::stack>

                <span
                    class="inline-flex items-center rounded-full
                           bg-slate-100 px-2.5 py-1
                           text-xs font-medium text-slate-600">
                    {{ $role->permissions->count() }} permissions
                </span>

            </div>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <x-wirekit::stack gap="xl">

                {{-- ORGANIZATION --}}
                @forelse($this->getPermissions as $group => $permissions)
                    <x-wirekit::stack gap="md">

                        <x-wirekit::stack gap="1">

                            <h3 class="text-sm font-semibold text-slate-900">
                                {{ $group }}
                            </h3>

                            <span class="text-xs text-slate-500">
                                {{ $permissions->count() }} permissions
                            </span>

                        </x-wirekit::stack>

                        <div class="flex flex-wrap gap-x-8 gap-y-3">

                            @foreach ($permissions as $permission)
                                <div class="flex items-center gap-2">
                                    <x-wirekit::icon name="check" class="size-4 text-emerald-600" />

                                    <span class="text-sm text-slate-700">
                                        {{ $permission->name }}
                                    </span>
                                </div>
                            @endforeach

                        </div>

                    </x-wirekit::stack>

                    <x-wirekit::divider />
                @empty
                    <p class="text-sm text-slate-500">
                        Role ini belum memiliki permission.
                    </p>
                @endforelse

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- ASSIGNED USERS --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex items-center justify-between gap-4">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Assigned Users
                    </h2>

                    <p class="text-sm text-slate-500">
                        User yang menggunakan role ini.
                    </p>

                </x-wirekit::stack>

                <livewire:components.main.roles.assign-user :role="$role" :key="$role->id">
                    <x-wirekit::button type="button" size="sm">
                        + Assign User
                    </x-wirekit::button>
                </livewire:components.main.roles.assign-user>

            </div>

        </x-wirekit::card.header>

        <x-wirekit::card.body>

            <x-wirekit::stack gap="sm">

                @forelse($role->users as $user)
                    <div class="flex items-center justify-between gap-4 py-3">

                        <div class="flex min-w-0 items-center gap-3">

                            <div
                                class="flex size-9 shrink-0 items-center
                                   justify-center rounded-full bg-sky-100">
                                <span class="text-sm font-semibold text-sky-600">
                                    AF
                                </span>
                            </div>

                            <div class="min-w-0">

                                <p class="truncate text-sm font-medium text-slate-900">
                                    {{ $user->name }}
                                </p>

                                <p class="truncate text-xs text-slate-500">
                                    {{ $user->email }}
                                </p>

                            </div>

                        </div>

                        <x-wirekit::button type="button" size="sm" intent="neutral" surface="ghost">
                            Remove
                        </x-wirekit::button>

                    </div>

                    <x-wirekit::divider />
                @empty
                    <p class="text-sm text-slate-500">
                        Role ini belum memiliki user yang menggunakan role ini.
                    </p>
                @endforelse


            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>


</x-wirekit::stack>
