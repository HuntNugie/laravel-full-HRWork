<x-wirekit::stack gap="md">

    {{-- =========================================================
        ACCOUNT WITHOUT ROLE
        ---------------------------------------------------------
        STATIC UI:
        Digunakan untuk kondisi ketika akun sudah aktif tetapi
        belum memiliki role.
    ========================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.body>

            <x-wirekit::stack gap="md" class="items-center justify-center text-center">

                {{-- Icon --}}
                <span
                    class="inline-flex h-12 w-12 items-center justify-center
                           rounded-full bg-slate-100 text-xl">
                    🔒
                </span>


                {{-- Information --}}
                <x-wirekit::stack gap="sm">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Role belum ditetapkan
                    </h2>

                    <p class="mx-auto max-w-md text-sm leading-6 text-slate-500">
                        Akun Anda sudah aktif, tetapi belum memiliki role
                        untuk mengakses fitur dalam sistem HRWork.
                        Silakan hubungi administrator untuk menetapkan role.
                    </p>

                </x-wirekit::stack>


                {{-- Action --}}
                <x-wirekit::button type="button" size="sm" intent="neutral" surface="outline">
                    Hubungi Administrator
                </x-wirekit::button>

            </x-wirekit::stack>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
