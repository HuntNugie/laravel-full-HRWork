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
                Ubah password untuk akun {{ $user->name }}.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>


    <x-wirekit::form wire:submit='changePassword'>
        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- PASSWORD BARU --}}
                <x-wirekit::password-input name="newPassword" label="Password Baru"
                    wire:model.live.debounce.400ms='newPassword' />

                {{-- KONFIRMASI PASSWORD --}}
                <x-wirekit::password-input type="password" label="Konfirmasi Password" name="password_confirmation"
                    wire:model.live.debounce.400ms='password_confirmation' />


                {{-- PASSWORD INFO --}}
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">

                    <div class="flex gap-2.5">

                        <x-wirekit::icon name="triangle-alert" class="mt-0.5 size-4 shrink-0 text-amber-600" />

                        <p class="text-sm leading-5 text-amber-700">
                            Password baru akan menggantikan password pengguna saat ini.
                        </p>

                    </div>

                </div>

            </x-wirekit::stack>

        </x-wirekit::modal.body>


        <x-wirekit::modal.footer>

            <div class="flex justify-end gap-2">

                <x-wirekit::button type="button" variant="ghost">
                    Batal
                </x-wirekit::button>

                <x-wirekit::button type="submit" :disabled="!$this->canSubmit()">
                    <span wire:loading.remove wire:target='changePassword'>Ubah Password</span>
                    <span wire:loading wire:target='changePassword'>
                        <x-wirekit::spinner />
                        Tunggu sebentar
                    </span>
                </x-wirekit::button>

            </div>

        </x-wirekit::modal.footer>
    </x-wirekit::form>
</x-wirekit::modal>
