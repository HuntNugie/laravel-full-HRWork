<x-slot:sidebar>

    <x-wirekit::sidebar>

        <x-wirekit::sidebar.group label="Overview">

            <x-wirekit::sidebar.item href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home" wire:navigate>
                Dashboard
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>


        <x-wirekit::sidebar.group label="Role dan sistem akses">

            <x-wirekit::sidebar.item href="#" icon="clock" wire:navigate>
                Presensi
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item href="#" icon="shield-check" wire:navigate>
                Roles
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item href="#" icon="key" wire:navigate>
                Permissions
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>

        @canany(['view-user'])
            <x-wirekit::sidebar.group collapsible label="User dan hak akses">
                @can('view-user')
                    <x-wirekit::sidebar.item href="{{ route('user.view') }}"  icon="user" :active="request()->routeIs('user.view')" wire:navigate>
                        Manajemen User
                    </x-wirekit::sidebar.item>
                @endcan


                <x-wirekit::sidebar.item href="#" icon="shield-check" wire:navigate>
                    Manajemen Role
                </x-wirekit::sidebar.item>


            </x-wirekit::sidebar.group>
        @endcanany

        @canany(['view-divisi', 'view-team', 'view-position'])

            <x-wirekit::sidebar.group collapsible label="Organisasi">

                @can('view-divisi')
                    <x-wirekit::sidebar.item href="{{ route('divisi.view') }}" icon="building" :active="request()->routeIs('divisi.view')" wire:navigate>
                        Manajemen Divisi
                    </x-wirekit::sidebar.item>
                @endcan


                @can('view-team')
                    <x-wirekit::sidebar.item href="{{ route('team.view') }}" icon="user-group" :active="request()->routeIs('team.view')" wire:navigate>
                        Manajemen Team
                    </x-wirekit::sidebar.item>
                @endcan


                @can('view-position')
                    <x-wirekit::sidebar.item href="{{ route('position.view') }}" :active="request()->routeIs('position.view')" icon="badge" wire:navigate>
                        Manajemen Jabatan
                    </x-wirekit::sidebar.item>
                @endcan



            </x-wirekit::sidebar.group>
        @endcanany

        @canany(['view-employee'])
            <x-wirekit::sidebar.group collapsible label="SDM">

                @can('view-employee')
                    <x-wirekit::sidebar.item href="{{ route('employee.view') }}" :active="request()->routeIs('employee.view')" icon="users" wire:navigate>
                        Manajemen Karyawan
                    </x-wirekit::sidebar.item>
                @endcan




                {{-- <x-wirekit::sidebar.item href="#" icon="academic-cap" wire:navigate>
                Training & TNA
            </x-wirekit::sidebar.item> --}}



            </x-wirekit::sidebar.group>
        @endcanany
        @canany(['view-benefit'])

            <x-wirekit::sidebar.group collapsible label="Kompensasi">


                @can('view-benefit')
                    <x-wirekit::sidebar.item href="{{ route('benefit.view') }}" :active="request()->routeIs('benefit.view')" icon="rocket-launch"
                        wire:navigate>
                        Manajemen Tunjangan
                    </x-wirekit::sidebar.item>
                @endcan


                {{-- <x-wirekit::sidebar.item href="#" icon="academic-cap" wire:navigate>
                Training & TNA
            </x-wirekit::sidebar.item> --}}



            </x-wirekit::sidebar.group>
        @endcanany




    </x-wirekit::sidebar>

</x-slot:sidebar>
