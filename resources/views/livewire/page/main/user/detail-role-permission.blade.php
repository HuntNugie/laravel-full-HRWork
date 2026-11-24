<x-wirekit::stack gap="md">

    {{-- PAGE HEADING --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('user.show', $user->id) }}" wire:navigate
            class="inline-flex w-fit items-center text-sm font-medium
                   text-slate-500 transition hover:text-[#30AFFF]">
            ← Kembali ke Users
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Pengguna
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Hak Akses HRD
        </h1>

        <p class="text-sm text-slate-500">
            Atur permission operasional untuk akun HRD ini.
        </p>

    </x-wirekit::stack>


    {{-- USER INFORMATION --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                {{-- USER --}}
                <div class="flex items-center gap-4">

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

                        <div class="flex flex-wrap items-center gap-2">

                            <h2 class="text-xl font-semibold text-slate-900">
                                {{ $user->name }}
                            </h2>

                            <span @class(['rounded-full px-2.5 py-1 text-xs font-medium', 'bg-emerald-50 text-emerald-600' => $user->status === 'active', 'bg-red-50 text-red-600' => $user->status === 'inactive', 'bg-yellow-50 text-yellow-600' => $user->status === 'pending'])>{{ $user->status }}</span>

                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $user->email }}
                        </p>

                    </div>

                </div>


                {{-- SUMMARY --}}
                <div class="flex items-center gap-8">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Roles
                        </p>

                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            {{ count($user->roles) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Permissions
                        </p>

                        <p class="mt-1 text-lg font-semibold text-slate-900">
                            {{ $user->getAllPermissions()->count() }}
                        </p>
                    </div>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    <x-wirekit::form wire:submit="save">
        <x-wirekit::stack gap="md">
            <x-wirekit::card>
                <x-wirekit::card.header>
                    <x-wirekit::row align="center" justify="between" class="flex-wrap gap-3">
                        <x-wirekit::stack gap="1">
                            <h2 class="text-lg font-semibold text-slate-900">Role & Permission</h2>
                            <p class="text-sm text-slate-500">Role HRD tetap. Pilih hak akses sesuai tanggung jawab pengguna.</p>
                        </x-wirekit::stack>
                        <x-wirekit::row gap="sm">
                            <x-wirekit::button type="button" size="sm" intent="neutral" surface="outline" wire:click="clearPermissions">Kosongkan</x-wirekit::button>
                            <x-wirekit::button type="button" size="sm" intent="neutral" surface="outline" wire:click="selectAllPermissions">Pilih Semua</x-wirekit::button>
                        </x-wirekit::row>
                    </x-wirekit::row>
                </x-wirekit::card.header>
                <x-wirekit::card.body>
                    <x-wirekit::stack gap="xl">
                        @foreach ($groups as $group => $items)
                            <x-wirekit::stack gap="md" wire:key="permission-group-{{ $loop->index }}">
                                <x-wirekit::row align="center" justify="between">
                                    <x-wirekit::row align="center" gap="sm">
                                        <div class="flex size-10 items-center justify-center rounded-xl bg-sky-100">
                                            <x-wirekit::icon name="shield-check" class="size-5 text-sky-600" />
                                        </div>
                                        <x-wirekit::stack gap="1">
                                            <h3 class="text-sm font-semibold text-slate-900">{{ $group }}</h3>
                                            <span class="text-xs text-slate-500">{{ count($items) }} permissions</span>
                                        </x-wirekit::stack>
                                    </x-wirekit::row>
                                    <x-wirekit::button type="button" size="sm" intent="neutral" surface="outline" wire:click="selectGroup(@js($group))">Pilih grup</x-wirekit::button>
                                </x-wirekit::row>
                                <div class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($items as $permission)
                                        <x-wirekit::checkbox :label="str($permission)->replace('-', ' ')->headline()" wire:model="permissions" value="{{ $permission }}" wire:key="permission-{{ $permission }}" />
                                    @endforeach
                                </div>
                            </x-wirekit::stack>
                            @unless($loop->last)<x-wirekit::divider />@endunless
                        @endforeach
                        @error('permissions.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </x-wirekit::stack>
                </x-wirekit::card.body>
            </x-wirekit::card>
            <x-wirekit::row justify="end" gap="sm">
                <x-wirekit::button type="button" intent="neutral" surface="outline" href="{{ route('user.show', $user->id) }}" wire:navigate>Batal</x-wirekit::button>
                <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Simpan Hak Akses</span>
                    <span wire:loading wire:target="save"><x-wirekit::spinner /> Menyimpan...</span>
                </x-wirekit::button>
            </x-wirekit::row>
        </x-wirekit::stack>
    </x-wirekit::form>
</x-wirekit::stack>
