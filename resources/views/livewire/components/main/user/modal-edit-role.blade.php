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
                            Budi Santoso
                        </p>

                        <p class="mt-0.5 truncate text-xs text-slate-500">
                            budi@inovindo.com
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

                        <x-wirekit::checkbox label="Employee" value="employee" wire:model="roles" />

                        <x-wirekit::checkbox label="HR" value="hr" wire:model="roles" />

                        <x-wirekit::checkbox label="Manager" value="manager" wire:model="roles" />

                        <x-wirekit::checkbox label="Administrator" value="administrator" wire:model="roles" />

                        <x-wirekit::checkbox label="Staff HR" value="staff-hr" wire:model="roles" />

                        <x-wirekit::checkbox label="Supervisor" value="supervisor" wire:model="roles" />

                        <x-wirekit::checkbox label="Finance" value="finance" wire:model="roles" />

                    </x-wirekit::stack>

                </div>

            </x-wirekit::stack>


            {{-- INFO --}}
            <div class="rounded-xl border border-[#92EEFF]/50 bg-[#92EEFF]/20 p-4">

                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                    Role Aktif
                </p>

                <p class="mt-1 text-sm font-medium text-slate-900">
                    Employee, HR
                </p>

            </div>

        </x-wirekit::stack>

    </x-wirekit::modal.body>


    {{-- FOOTER --}}
    <x-wirekit::modal.footer>

        <x-wirekit::row justify="end" gap="sm">

            <x-wirekit::button type="button" variant="outline">
                Batal
            </x-wirekit::button>

            <x-wirekit::button type="button">
                Simpan Perubahan
            </x-wirekit::button>

        </x-wirekit::row>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
