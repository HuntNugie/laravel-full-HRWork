    <x-wirekit::modal name="add-holiday">

        <x-slot:trigger>

            {{ $slot }}
        </x-slot:trigger>


        <x-wirekit::modal.header>

            <x-wirekit::stack gap="xs">

                <h2 class="text-lg font-semibold text-slate-900">
                    Tambah Hari Libur
                </h2>

                <p class="text-sm text-slate-500">
                    Tambahkan tanggal yang ditetapkan sebagai hari libur.
                </p>

            </x-wirekit::stack>

        </x-wirekit::modal.header>


        <x-wirekit::form wire:submit="store">
            <x-wirekit::modal.body>

                <x-wirekit::stack gap="md">

                    <div class="max-w-[12rem]">

                        <x-wirekit::date-picker label="Tanggal" name="date" wire:model.live="date" />

                    </div>


                    <x-wirekit::input label="Nama Hari Libur" name="name" wire:model.live.debounce.400ms="name"
                        placeholder="Masukan nama event" />


                    <x-wirekit::textarea label="Keterangan" name="desc" placeholder="Tambahkan keterangan..."
                        wire:model.live.debounce.400ms="desc"></x-wirekit::textarea>

                </x-wirekit::stack>

            </x-wirekit::modal.body>


            <x-wirekit::modal.footer>

                <x-wirekit::row justify="end" gap="sm">

                    <x-wirekit::modal.close>
                        <x-wirekit::button variant="ghost">
                            Batal
                        </x-wirekit::button>
                    </x-wirekit::modal.close>

                    <x-wirekit::button type="submit">
                        Simpan Hari Libur
                    </x-wirekit::button>

                </x-wirekit::row>

            </x-wirekit::modal.footer>
        </x-wirekit::form>

    </x-wirekit::modal>
