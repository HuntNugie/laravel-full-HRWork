<x-wirekit::modal name="reject-leave-{{ $request->id }}">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    <x-wirekit::modal.header>
        Tolak Pengajuan Cuti
    </x-wirekit::modal.header>

    <x-wirekit::form wire:submit="reject">

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                <div>

                    <p class="text-sm text-slate-600">
                        Anda akan menolak pengajuan cuti berikut.
                    </p>

                    <div class="mt-3 rounded-lg bg-slate-50 p-3">

                        <p class="text-sm font-medium text-slate-800">
                            {{ $request->employees?->user?->name ?? '-' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $request->leaveType?->name ?? '-' }}
                            ·
                            {{ $request->total_days }} Hari
                        </p>

                    </div>

                </div>


                <x-wirekit::textarea label="Alasan Penolakan" placeholder="Masukkan alasan penolakan..." rows="4"
                    wire:model="rejectionReason" />

            </x-wirekit::stack>

        </x-wirekit::modal.body>


        <x-wirekit::modal.footer>

            <x-wirekit::row justify="end" gap="sm">

                <x-wirekit::modal.close>

                    <x-wirekit::button intent="neutral" surface="ghost" size="sm" type="button">
                        Batal
                    </x-wirekit::button>

                </x-wirekit::modal.close>


                <x-wirekit::button intent="danger" size="sm" type="submit">

                    <span wire:loading.remove wire:target="reject">
                        Ya, Tolak
                    </span>

                    <span wire:loading wire:target="reject">
                        Mohon tunggu...
                    </span>

                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
