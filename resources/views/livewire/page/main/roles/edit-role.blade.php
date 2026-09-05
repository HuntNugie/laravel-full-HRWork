<div>
    <x-wirekit::stack gap="lg">

        {{-- ===================================================== --}}
        {{-- BACK --}}
        {{-- ===================================================== --}}
        <a href="{{ route('role.view') }}" wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-900">
            <x-wirekit::icon name="arrow-left" class="h-4 w-4" />

            Back to Roles
        </a>


        {{-- ===================================================== --}}
        {{-- PAGE HEADER --}}
        {{-- ===================================================== --}}
        <x-wirekit::stack gap="1">

            <h1 class="text-2xl font-semibold text-slate-900">
                Edit Role
            </h1>

            <p class="text-sm text-slate-500">
                Edit a new role and configure its permissions.
            </p>

        </x-wirekit::stack>


        {{-- ===================================================== --}}
        {{-- ROLE INFORMATION --}}
        {{-- ===================================================== --}}
        <x-wirekit::form wire:submit="submit">

            <x-wirekit::card class="mb-4">

                <x-wirekit::card.header>
                    <x-wirekit::stack gap="1">

                        <h2 class="text-sm font-semibold text-slate-900">
                            Role Information
                        </h2>

                        <p class="text-sm text-slate-500">
                            Define the name of this role.
                        </p>

                    </x-wirekit::stack>
                </x-wirekit::card.header>

                <x-wirekit::card.body>

                    <div class="max-w-xl">

                        <x-wirekit::input label="Role Name" placeholder="e.g. hr" wire:model.live.debounce.400ms="roleName" name="roleName" />



                        <p class="mt-2 text-xs text-slate-500">
                            Use a unique role name such as
                            <span class="font-medium text-slate-700">hr</span>,
                            <span class="font-medium text-slate-700">admin</span>,
                            or
                            <span class="font-medium text-slate-700">employee</span>.
                        </p>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>


            {{-- ===================================================== --}}
            {{-- PERMISSIONS --}}
            {{-- ===================================================== --}}


            <x-wirekit::card>

                <x-wirekit::card.header>

                    <x-wirekit::row align="center" justify="between" class="gap-4">

                        <x-wirekit::stack gap="1">

                            <h2 class="text-sm font-semibold text-slate-900">
                                Permissions
                            </h2>

                            <p class="text-sm text-slate-500">
                                Select the permissions this role should have.
                            </p>

                        </x-wirekit::stack>

                        <x-wirekit::button type="button" size="sm" intent="neutral" surface="outline"
                            wire:click="selectAllPermissions">
                            Select All
                        </x-wirekit::button>

                    </x-wirekit::row>

                </x-wirekit::card.header>


                <x-wirekit::card.body>

                    <x-wirekit::stack gap="xl">

                        @foreach ($this->getPermissions as $group => $permissions)
                            {{-- ================================================= --}}
                            {{-- EMPLOYEE --}}
                            {{-- ================================================= --}}
                            <x-wirekit::stack gap="md">

                                <x-wirekit::row align="center" justify="between">

                                    <x-wirekit::stack gap="1">

                                        <h3 class="text-sm font-semibold text-slate-900">
                                            {{ $group }}
                                        </h3>

                                        <span class="text-xs text-slate-500">
                                            {{ $permissions->count() }} permissions
                                        </span>

                                    </x-wirekit::stack>

                                    <button type="button"
                                        class="text-xs font-medium text-slate-500 hover:text-slate-900"
                                        wire:click="selectAllPermissionByGroup('{{ $group }}')">
                                        Select all
                                    </button>

                                </x-wirekit::row>


                                <div class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">

                                    @foreach ($permissions as $permission)
                                        <x-wirekit::checkbox :label="str($permission->name)->replace('-', ' ')->headline()" wire:model="permissionsValue"
                                            value="{{ $permission->name }}" />
                                    @endforeach

                                </div>

                            </x-wirekit::stack>


                            <x-wirekit::divider />
                        @endforeach

                    </x-wirekit::stack>

                </x-wirekit::card.body>

            </x-wirekit::card>


            {{-- ===================================================== --}}
            {{-- FORM ACTIONS --}}
            {{-- ===================================================== --}}
            <x-wirekit::row justify="end" gap="sm">

                <x-wirekit::button type="button" intent="neutral" surface="outline" href="{{ route('role.view') }}"
                    wire:navigate>
                    Cancel
                </x-wirekit::button>

                <x-wirekit::button type="submit" intent="primary">
                    Edit Role
                </x-wirekit::button>

            </x-wirekit::row>
        </x-wirekit::form>
    </x-wirekit::stack>
</div>
