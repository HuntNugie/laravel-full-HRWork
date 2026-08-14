<x-slot:sidebar>

    <x-wirekit::sidebar
    >

        <x-wirekit::sidebar.group label="Overview">

            <x-wirekit::sidebar.item
                href="{{ route('dashboard') }}"
                :active="request()->routeIs('dashboard')"
                icon="home"
                wire:navigate
            >
                Dashboard
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>


        <x-wirekit::sidebar.group label="Access Management">

            <x-wirekit::sidebar.item
                href="#"
                icon="users"
                wire:navigate
            >
                Users
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item
                href="#"
                icon="shield-check"
                wire:navigate
            >
                Roles
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item
                href="#"
                icon="key"
                wire:navigate
            >
                Permissions
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>


        <x-wirekit::sidebar.group label="Human Resources">

            <x-wirekit::sidebar.item
                href="#"
                icon="user-group"
                wire:navigate
            >
                Employees
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item
                href="#"
                icon="clock"
                wire:navigate
            >
                Attendance
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item
                href="#"
                icon="chart-bar"
                wire:navigate
            >
                Performance
            </x-wirekit::sidebar.item>

            <x-wirekit::sidebar.item
                href="#"
                icon="academic-cap"
                wire:navigate
            >
                Training & TNA
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>


        <x-wirekit::sidebar.group label="System">

            <x-wirekit::sidebar.item
                href="#"
                icon="cog-6-tooth"
                wire:navigate
            >
                Settings
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>


        <livewire:components.main.btn-logout/>
    </x-wirekit::sidebar>

</x-slot:sidebar>