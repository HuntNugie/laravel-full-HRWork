<x-wirekit::modal name="add-team">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                {{ $employee?->team ? 'Edit' : 'Tambahkan' }} Team
            </h2>

            <p class="text-sm text-slate-500">
                Pilih team untuk menugaskan karyawan ini.
            </p>

        </x-wirekit::stack>
    </x-wirekit::modal.header>


    {{-- BODY --}}
    <x-wirekit::form wire:submit='changeTeam'>

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- TEAM --}}
                <x-wirekit::select label="Team" wire:model.live='selectTeams'>
                    <option value="">Pilih team</option>
                    @foreach ($teams as $id => $team)
                        <option value="{{ $id }}">
                            {{ $team }}
                        </option>
                    @endforeach
                </x-wirekit::select>


                {{-- SUPERVISOR INFORMATION --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Supervisor
                    </p>

                    <div class="mt-2 flex items-center gap-3">

                        <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-white">
                            <span class="text-xs font-semibold text-slate-500">
                                BS
                            </span>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-slate-900">
                                {{ $supervisor?->user?->name ?? 'Belum ada teams di pilih' }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $supervisor?->position?->name ?? 'Tidak ada jabatan' }}
                            </p>
                        </div>

                    </div>

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
                    {{ $employee?->team ? 'Edit' : 'Tambahkan' }} Team
                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>
    </x-wirekit::form>

</x-wirekit::modal>
