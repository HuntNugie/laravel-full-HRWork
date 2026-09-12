{{-- CHANGE PASSWORD MODAL --}}
<x-wirekit::modal name="change-password">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="1">

            <h2 class="text-lg font-semibold text-slate-900">
                Ubah Password
            </h2>

            <p class="text-sm text-slate-500">
                Perbarui password akun Anda untuk menjaga keamanan akun.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    <x-wirekit::form wire:submit='save'>
        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- CURRENT PASSWORD --}}
                <x-wirekit::password-input name="oldPassword" label="Password Saat Ini"
                    placeholder="Masukkan password saat ini" autocomplete="current-password" wire:model='oldPassword' />


                {{-- NEW PASSWORD --}}
                <x-wirekit::password-input name="newPassword" label="Password Baru" placeholder="Masukkan password baru"
                    autocomplete="new-password" hint="Gunakan minimal 8 karakter" wire:model='newPassword' />


                {{-- CONFIRM PASSWORD --}}
                <x-wirekit::password-input name="newPassword_confirmation" label="Konfirmasi Password"
                    placeholder="Masukkan kembali password baru" autocomplete="new-password"
                    wire:model='newPassword_confirmation' />


                {{-- WARNING --}}
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">

                    <div class="flex gap-2.5">

                        <x-wirekit::icon name="triangle-alert" class="mt-0.5 size-4 shrink-0 text-amber-600" />

                        <p class="text-sm leading-5 text-amber-700">
                            Setelah password berhasil diubah, gunakan password baru
                            untuk login berikutnya.
                        </p>

                    </div>

                </div>

            </x-wirekit::stack>

        </x-wirekit::modal.body>


        <x-wirekit::modal.footer>

            <div class="flex justify-end gap-2">

                <x-wirekit::modal.close>
                    <x-wirekit::button type="button" variant="ghost" intent='danger'>
                        Batal
                    </x-wirekit::button>
                </x-wirekit::modal.close>

                <x-wirekit::button type="submit">
                    <span wire:loading.remove wire:target='save'>
                        Ubah Password
                    </span>
                    <span wire:loading wire:target='save'>
                        <x-wirekit::spinner />
                        Tunggu sebentar
                    </span>
                </x-wirekit::button>

            </div>

        </x-wirekit::modal.footer>
    </x-wirekit::form>
</x-wirekit::modal>
