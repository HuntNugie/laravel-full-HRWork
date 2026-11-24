<x-slot:sidebar>
    <x-wirekit::sidebar class="max-lg:max-h-[calc(100dvh-4rem)] max-lg:overflow-y-auto max-lg:overscroll-contain">
        <x-wirekit::sidebar.group label="Overview">
            <x-wirekit::sidebar.item href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home" wire:navigate>Dashboard</x-wirekit::sidebar.item>
            @can('view-ai-assistant')
                <x-wirekit::sidebar.item href="{{ route('ai.assistant') }}" :active="request()->routeIs('ai.assistant')" icon="sparkles" wire:navigate>HRWork AI</x-wirekit::sidebar.item>
            @endcan
        </x-wirekit::sidebar.group>
        @role('administrator')
            <x-wirekit::sidebar.group label="Manajemen Sistem">
                <x-wirekit::sidebar.item href="{{ route('user.view') }}" :active="request()->routeIs('user.*')" icon="user" wire:navigate>Akun & Hak Akses HRD</x-wirekit::sidebar.item>
            </x-wirekit::sidebar.group>
        @else
            <x-wirekit::sidebar.group label="Kepegawaian">
                @can('view-employee')
                    <x-wirekit::sidebar.item href="{{ route('employee.view') }}" :active="request()->routeIs('employee.*')" icon="user" wire:navigate>Karyawan</x-wirekit::sidebar.item>
                @endcan
                @can('view-contract')
                    <x-wirekit::sidebar.item href="{{ route('contract.view') }}" :active="request()->routeIs('contract.*')" icon="archive" wire:navigate>Kontrak</x-wirekit::sidebar.item>
                @endcan
            </x-wirekit::sidebar.group>
            <x-wirekit::sidebar.group label="Master Data">
                @can('view-position')
                    <x-wirekit::sidebar.item href="{{ route('position.view') }}" :active="request()->routeIs('position.*')" icon="briefcase" wire:navigate>Jabatan</x-wirekit::sidebar.item>
                @endcan
                @can('view-benefit')
                    <x-wirekit::sidebar.item href="{{ route('benefit.view') }}" :active="request()->routeIs('benefit.*')" icon="banknotes" wire:navigate>Benefit</x-wirekit::sidebar.item>
                @endcan
                @can('view-type-leave')
                    <x-wirekit::sidebar.item href="{{ route('leave.type.view') }}" :active="request()->routeIs('leave.type.*')" icon="book" wire:navigate>Jenis Cuti</x-wirekit::sidebar.item>
                @endcan
            </x-wirekit::sidebar.group>
        @endrole
        <x-wirekit::sidebar.group label="Akun">
            <x-wirekit::sidebar.item href="{{ route('my-profile') }}" :active="request()->routeIs('my-profile')" icon="user" wire:navigate>Profil Saya</x-wirekit::sidebar.item>
        </x-wirekit::sidebar.group>
    </x-wirekit::sidebar>
</x-slot:sidebar>
