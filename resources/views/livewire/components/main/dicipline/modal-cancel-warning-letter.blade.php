<x-wirekit::modal name="cancel-warning-letter-{{ $warningLetter->id }}" size="md">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">
            <h2 class="text-lg font-semibold text-slate-900">
                Batalkan Surat Peringatan
            </h2>

            <p class="text-sm text-slate-500">
                Surat akan diubah menjadi status dibatalkan dan tetap tersimpan di sistem.
            </p>
        </x-wirekit::stack>
    </x-wirekit::modal.header>

    {{-- FORM --}}
    <x-wirekit::form wire:submit="cancel">

        <x-wirekit::modal.body>
            <x-wirekit::stack gap="md">

                {{-- INFO SURAT --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <x-wirekit::stack gap="xs">

                        <div>
                            <p class="text-xs text-slate-500">
                                Karyawan
                            </p>

                            <p class="text-sm font-semibold text-slate-900">
                                {{ $warningLetter->employee->user?->name ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">
                                Nomor Surat
                            </p>

                            <p class="text-sm font-medium text-slate-900">
                                {{ $warningLetter->letter_number ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">
                                Level
                            </p>

                            <p class="text-sm font-medium text-slate-900">
                                {{ $warningLetter->warning_level }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">
                                Status Saat Ini
                            </p>

                            <p class="text-sm font-medium text-slate-900">
                                {{ $warningLetter->status === 'draft' ? 'Draft' : 'Diterbitkan' }}
                            </p>
                        </div>

                    </x-wirekit::stack>
                </div>

                {{-- PERINGATAN --}}
                <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-900">
                        Perhatian
                    </p>

                    @if ($warningLetter->status === 'draft')
                        <p class="mt-1 text-sm leading-5 text-red-700">
                            Draft Surat Peringatan akan dibatalkan dan tidak dapat diedit atau diterbitkan kembali.
                        </p>
                    @else
                        <p class="mt-1 text-sm leading-5 text-red-700">
                            Surat Peringatan yang telah diterbitkan akan dibatalkan.
                            Data surat tetap tersimpan dan riwayat pembatalan akan dicatat.
                        </p>
                    @endif
                </div>

                {{-- ALASAN --}}
                <x-wirekit::textarea label="Alasan Pembatalan" wire:model="cancellationReason" rows="4"
                    placeholder="Masukkan alasan pembatalan surat..." />

            </x-wirekit::stack>
        </x-wirekit::modal.body>

        {{-- FOOTER --}}
        <x-wirekit::modal.footer>
            <x-wirekit::row justify="end" gap="sm">

                <x-wirekit::modal.close>
                    <x-wirekit::button variant="outline" type="button">
                        Batal
                    </x-wirekit::button>
                </x-wirekit::modal.close>

                <x-wirekit::button type="submit" intent="danger" wire:loading.attr="disabled" wire:target="cancel">
                    <span wire:loading.remove wire:target="cancel">
                        Batalkan Surat
                    </span>

                    <span wire:loading wire:target="cancel">
                        mohon tunggu sebentar
                    </span>
                </x-wirekit::button>

            </x-wirekit::row>
        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
