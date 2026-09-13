<x-wirekit::modal name="edit-setting">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">
            <h2 class="text-lg font-semibold text-slate-900">
                Setting toleransi menit
            </h2>
        </x-wirekit::stack>
    </x-wirekit::modal.header>

    {{-- BODY --}}
    <x-wirekit::form wire:submit='submit'>
        <x-wirekit::modal.body>
            <x-wirekit::stack gap="md">

                <p class="text-sm text-slate-500">
                    Setting Pengajuan ini untuk tanggal {{ now()->translatedFormat('l, d F Y') }}.
                </p>

                {{-- JENIS --}}
                <x-wirekit::input label="tolaransi menit" name="tolerance" type="numeric" wire:model='tolerance' />

            </x-wirekit::stack>
        </x-wirekit::modal.body>

        {{-- FOOTER --}}
        <x-wirekit::modal.footer>
            <x-wirekit::row justify="end" gap="sm">
                <x-wirekit::modal.close>
                    <x-wirekit::button variant="outline" type="button">
                        Batal
                    </x-wirekit::button>
                </x-wirekit::modal.close>

                <x-wirekit::button type="submit">
                    <span wire:loading.remove wire:target='submit'>
                        Submit
                    </span>
                    <span wire:loading wire:target='submit'>
                        mohon tunggu sebentar
                    </span>
                </x-wirekit::button>
            </x-wirekit::row>
        </x-wirekit::modal.footer>
    </x-wirekit::form>
</x-wirekit::modal>
