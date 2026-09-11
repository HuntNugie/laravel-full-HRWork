<x-wirekit::modal name="edit-role">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Ubah Role
            </h2>

            <p class="text-sm text-slate-500">
                Perbarui role yang dimiliki pengguna ini.
            </p>

        </x-wirekit::stack>
    </x-wirekit::modal.header>


    {{-- BODY --}}
    <x-wirekit::form wire:submit='changeRole'>
        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- USER INFO --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                    <div class="flex items-center gap-3">

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white">
                            <span class="text-xs font-semibold text-slate-500">
                                BS
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $user->name }}
                            </p>

                            <p class="mt-0.5 truncate text-xs text-slate-500">
                                {{ $user->email }}
                            </p>
                        </div>

                    </div>

                </div>


                <x-wirekit::stack gap="sm">

                    <p class="text-sm font-medium text-slate-900">
                        Role
                    </p>

                    <div class="max-h-60 overflow-y-auto rounded-xl border border-slate-200 p-3">

                        <x-wirekit::stack gap="sm">

                            @foreach ($roles as $role)
                                <x-wirekit::checkbox label="{{ $role->name }}" value="{{ $role->name }}"
                                    wire:model="roleName" />
                            @endforeach



                        </x-wirekit::stack>

                    </div>

                </x-wirekit::stack>


                {{-- INFO --}}
                <div class="rounded-xl border border-[#92EEFF]/50 bg-[#92EEFF]/20 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                        Role Aktif
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-900">
                        {{ $user->roles()->pluck('name')->join(' , ') }}
                    </p>

                </div>

            </x-wirekit::stack>

        </x-wirekit::modal.body>


        {{-- FOOTER --}}
        <x-wirekit::modal.footer>

            <x-wirekit::row justify="end" gap="sm">

                <x-wirekit::modal.close>
                    <x-wirekit::button type="button" variant="outline">
                        Batal
                    </x-wirekit::button>
                </x-wirekit::modal.close>

                <x-wirekit::button type="submit" :disabled="!$this->canSubmit()">
                    <span wire:loading.remove wire:target='changeRole'>
                        Simpan Perubahan
                    </span>

                    <span wire:loading wire:target='changeRole'>
                        <x-wirekit::spinner />
                        Tunggu sebentar
                    </span>
                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>
    </x-wirekit::form>
</x-wirekit::modal>
