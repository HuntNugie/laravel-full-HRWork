<x-wirekit::stack gap="md">

    {{-- =========================================================
        PAGE HEADING
    ========================================================== --}}

    <x-wirekit::stack gap="sm">

        <a href="{{ route('user.view') }}" wire:navigate
            class="inline-flex w-fit items-center text-sm font-medium
                   text-slate-500 transition hover:text-[#30AFFF]">
            ← Kembali ke Akun HRD
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Role & Sistem Akses
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Detail Akun HRD
        </h1>

        <p class="text-sm text-slate-500">
            Informasi akun, status akses, role, dan keamanan user.
        </p>

    </x-wirekit::stack>


    {{-- =========================================================
        ACCOUNT HEADER
    ========================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-4">

                    {{-- Avatar --}}
                    <div
                        class="flex size-14 shrink-0 items-center justify-center
                               rounded-full bg-sky-100">
                        @if ($user->getFirstMediaUrl('avatar'))
                            <img src="{{ $user->getFirstMediaUrl('avatar') }}" alt="gambar dari {{ $user->name }}"
                                class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                        @else
                            <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                        @endif
                    </div>

                    <div class="min-w-0">

                        <h2 class="truncate text-xl font-semibold text-slate-900">
                            {{ $user->name }}
                        </h2>

                        <p class="mt-1 truncate text-sm text-slate-500">
                            {{ $user->email }}
                        </p>

                        <div class="mt-2 flex flex-wrap items-center gap-2">

                            <span @class([
                                "inline-flex items-center rounded-full
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    px-2.5 py-1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   text-xs font-medium ",
                                'bg-emerald-50 text-emerald-600' => $user->status === 'active',
                                'bg-yellow-50 text-yellow-600' => $user->status === 'pending',
                                'bg-red-50 text-red-600' => $user->status === 'inactive',
                            ])>
                                {{ $user->status }}
                            </span>

                            <span
                                class="inline-flex items-center rounded-full
                                       bg-slate-100 px-2.5 py-1
                                       text-xs font-medium text-slate-600">
                                HRD
                            </span>

                        </div>

                    </div>

                </div>


                {{-- Account Action --}}
                <div class="flex shrink-0">




                    @can('change-user-status')
                    <x-wirekit::button type="button" wire:click="actionStatusAccount" wire:confirm="Ubah status akun HRD ini?" wire:loading.attr="disabled" wire:target="actionStatusAccount" @class([
                        'text-white ',
                        'bg-green-500 hover:bg-green-600' =>
                            $user->status === 'inactive' || $user->status === 'pending',
                        'bg-red-500 hover:bg-red-600' => $user->status === 'active',
                    ])>
                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }} Account
                    </x-wirekit::button>
                    @endcan
                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =========================================================
        ACCOUNT + ROLES
    ========================================================== --}}

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Account Information --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Account Information
                    </h2>

                    <div class="flex flex-wrap items-start justify-between gap-3"><p class="text-sm text-slate-500">Informasi dasar akun HRD.</p>
                    @can('update-user')
                        <x-wirekit::modal name="edit-hr-account">
                            <x-slot:trigger>
                                <x-wirekit::button type="button" size="sm" intent="neutral" surface="outline"><x-wirekit::icon name="pencil" /> Edit Akun</x-wirekit::button>
                            </x-slot:trigger>
                            <x-wirekit::modal.header>
                                <x-wirekit::stack gap="1">
                                    <h2 class="text-lg font-semibold text-slate-900">Edit Akun HRD</h2>
                                    <p class="text-sm text-slate-500">Perbarui nama dan email akun HRD.</p>
                                </x-wirekit::stack>
                            </x-wirekit::modal.header>
                            <x-wirekit::form wire:submit="save">
                                <x-wirekit::modal.body>
                                    <x-wirekit::stack gap="md">
                                        <x-wirekit::input label="Nama Lengkap" name="name" wire:model="name" />
                                        <x-wirekit::input type="email" label="Email" name="email" wire:model="email" />
                                    </x-wirekit::stack>
                                </x-wirekit::modal.body>
                                <x-wirekit::modal.footer>
                                    <x-wirekit::row justify="end" gap="sm">
                                        <x-wirekit::modal.close><x-wirekit::button type="button" intent="neutral" surface="outline">Batal</x-wirekit::button></x-wirekit::modal.close>
                                        <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="save">
                                            <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                                            <span wire:loading wire:target="save"><x-wirekit::spinner /> Menyimpan...</span>
                                        </x-wirekit::button>
                                    </x-wirekit::row>
                                </x-wirekit::modal.footer>
                            </x-wirekit::form>
                        </x-wirekit::modal>
                    @endcan
</div>
</x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Name
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $user->name }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Email
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $user->email }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Account Status
                        </p>
                        <span @class([
                            "inline-flex items-center rounded-full
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            px-2.5 py-1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           text-xs font-medium ",
                            'bg-emerald-50 text-emerald-600' => $user->status === 'active',
                            'bg-yellow-50 text-yellow-600' => $user->status === 'pending',
                            'bg-red-50 text-red-600' => $user->status === 'inactive',
                        ])>
                            {{ $user->status }}
                        </span>


                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Created At
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $user->created_at->format('d F Y') }}
                        </p>

                    </div>




                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Roles --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Roles
                        </h2>

                        <p class="text-sm text-slate-500">
                            Role tetap dan hak akses operasional HRD.
                        </p>

                    </x-wirekit::stack>


                    


                </div>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    {{-- Base Role --}}
                    <div>

                        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-slate-400">
                            Role
                        </p>

                        @foreach ($user->roles as $role)
                            <span
                                class="inline-flex items-center rounded-full
                                    px-3 py-1.5
                                   text-xs font-medium text-slate-700 bg-slate-100 ">
                                {{ $role->name }}
                            </span>
                        @endforeach


                    </div>





                    {{-- Permission Info --}}
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <p class="text-sm font-medium text-slate-800">
                                    Hak akses diberikan secara individual kepada akun HRD ini.
                                </p>

                            </div>

                            @can('manage-hr-access')
<x-wirekit::button type="button" size="sm"
                                href="{{ route('user.role.show', $user->id) }}" wire:navigate>
                                Atur Hak Akses
                            </x-wirekit::button>
@endcan

                        </div>

                    </div>

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        EMPLOYEE + SECURITY
    ========================================================== --}}

    <div class="grid gap-6 lg:grid-cols-2">
        <x-wirekit::card>

            <x-wirekit::card.header>

                <div class="flex items-center justify-between gap-4">

                    <x-wirekit::stack gap="1">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Security
                        </h2>

                        <p class="text-sm text-slate-500">
                            Pengaturan keamanan akun user.
                        </p>

                    </x-wirekit::stack>

                </div>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">

                    {{-- Password --}}
                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <p class="text-sm font-medium text-slate-800">
                                Password
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Kelola kata sandi akun HRD.
                            </p>

                        </div>

                        @can('reset-user-password')
                            <livewire:components.main.user.modal-change-password :user="$user">
                                <x-wirekit::button type="button" size="sm" intent="danger">
                                    Change Password
                                </x-wirekit::button>
                            </livewire:components.main.user.modal-change-password>
                        @endcan

                    </div>



                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =========================================================
        ACCOUNT STATUS INFORMATION
    ========================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-800">
                        Account Status
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        {{ $user->status === 'active' ? 'Akun aktif dan dapat digunakan untuk login ke sistem HRWork.' : 'Akun belum dapat digunakan untuk login ke sistem HRWork.' }}
                    </p>

                </div>

                <span @class([
                    "inline-flex items-center rounded-full
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            px-2.5 py-1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           text-xs font-medium ",
                    'bg-emerald-50 text-emerald-600' => $user->status === 'active',
                    'bg-yellow-50 text-yellow-600' => $user->status === 'pending',
                    'bg-red-50 text-red-600' => $user->status === 'inactive',
                ])>
                    {{ $user->status }}
                </span>


            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
