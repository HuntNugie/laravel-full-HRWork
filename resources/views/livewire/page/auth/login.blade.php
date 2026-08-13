<div class="w-full max-w-md">

    <x-wirekit::stack gap="6">

        {{-- Heading --}}
        <x-wirekit::stack gap="2">

            <span class="text-sm font-medium text-[#30AFFF]">
                Welcome back
            </span>

            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                Masuk ke akun Anda
            </h1>

            <p class="text-sm leading-6 text-slate-500">
                Kelola SDM dan pekerjaan Anda dalam satu sistem.
            </p>

        </x-wirekit::stack>


        {{-- Login --}}
        <x-wirekit::form wire:submit="authenticate">

            <x-wirekit::stack gap="10">

                <x-wirekit::input name="email" wire:model="email" label="Email" placeholder="nama@perusahaan.com" />

                <x-wirekit::password-input name="password" label="Password" optimistic="savePassword" />

                <div class="mt-4">
                    <x-wirekit::checkbox name="remember" wire:model="remember" label="Ingat Saya" class="my-5" />
                </div>

                <x-wirekit::button type="submit" color="primary" class="w-full bg-[#30AFFF] text-white
                                           hover:bg-sky-500
                                           focus:ring-2 focus:ring-[#92EEFF]">
                    Masuk ke Sistem
                </x-wirekit::button>

            </x-wirekit::stack>

        </x-wirekit::form>




    </x-wirekit::stack>

</div>