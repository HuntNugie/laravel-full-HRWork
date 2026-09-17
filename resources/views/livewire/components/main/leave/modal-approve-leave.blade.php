<x-wirekit::modal name="approve-leave-{{ $request->id }}">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Konfirmasi Setujui
            </h2>

            <p class="text-sm text-slate-500">
                Pastikan data pengajuan sudah sesuai sebelum menyetujui.
            </p>

        </x-wirekit::stack>
    </x-wirekit::modal.header>


    {{-- BODY --}}
    <x-wirekit::modal.body>

        <x-wirekit::stack gap="md">

            <p class="text-sm leading-6 text-slate-600">
                Apakah Anda yakin ingin menyetujui pengajuan cuti ini?
            </p>


            {{-- INFORMASI REQUEST --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                <x-wirekit::stack gap="xs">

                    <div class="flex items-center justify-between gap-4">

                        <span class="text-xs text-slate-500">
                            Karyawan
                        </span>

                        <span class="text-sm font-medium text-slate-800 text-right">
                            {{ $request->employees?->user?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4">

                        <span class="text-xs text-slate-500">
                            Jenis Cuti
                        </span>

                        <span class="text-sm font-medium text-slate-800 text-right">
                            {{ $request->leaveType?->name ?? '-' }}
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4">

                        <span class="text-xs text-slate-500">
                            Durasi
                        </span>

                        <span class="text-sm font-medium text-slate-800 text-right">
                            {{ $request->total_days }} Hari
                        </span>

                    </div>

                </x-wirekit::stack>

            </div>




        </x-wirekit::stack>

    </x-wirekit::modal.body>


    {{-- FOOTER --}}
    <x-wirekit::modal.footer>

        <x-wirekit::row justify="end" gap="sm">

            <x-wirekit::modal.close>

                <x-wirekit::button intent="neutral" surface="ghost" size="sm" type="button">
                    Batal
                </x-wirekit::button>

            </x-wirekit::modal.close>


            <x-wirekit::button size="sm" wire:click="approve" wire:loading.attr="disabled" wire:target="approve">

                <span wire:loading.remove wire:target="approve">
                    Ya, Setujui
                </span>

                <span wire:loading wire:target="approve">
                    Memproses...
                </span>

            </x-wirekit::button>

        </x-wirekit::row>

    </x-wirekit::modal.footer>

</x-wirekit::modal>
