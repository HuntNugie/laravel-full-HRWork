<x-wirekit::modal name="edit-photo">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>


    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Edit Foto Profil
            </h2>

            <p class="text-sm text-slate-500">
                Perbarui foto profil karyawan.
            </p>

        </x-wirekit::stack>
    </x-wirekit::modal.header>


    {{-- BODY --}}
    <x-wirekit::modal.body>

        <x-wirekit::stack gap="md">

            {{-- FOTO --}}
            <div class="flex flex-col items-center gap-4">

                <div
                    class="flex size-32 items-center justify-center overflow-hidden rounded-full
                    bg-slate-100 ring-4 ring-slate-200">

                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview foto profil" class="size-full object-cover">
                    @elseif ($user->hasMedia('avatar'))
                        <img src="{{ $user->getFirstMediaUrl('avatar') }}" alt="Foto profil {{ auth()->user()->name }}"
                            class="size-full object-cover">
                    @else
                        <img src="{{ asset('assets/nonProfile.jpg') }}" alt="Preview foto profil"
                            class="size-full object-cover">
                    @endif

                </div>

                {{-- UPLOAD --}}
                <x-wirekit::stack gap="xs" class="w-full">

                    <p class="text-sm font-medium text-slate-900">
                        Foto Profil
                    </p>

                    <x-wirekit::file-upload name="avatar" accept="image/png,image/jpeg,image/webp,image/png"
                        label="Drop an image or click to browse" hint="PNG, JPG, WebP, or PNG — up to 5 MB"
                        wire:model='photo' />

                </x-wirekit::stack>

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

            <x-wirekit::button type="button" :disabled="!$this->canSubmit()" wire:click='save'>
                Simpan Foto
            </x-wirekit::button>

        </x-wirekit::row>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
