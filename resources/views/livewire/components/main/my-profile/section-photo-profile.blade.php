 <x-wirekit::card>

     <x-wirekit::card.header>

         <x-wirekit::stack gap="1">

             <h2 class="text-lg font-semibold text-slate-900">
                 Foto Profil
             </h2>

             <p class="text-sm text-slate-500">
                 Gunakan foto yang jelas dan profesional.
             </p>

         </x-wirekit::stack>

     </x-wirekit::card.header>


     <x-wirekit::card.body>

         <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

             {{-- PREVIEW --}}
             <div
                 class="flex size-28 shrink-0 items-center justify-center
           overflow-hidden rounded-full bg-slate-100">
                 @if ($photo)
                     <img src="{{ $photo->temporaryUrl() }}" alt="Preview foto profil" class="size-full object-cover">
                 @elseif (auth()->user()->hasMedia('avatar'))
                     <img src="{{ asset(auth()->user()->getFirstMediaUrl('avatar')) }}"
                         alt="Foto profil {{ auth()->user()->name }}" class="size-full object-cover">
                 @else
                     <img src="{{ asset('assets/nonProfile.jpg') }}" alt="Preview foto profil"
                         class="size-full object-cover">
                 @endif
             </div>


             <div class="flex-1">

                 <x-wirekit::file-upload name="photo" accept="image/jpeg,image/png,image/webp" wire:model='photo'
                     hint="Max 5 MB — JPG, JPEG, PNG, or WEBP" m />

             </div>


             <x-wirekit::button type="button" wire:click='save' :disabled="!$this->canSubmit()">
                 Simpan Foto
             </x-wirekit::button>

         </div>

     </x-wirekit::card.body>

 </x-wirekit::card>
