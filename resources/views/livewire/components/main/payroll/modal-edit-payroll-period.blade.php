<x-wirekit::modal name="edit-payroll-period" wire:model="show" size="lg">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    <x-wirekit::modal.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Edit Periode Payroll
            </h2>

            <p class="text-sm text-slate-500">
                Perbarui informasi periode payroll selama status masih draft.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    <x-wirekit::modal.body>

        <form wire:submit="save">

            <x-wirekit::stack gap="md">

                @error('form')
                    <div class="rounded-xl border border-red-100 bg-red-50 p-3">

                        <p class="text-sm text-red-700">
                            {{ $message }}
                        </p>

                    </div>
                @enderror


                {{-- Name --}}

                <x-wirekit::input class="text-black" label="Nama Periode" name="name" wire:model.live="name"
                    placeholder="Contoh: September 2026" />


                {{-- Dates --}}

                <div class="grid gap-5 md:grid-cols-2">

                    <x-wirekit::input class="text-black" label="Tanggal Mulai" name="startDate" type="date"
                        wire:model.live="startDate" />


                    <x-wirekit::input class="text-black" label="Tanggal Selesai" name="endDate" type="date"
                        wire:model.live="endDate" />

                </div>


                {{-- Payment Date --}}

                <x-wirekit::input class="text-black" label="Tanggal Pembayaran" name="paymentDate" type="date"
                    wire:model.live="paymentDate" />


                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">

                            <x-wirekit::icon name="information-circle" class="size-5 text-slate-500" />

                        </div>

                        <p class="text-sm leading-6 text-slate-600">
                            Mengubah tanggal periode setelah payroll dibuat
                            dapat memengaruhi dasar perhitungan payroll.
                            Pastikan periode masih benar sebelum diproses.
                        </p>

                    </div>

                </div>

            </x-wirekit::stack>

        </form>

    </x-wirekit::modal.body>


    <x-wirekit::modal.footer>

        <x-wirekit::modal.close>

            <x-wirekit::button type="button" variant="outline">
                Batal
            </x-wirekit::button>

        </x-wirekit::modal.close>


        <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500" wire:click="save">
            <span wire:loading.remove wire:target='save'>
                Simpan Perubahan
            </span>
            <span wire:loading wire:target='save'>
                <x-wirekit::spinner />
                Tunggu sebentar
            </span>
        </x-wirekit::button>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
