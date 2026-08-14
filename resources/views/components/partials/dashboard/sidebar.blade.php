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