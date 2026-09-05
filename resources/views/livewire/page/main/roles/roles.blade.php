<div>
    <x-wirekit::stack gap="lg">

        {{-- ===================================================== --}}
        {{-- PAGE HEADER --}}
        {{-- ===================================================== --}}
        <x-wirekit::row align="center" justify="between" class="flex-wrap gap-4">
            <x-wirekit::stack gap="1">
                <h1 class="text-2xl font-semibold text-slate-900">
                    Roles
                </h1>

                <p class="text-sm text-slate-500">
                    Manage roles and permissions for your HRIS.
                </p>
            </x-wirekit::stack>

            <x-wirekit::button intent="primary" href="{{ route('role.create') }}" wire:navigate>
                <x-slot:iconLeft>
                    <x-wirekit::icon name="plus" class="h-4 w-4" />
                </x-slot:iconLeft>

                Create Role
            </x-wirekit::button>
        </x-wirekit::row>


        {{-- ===================================================== --}}
        {{-- FILTER --}}
        {{-- ===================================================== --}}
        <x-wirekit::row align="center" justify="between" class="flex-wrap gap-4">

            <x-wirekit::input placeholder="Search roles..." wire:model.live.debounce.400ms="search" name="search">
                <x-slot:iconLeft>
                    <x-wirekit::icon name="search" class="h-4 w-4" />
                </x-slot:iconLeft>
            </x-wirekit::input>

        </x-wirekit::row>


        {{-- ===================================================== --}}
        {{-- ROLE TABLE --}}
        {{-- ===================================================== --}}
        <x-wirekit::card>

            <x-wirekit::card.body class="p-0">

                <x-wirekit::table hoverable table-label="Roles">

                    {{-- ================================================= --}}
                    {{-- TABLE HEAD --}}
                    {{-- ================================================= --}}
                    <x-wirekit::table.head>
                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Role
                            </x-wirekit::table.th>


                            <x-wirekit::table.th align="center">
                                Users
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="center">
                                Permissions
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Action
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>
                    </x-wirekit::table.head>


                    {{-- ================================================= --}}
                    {{-- TABLE BODY --}}
                    {{-- ================================================= --}}
                    <x-wirekit::table.body>

                        @forelse ($roles as $role)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>
                                    <x-wirekit::stack gap="1">
                                        <span class="font-medium text-slate-900">
                                            {{ $role->name }}
                                        </span>


                                    </x-wirekit::stack>
                                </x-wirekit::table.td>


                                <x-wirekit::table.td align="center">
                                    <span class="font-medium text-slate-900">
                                        {{ $role->users()->count() }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td align="center">
                                    <x-wirekit::badge intent="primary">
                                        {{ $role->permissions()->count() }}
                                    </x-wirekit::badge>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td align="right">
                                    <x-wirekit::stack gap="sm" align="center" justify="end" class="flex-wrap">
                                        <x-wirekit::button size="sm" intent="neutral" surface="outline" href="{{ route('role.show',$role->id) }}" wire:navigate>
                                            View
                                        </x-wirekit::button>
                                        <livewire:components.main.roles.modal-delete :role="$role" :key="$role->id">
                                            <x-wirekit::button size="sm" intent="danger" surface="outline">
                                                Delete
                                            </x-wirekit::button>
                                        </livewire:components.main.roles.modal-delete>
                                    </x-wirekit::stack>
                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @empty
                            <x-wirekit::table.row>
                                <x-wirekit::table.td colspan="4">
                                    <div class="py-8 text-center">
                                        <p class="font-medium text-slate-900">
                                            Belum ada role
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Buat role baru untuk mulai mengatur permissions.
                                        </p>

                                        <x-wirekit::button size="sm" intent="primary"
                                            href="{{ route('role.create') }}" wire:navigate class="mt-4">
                                            Create Role
                                        </x-wirekit::button>
                                    </div>
                                </x-wirekit::table.td>
                            </x-wirekit::table.row>
                        @endforelse



                    </x-wirekit::table.body>

                </x-wirekit::table>
                {{ $roles->links() }}
            </x-wirekit::card.body>

        </x-wirekit::card>

    </x-wirekit::stack>
</div>
