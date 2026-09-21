<x-slot:sidebar>

    <x-wirekit::sidebar>

        <x-wirekit::sidebar.group label="Overview">

            <x-wirekit::sidebar.item href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home" wire:navigate>
                Dashboard
            </x-wirekit::sidebar.item>

        </x-wirekit::sidebar.group>


        @canany(['view-attendance', 'view-leave', 'view-data-my'])
            <x-wirekit::sidebar.group label="Layanan Karyawan">

                @can('view-attendance')
                    <x-wirekit::sidebar.item href="{{ route('attendance.view') }}" :active="request()->routeIs('attendance.view')" icon="finger-print"
                        wire:navigate>
                        Presensi
                    </x-wirekit::sidebar.item>
                @endcan

                @can('view-leave')
                    <x-wirekit::sidebar.item href="{{ route('leave.view') }}" :active="request()->routeIs('leave.view')" icon="book" wire:navigate>
                        Pengajuan cuti
                    </x-wirekit::sidebar.item>
                @endcan

                @can('view-data-my')
                    <x-wirekit::sidebar.item href="{{ route('my-data') }}" :active="request()->routeIs('my-data')" icon="user" wire:navigate>
                        Data saya
                    </x-wirekit::sidebar.item>
                @endcan
                @can('view-contract-my')
                    <x-wirekit::sidebar.item href="{{ route('my-contract') }}" :active="request()->routeIs('my-contract')" icon="archive" wire:navigate>
                        Kontrak saya
                    </x-wirekit::sidebar.item>
                @endcan



            </x-wirekit::sidebar.group>
        @endcanany


        @canany(['view-work-time', 'view-holiday'])
            <x-wirekit::sidebar.group collapsible label="Jadwal kerja">

                @can('view-work-time')
                    <x-wirekit::sidebar.item href="{{ route('time.view') }}" :active="request()->routeIs('time.view')" icon="clock" wire:navigate>
                        Waktu Kerja
                    </x-wirekit::sidebar.item>
                @endcan
                @can('view-holiday')
                    <x-wirekit::sidebar.item href="{{ route('holiday.view') }}" :active="request()->routeIs('holiday.view')" icon="sun" wire:navigate>
                        Hari Libur
                    </x-wirekit::sidebar.item>
                @endcan

            </x-wirekit::sidebar.group>
        @endcanany


        @canany(['view-user', 'view-role'])
            <x-wirekit::sidebar.group collapsible label="User dan hak akses">
                @can('view-user')
                    <x-wirekit::sidebar.item href="{{ route('user.view') }}" icon="user" :active="request()->routeIs('user.view')" wire:navigate>
                        Manajemen User
                    </x-wirekit::sidebar.item>
                @endcan


                @can('view-role')
                    <x-wirekit::sidebar.item href="{{ route('role.view') }}" icon="shield-check" :active="request()->routeIs('role.view')"
                        wire:navigate>
                        Manajemen Role
                    </x-wirekit::sidebar.item>
                @endcan



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
                    <x-wirekit::sidebar.item href="{{ route('team.view') }}" icon="user-group" :active="request()->routeIs('team.view')"
                        wire:navigate>
                        Manajemen Team
                    </x-wirekit::sidebar.item>
                @endcan


                @can('view-position')
                    <x-wirekit::sidebar.item href="{{ route('position.view') }}" :active="request()->routeIs('position.view')" icon="badge"
                        wire:navigate>
                        Manajemen Jabatan
                    </x-wirekit::sidebar.item>
                @endcan



            </x-wirekit::sidebar.group>
        @endcanany

        @canany(['view-employee', 'view-contract'])
            <x-wirekit::sidebar.group collapsible label="SDM">

                @can('view-employee')
                    <x-wirekit::sidebar.item href="{{ route('employee.view') }}" :active="request()->routeIs('employee.view')" icon="users"
                        wire:navigate>
                        Manajemen Karyawan
                    </x-wirekit::sidebar.item>
                @endcan

                @can('view-contract')
                    <x-wirekit::sidebar.item href="{{ route('contract.view') }}" :active="request()->routeIs('contract.view')" icon="file-text"
                        wire:navigate>
                        Manajemen kontrak
                    </x-wirekit::sidebar.item>
                @endcan




                {{-- <x-wirekit::sidebar.item href="#" icon="academic-cap" wire:navigate>
                Training & TNA
            </x-wirekit::sidebar.item> --}}



            </x-wirekit::sidebar.group>
        @endcanany
        @canany(['view-monitor-attendance', 'history-attendance '])

            <x-wirekit::sidebar.group collapsible label="Presensi">


                @can('view-monitor-attendance')
                    <x-wirekit::sidebar.item href="{{ route('attendance.monitor.view') }}" :active="request()->routeIs('attendance.monitor.view')" icon="star"
                        wire:navigate>
                        Monitoring Presensi
                    </x-wirekit::sidebar.item>
                @endcan
                @can('history-attendance')
                    <x-wirekit::sidebar.item href="{{ route('attendance.history.view') }}" :active="request()->routeIs('attendance.history.view')" icon="calendar"
                        wire:navigate>
                        Riwayat Presensi
                    </x-wirekit::sidebar.item>
                @endcan


                {{-- <x-wirekit::sidebar.item href="#" icon="academic-cap" wire:navigate>
                Training & TNA
            </x-wirekit::sidebar.item> --}}



            </x-wirekit::sidebar.group>
        @endcanany

        @canany(['view-manage-absence', 'view-type-leave'])

            <x-wirekit::sidebar.group collapsible label="Cuti dan izin">


                @can('view-manage-absence')
                    <x-wirekit::sidebar.item href="{{ route('absence.view') }}" :active="request()->routeIs('absence.view')" icon="cloud"
                        wire:navigate>
                        Pengajuan izin dan sakit
                    </x-wirekit::sidebar.item>
                @endcan
                @can('view-type-leave')
                    <x-wirekit::sidebar.item href="{{ route('leave.type.view') }}" :active="request()->routeIs('leave.type.view')" icon="tag"
                        wire:navigate>
                        Jenis cuti
                    </x-wirekit::sidebar.item>
                @endcan

                @can('view-management-leave')
                    <x-wirekit::sidebar.item href="{{ route('leave.manage.view') }}" :active="request()->routeIs('leave.manage].view')" icon="book"
                        wire:navigate>
                        Manajemen cuti
                    </x-wirekit::sidebar.item>
                @endcan


                {{-- <x-wirekit::sidebar.item href="#" icon="academic-cap" wire:navigate>
                Training & TNA
            </x-wirekit::sidebar.item> --}}



            </x-wirekit::sidebar.group>
        @endcanany
        @canany(['view-benefit', 'view-payroll'])

            <x-wirekit::sidebar.group collapsible label="Kompensasi">
                @can('view-payroll')
                    <x-wirekit::sidebar.item href="{{ route('payroll.view') }}" :active="request()->routeIs('payroll.view')" icon="credit-card"
                        wire:navigate>
                        Penggajian
                    </x-wirekit::sidebar.item>
                @endcan
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
        @canany(['view-late-discipline-rule', 'view-unpresent-discipline-rule'])

            <x-wirekit::sidebar.group collapsible label="Disiplin">
                @can('view-late-discipline-rule')
                    <x-wirekit::sidebar.item href="{{ route('discipline.late.view') }}" :active="request()->routeIs('discipline.late.view')" icon="warning"
                        wire:navigate>
                        Aturan keterlambatan
                    </x-wirekit::sidebar.item>
                @endcan
                @can('view-unpresent-discipline-rule')
                    <x-wirekit::sidebar.item href="{{ route('discipline.unpresent.view') }}" :active="request()->routeIs('discipline.unpresent.view')" icon="warning"
                        wire:navigate>
                        Aturan tidak hadir
                    </x-wirekit::sidebar.item>
                @endcan
                @can('view-warning-letter')
                    <x-wirekit::sidebar.item href="{{ route('discipline.warning-letter.view') }}" :active="request()->routeIs('discipline.warning-letter.view')"
                        icon="legal" wire:navigate>
                        Surat peringatan
                    </x-wirekit::sidebar.item>
                @endcan


                {{-- <x-wirekit::sidebar.item href="#" icon="academic-cap" wire:navigate>
                Training & TNA
            </x-wirekit::sidebar.item> --}}



            </x-wirekit::sidebar.group>
        @endcanany




    </x-wirekit::sidebar>

</x-slot:sidebar>
