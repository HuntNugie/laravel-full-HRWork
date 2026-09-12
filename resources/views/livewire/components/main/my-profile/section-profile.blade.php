<x-wirekit::card>

    <x-wirekit::card.header>

        <x-wirekit::stack gap="sm">

            <h2 class="text-lg font-semibold text-slate-900">
                Informasi Pribadi
            </h2>

            <p class="text-sm text-slate-500">
                Informasi pribadi yang dapat Anda perbarui sendiri.
            </p>

        </x-wirekit::stack>

    </x-wirekit::card.header>


    <x-wirekit::card.body>

        <x-wirekit::form wire:submit='update'>
            <x-wirekit::stack gap="md">

                {{-- NAMA --}}
                <x-wirekit::input label="Nama Lengkap" placeholder="Masukkan nama lengkap" name="name"
                    wire:model.live.debounce.400ms='name' />


                {{-- EMAIL PRIBADI --}}
                <x-wirekit::input type="email" label="Email Pribadi" placeholder="Masukkan email pribadi"
                    value="nugie.personal@email.com" wire:model.live.debounce.400ms='email' disabled readonly />


                {{-- NOMOR TELEPON --}}
                <x-wirekit::input type="tel" label="Nomor Telepon" placeholder="08xxxxxxxxxx"
                    wire:model.live.debounce.400ms='noHp' name="noHp" />


                {{-- JENIS KELAMIN --}}
                <x-wirekit::select label="Jenis Kelamin" placeholder="Pilih jenis kelamin" :options="[
                    'male' => 'Laki-laki',
                    'female' => 'Perempuan',
                ]"
                    wire:model.live='gender' name="gender" />


                <div class="flex justify-end pt-2">

                    <x-wirekit::button type="submit">
                        Simpan Perubahan
                    </x-wirekit::button>

                </div>
            </x-wirekit::stack>
        </x-wirekit::form>

    </x-wirekit::card.body>

</x-wirekit::card>
