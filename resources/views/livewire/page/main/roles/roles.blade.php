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
        <x-wirekit::filter-builder searchable search-placeholder="Search roles..." add-label="Filter" :fields="[
            [
                'key' => 'name',
                'label' => 'Role',
                'type' => 'select',
                'options' => [
                    [
                        'value' => 'super-admin',
                        'label' => 'Super Admin',
                    ],
                    [
                        'value' => 'hr',
                        'label' => 'HR',
                    ],
                    [
                        'value' => 'admin',
                        'label' => 'Admin',
                    ],
                    [
                        'value' => 'employee',
                        'label' => 'Employee',
                    ],
                ],
            ],

            [
                'key' => 'users_count',
                'label' => 'Users',
                'type' => 'number',
            ],

            [
                'key' => 'permissions_count',
                'label' => 'Permissions',
                'type' => 'number',
            ],
        ]">
            <x-slot:status>
                4 roles
            </x-slot:status>
        </x-wirekit::filter-builder>


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

                            <x-wirekit::table.th>
                                Description
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

                        {{-- Super Admin --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>
                                <x-wirekit::stack gap="1">
                                    <span class="font-medium text-slate-900">
                                        Super Admin
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        System role
                                    </span>
                                </x-wirekit::stack>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td>
                                <span class="text-sm text-slate-600">
                                    Full access to the entire system.
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <span class="font-medium text-slate-900">
                                    1
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <x-wirekit::badge intent="primary">
                                    32
                                </x-wirekit::badge>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="right">
                                <x-wirekit::button size="sm" intent="neutral" surface="outline">
                                    View
                                </x-wirekit::button>
                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- HR --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>
                                <x-wirekit::stack gap="1">
                                    <span class="font-medium text-slate-900">
                                        HR
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        Human Resources
                                    </span>
                                </x-wirekit::stack>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td>
                                <span class="text-sm text-slate-600">
                                    Manage employees, organization,
                                    attendance and HR processes.
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <span class="font-medium text-slate-900">
                                    2
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <x-wirekit::badge intent="primary">
                                    24
                                </x-wirekit::badge>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="right">
                                <x-wirekit::button size="sm" intent="neutral" surface="outline">
                                    View
                                </x-wirekit::button>
                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- Admin --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>
                                <x-wirekit::stack gap="1">
                                    <span class="font-medium text-slate-900">
                                        Admin
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        System Administration
                                    </span>
                                </x-wirekit::stack>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td>
                                <span class="text-sm text-slate-600">
                                    Manage system configuration
                                    and operational data.
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <span class="font-medium text-slate-900">
                                    1
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <x-wirekit::badge intent="primary">
                                    18
                                </x-wirekit::badge>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="right">
                                <x-wirekit::button size="sm" intent="neutral" surface="outline">
                                    View
                                </x-wirekit::button>
                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- Employee --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>
                                <x-wirekit::stack gap="1">
                                    <span class="font-medium text-slate-900">
                                        Employee
                                    </span>

                                    <span class="text-xs text-slate-500">
                                        Standard Access
                                    </span>
                                </x-wirekit::stack>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td>
                                <span class="text-sm text-slate-600">
                                    Basic access for employees.
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <span class="font-medium text-slate-900">
                                    28
                                </span>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="center">
                                <x-wirekit::badge intent="neutral">
                                    6
                                </x-wirekit::badge>
                            </x-wirekit::table.td>

                            <x-wirekit::table.td align="right">
                                <x-wirekit::button size="sm" intent="neutral" surface="outline">
                                    View
                                </x-wirekit::button>
                            </x-wirekit::table.td>

                        </x-wirekit::table.row>

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </x-wirekit::stack>
</div>
