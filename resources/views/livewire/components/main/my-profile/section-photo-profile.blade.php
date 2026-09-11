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
                           rounded-full bg-slate-100">
                 <span class="text-4xl font-semibold text-slate-400">
                     N
                 </span>
             </div>


             <div class="flex-1">

                 <label
                     class="flex cursor-pointer flex-col items-center justify-center
                               rounded-xl border-2 border-dashed border-slate-200
                               bg-slate-50 px-4 py-6 text-center
                               transition hover:border-[#30AFFF] hover:bg-sky-50">

                     <span class="text-sm font-medium text-slate-700">
                         Pilih Foto
                     </span>

                     <span class="mt-1 text-xs text-slate-400">
                         JPG, JPEG, PNG atau WEBP · Maks. 2MB
                     </span>

                     <input type="file" class="hidden" accept="image/jpeg,image/png,image/webp">

                 </label>

             </div>


             <x-wirekit::button type="button">
                 Simpan Foto
             </x-wirekit::button>

         </div>

     </x-wirekit::card.body>

 </x-wirekit::card>
