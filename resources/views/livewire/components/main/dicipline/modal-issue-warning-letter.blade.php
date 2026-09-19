<x-wirekit::modal name="issue-warning-letter-{{ $warningLetter->id }}" size="md">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Terbitkan Surat Peringatan
            </h2>

            <p class="text-sm text-slate-500">
                Pastikan data surat telah diperiksa sebelum diterbitkan.
            </p>

        </x-wirekit::stack>
    </x-wirekit::modal.header>

    {{-- FORM --}}
    <x-wirekit::form wire:submit="issue">

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
                                Tanggal Surat
                            </p>

                            <p class="text-sm font-medium text-slate-900">
                                {{ $warningLetter->issued_date?->translatedFormat('d F Y') ?? '-' }}
                            </p>
                        </div>

                    </x-wirekit::stack>
                </div>

                {{-- KONFIRMASI --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="text-sm font-medium text-slate-900">
                        Terbitkan surat ini?
                    </p>

                    <p class="mt-1 text-sm leading-5 text-slate-500">
                        Setelah diterbitkan, surat akan berstatus resmi dan tidak dapat diedit.
                        Tindakan pembatalan tetap tersedia setelah surat diterbitkan.
                    </p>

                </div>

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

                <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="issue">
                    <span wire:loading.remove wire:target="issue">
                        Terbitkan
                    </span>

                    <span wire:loading wire:target="issue">
                        mohon tunggu sebentar
                    </span>
                </x-wirekit::button>

            </x-wirekit::row>
        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
