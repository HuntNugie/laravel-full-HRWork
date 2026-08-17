<x-slot:sidebar>

    <x-wirekit::sidebar>

        <x-wirekit::sidebar.group label="Overview">

            <x-wirekit::sidebar.item href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                icon="home" wire:navigate>
                Dashboard
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>


        <x-wirekit::sidebar.group label="Personal access">

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


        <x-wirekit::sidebar.group label="{{ auth()->user()->getRoleNames()->first() }}">

            @can('view-divisi')
                <x-wirekit::sidebar.item href="{{ route('divisi.view') }}" icon="building"
                    :active="request()->routeIs('divisi.view')" wire:navigate>
                    Divisi
                </x-wirekit::sidebar.item>
            @endcan


            @can('view-team')

                <x-wirekit::sidebar.item href="{{ route('team.view') }}" icon="user-group" :active="request()->routeIs('team.view')" wire:navigate>
                    Team
                </x-wirekit::sidebar.item>
            @endcan

            <x-wirekit::sidebar.item href="#" icon="chart-bar" wire:navigate>
                Performance
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item href="#" icon="academic-cap" wire:navigate>
                Training & TNA
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>






    </x-wirekit::sidebar>

</x-slot:sidebar>