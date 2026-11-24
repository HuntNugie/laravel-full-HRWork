<x-wirekit::stack gap="md">

    {{-- =========================================================
        PAGE HEADING
    ========================================================== --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                Role & Sistem Akses
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Manajemen Akun HRD
            </h1>

            <p class="text-sm text-slate-500">
                Kelola akun, status, hak akses, dan keamanan HRD dalam sistem HRWork.
            </p>

        </x-wirekit::stack>


        @can('create-user')
            <x-wirekit::modal name="create-hr-account">
                <x-slot:trigger>
                    <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                        <x-wirekit::icon name="plus" /> Tambah Akun HRD
                    </x-wirekit::button>
                </x-slot:trigger>
                <x-wirekit::modal.header>
                    <x-wirekit::stack gap="1">
                        <h2 class="text-lg font-semibold text-slate-900">Tambah Akun HRD</h2>
                        <p class="text-sm text-slate-500">Buat akun HRD baru. Hak akses dapat diatur setelah akun dibuat.</p>
                    </x-wirekit::stack>
                </x-wirekit::modal.header>
                <x-wirekit::form wire:submit="createAccount">
                    <x-wirekit::modal.body>
                        <x-wirekit::stack gap="md">
                            <x-wirekit::input label="Nama Lengkap" name="name" wire:model="name" placeholder="Nama HRD" />
                            <x-wirekit::input label="Email" type="email" name="email" wire:model="email" placeholder="hrd@perusahaan.com" />
                            <x-wirekit::password-input label="Password" name="password" wire:model="password" :strength-meter="true" hint="Minimal 12 karakter." />
                            <x-wirekit::password-input label="Konfirmasi Password" name="password_confirmation" wire:model="password_confirmation" />
                            <div class="rounded-xl border border-sky-200 bg-sky-50 p-3 text-sm text-sky-700">
                                <x-wirekit::icon name="shield-check" class="size-4" /> Role HRD ditetapkan otomatis. Akun baru belum memiliki permission operasional.
                            </div>
                        </x-wirekit::stack>
                    </x-wirekit::modal.body>
                    <x-wirekit::modal.footer>
                        <x-wirekit::row justify="end" gap="sm">
                            <x-wirekit::modal.close><x-wirekit::button type="button" intent="neutral" surface="outline">Batal</x-wirekit::button></x-wirekit::modal.close>
                            <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="createAccount">
                                <span wire:loading.remove wire:target="createAccount">Tambah Akun</span>
                                <span wire:loading wire:target="createAccount"><x-wirekit::spinner /> Menyimpan...</span>
                            </x-wirekit::button>
                        </x-wirekit::row>
                    </x-wirekit::modal.footer>
                </x-wirekit::form>
            </x-wirekit::modal>
        @endcan

    </div>


    {{-- =========================================================
        SUMMARY
    ========================================================== --}}

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Total Users --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Total Users
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#30AFFF]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        {{ $this->totalUser() }}
                    </span>

                    <span class="text-xs text-slate-400">
                        Akun terdaftar
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Active --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Active Accounts
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#C4F7CA]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        {{ $this->totalActive }}
                    </span>

                    <span class="text-xs text-emerald-600">
                        Akun aktif
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Pending --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Pending Activation
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#FFA239]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        {{ $this->totalPending }}
                    </span>

                    <span class="text-xs text-amber-600">
                        Menunggu aktivasi
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Inactive --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="3">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Inactive Accounts
                        </span>

                        <span class="h-2.5 w-2.5 rounded-full bg-[#FF5656]"></span>

                    </div>

                    <span class="text-3xl font-bold text-slate-900">
                        {{ $this->totalInactive }}
                    </span>

                    <span class="text-xs text-red-600">
                        Akun tidak aktif
                    </span>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        USER MANAGEMENT
    ========================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        User Accounts
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar akun HRD yang terdaftar dalam sistem.
                    </p>

                </x-wirekit::stack>


                <div class="grid w-full gap-3 sm:grid-cols-[minmax(0,1fr)_16rem_auto] xl:max-w-3xl">

                    <x-wirekit::input
                        placeholder="Cari nama atau email HRD"
                        name="search"
                        wire:model.live.debounce.400ms="search"
                        class="text-black"
                    />

                    <x-wirekit::select
                        name="statusFilter"
                        label="Status"
                        hideLabel
                        placeholder="Semua status"
                        :options="['' => 'Semua status', 'active' => 'Active', 'pending' => 'Pending', 'inactive' => 'Inactive']"
                        wire:model.live="statusFilter"
                    />

                    <x-wirekit::button
                        type="button"
                        intent="neutral"
                        surface="outline"
                        wire:click="resetFilters"
                    >
                        Reset
                    </x-wirekit::button>

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="name">
                                User
                            </x-wirekit::table.th>

                            

                            <x-wirekit::table.th>
                                Roles
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="status">
                                Account Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="updated_at">
                                Last Updated
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($users as $user)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-sky-100">

                                            @if ($user->getFirstMediaUrl('avatar'))
                                                <img src="{{ $user->getFirstMediaUrl('avatar') }}"
                                                    alt="gambar dari {{ $user->name }}"
                                                    class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                                            @else
                                                <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                                    class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                                            @endif

                                        </div>

                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-800">
                                                {{ $user->name }}
                                            </p>

                                            <p class="truncate text-xs text-slate-400">
                                                {{ $user->email }}
                                            </p>

                                        </div>

                                    </div>

                                </x-wirekit::table.td>


                                


                                <x-wirekit::table.td>

                                    <div class="flex flex-wrap gap-1.5">

                                        @if ($user->getRoleNames()->isNotEmpty())
                                            @foreach ($user->getRoleNames() as $role)
                                                <span @class([
                                                    'rounded-full px-2.5 py-1 text-xs font-medium',
                                                    'text-olive-600 bg-olive-100' => $role === 'employee',
                                                    'text-purple-600 bg-purple-100' => $role === 'super-admin',
                                                    'text-red-600 bg-red-100' => $role === 'hr',
                                                    'text-blue-600 bg-blue-100' => $role === 'administrator',
                                                ])>
                                                    {{ $role }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span
                                                class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-500">
                                                Belum ada role
                                            </span>
                                        @endif

                                    </div>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span @class([
                                        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium',
                                        'bg-emerald-50 text-emerald-600' => $user->status === 'active',
                                        'bg-yellow-50 text-yellow-600' => $user->status === 'pending',
                                        'bg-red-50 text-red-600' => $user->status === 'inactive',
                                    ])>
                                        {{ $user->status }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-500">
                                        {{ $user->updated_at->diffForHumans() }}
                                    </span>

                                </x-wirekit::table.td>


                                <x-wirekit::table.td>

                                    <x-wirekit::button
                                        type="button"
                                        href="{{ route('user.show', $user->id) }}"
                                        wire:navigate
                                        class="px-3 py-1.5 text-xs"
                                    >
                                        Detail
                                    </x-wirekit::button>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty
                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="5" class="py-10 text-center">

                                    <span class="text-sm text-slate-500">
                                        Tidak ada akun HRD yang ditemukan.
                                    </span>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

                {{ $users->links() }}

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
