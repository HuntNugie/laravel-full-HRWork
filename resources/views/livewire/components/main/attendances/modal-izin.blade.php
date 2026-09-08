<x-wirekit::modal name="create-absence-request">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">
            <h2 class="text-lg font-semibold text-slate-900">
                Ajukan Izin / Sakit
            </h2>

            <p class="text-sm text-slate-500">
                Ajukan ketidakhadiran untuk hari ini.
            </p>
        </x-wirekit::stack>
    </x-wirekit::modal.header>

    {{-- BODY --}}
    <x-wirekit::form wire:submit='submit'>
        <x-wirekit::modal.body>
            <x-wirekit::stack gap="md">

                <p class="text-sm text-slate-500">
                    Pengajuan ini untuk tanggal {{ now()->translatedFormat('l, d F Y') }}.
                </p>

                {{-- JENIS --}}
                <x-wirekit::select label="Jenis" wire:model='type'>
                    <option value="">Pilih jenis</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                </x-wirekit::select>


                {{-- ALASAN --}}
                <x-wirekit::textarea label="Alasan" placeholder="Jelaskan alasan izin atau sakit Anda..." rows="4"
                    wire:model='reason' />

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
                        Ajukan
                    </span>
                    <span wire:loading wire:target='submit'>
                        mohon tunggu sebentar
                    </span>
                </x-wirekit::button>
            </x-wirekit::row>
        </x-wirekit::modal.footer>
    </x-wirekit::form>
</x-wirekit::modal>
