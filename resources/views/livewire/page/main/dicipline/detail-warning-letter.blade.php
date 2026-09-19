<div>

    <x-wirekit::stack gap="lg">

        <div>
            <a href="{{ route('discipline.warning-letter.view') }}" wire:navigate
                class="mb-3 inline-flex items-center text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">

                ← Kembali

            </a>
        </div>

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

            <x-wirekit::stack gap="xs">
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-slate-900">
                        Detail Surat Peringatan
                    </h1>

                    <x-wirekit::badge :intent="$this->statusIntent()">
                        {{ $this->statusLabel() }}
                    </x-wirekit::badge>
                </div>

                <p class="text-sm text-slate-500">
                    {{ $warningLetter->letter_number ?? 'Nomor surat belum ditentukan' }}
                </p>
            </x-wirekit::stack>

            {{-- ACTION --}}
            <x-wirekit::row gap="sm">

                {{-- ACTION --}}
                <x-wirekit::row gap="sm">

                    @if ($warningLetter->status === 'draft')
                        @can('edit-warning-letter')
                            <livewire:components.main.dicipline.modal-edit-warning-letter :warning-letter="$warningLetter"
                                :key="'edit-warning-letter-' . $warningLetter->id">
                                <x-wirekit::button type="button" variant="outline">
                                    <x-wirekit::icon name="pencil" />
                                    Edit
                                </x-wirekit::button>
                            </livewire:components.main.dicipline.modal-edit-warning-letter>
                        @endcan
                        @can('issue-warning-letter')
                            <livewire:components.main.dicipline.modal-issue-warning-letter :warning-letter="$warningLetter"
                                :key="'issue-warning-letter-' . $warningLetter->id">
                                <x-wirekit::button type="button">
                                    <x-wirekit::icon name="check" />
                                    Terbitkan
                                </x-wirekit::button>
                            </livewire:components.main.dicipline.modal-issue-warning-letter>
                        @endcan
                    @endif

                    @if ($warningLetter->status !== 'cancelled')
                        @can('cancel-warning-letter')
                            <livewire:components.main.dicipline.modal-cancel-warning-letter :warning-letter="$warningLetter"
                                :key="'cancel-warning-letter-' . $warningLetter->id">
                                <x-wirekit::button type="button" variant="outline" intent="danger">
                                    Batalkan
                                </x-wirekit::button>
                            </livewire:components.main.dicipline.modal-cancel-warning-letter>
                        @endcan
                    @endif


                </x-wirekit::row>

            </x-wirekit::row>

        </div>


        {{-- IDENTITAS KARYAWAN --}}
        <x-wirekit::card>

            <x-wirekit::card.header>
                <x-wirekit::stack gap="xs">
                    <h2 class="font-semibold text-slate-900">
                        Karyawan
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi karyawan yang menerima surat peringatan.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <div>
                        <p class="text-sm text-slate-500">
                            Nama
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ $warningLetter->employee->user?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">
                            Kode Karyawan
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ $warningLetter->employee->employee_code ?? '-' }}
                        </p>
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- INFORMASI SURAT --}}
        <x-wirekit::card>

            <x-wirekit::card.header>
                <x-wirekit::stack gap="xs">
                    <h2 class="font-semibold text-slate-900">
                        Informasi Surat
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi utama Surat Peringatan.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <div>
                        <p class="text-sm text-slate-500">
                            Level
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ $warningLetter->warning_level }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">
                            Nomor Surat
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ $warningLetter->letter_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">
                            Tanggal Surat
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ $warningLetter->issued_date?->translatedFormat('d F Y') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-slate-500">
                            Status
                        </p>

                        <div class="mt-1">
                            <x-wirekit::badge :intent="$this->statusIntent()">
                                {{ $this->statusLabel() }}
                            </x-wirekit::badge>
                        </div>
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- DETAIL PELANGGARAN --}}
        <x-wirekit::card>

            <x-wirekit::card.header>
                <x-wirekit::stack gap="xs">
                    <h2 class="font-semibold text-slate-900">
                        Detail Pelanggaran
                    </h2>

                    <p class="text-sm text-slate-500">
                        Alasan dan keterangan yang menjadi dasar surat.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    <div>
                        <p class="text-sm text-slate-500">
                            Alasan
                        </p>

                        <p class="mt-1 text-sm leading-6 text-slate-900">
                            {{ $warningLetter->reason }}
                        </p>
                    </div>

                    @if ($warningLetter->description)
                        <div>
                            <p class="text-sm text-slate-500">
                                Keterangan
                            </p>

                            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-900">
                                {{ $warningLetter->description }}
                            </p>
                        </div>
                    @endif

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- RIWAYAT --}}
        <x-wirekit::card>

            <x-wirekit::card.header>
                <x-wirekit::stack gap="xs">
                    <h2 class="font-semibold text-slate-900">
                        Riwayat
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi proses Surat Peringatan.
                    </p>
                </x-wirekit::stack>
            </x-wirekit::card.header>

            <x-wirekit::card.body>

                <x-wirekit::stack gap="md">

                    <div>
                        <p class="text-sm text-slate-500">
                            Dibuat oleh
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ $warningLetter->creator?->name ?? '-' }}
                        </p>

                        <p class="text-xs text-slate-500">
                            {{ $warningLetter->created_at?->translatedFormat('d F Y H:i') ?? '-' }}
                        </p>
                    </div>

                    @if ($warningLetter->status === 'issued')
                        <div>
                            <p class="text-sm text-slate-500">
                                Diterbitkan oleh
                            </p>

                            <p class="mt-1 font-medium text-slate-900">
                                {{ $warningLetter->issuer?->name ?? '-' }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $warningLetter->issued_at?->translatedFormat('d F Y H:i') ?? '-' }}
                            </p>
                        </div>
                    @endif

                    @if ($warningLetter->status === 'cancelled')
                        <div>
                            <p class="text-sm text-slate-500">
                                Dibatalkan oleh
                            </p>

                            <p class="mt-1 font-medium text-slate-900">
                                {{ $warningLetter->canceller?->name ?? '-' }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $warningLetter->cancelled_at?->translatedFormat('d F Y H:i') ?? '-' }}
                            </p>
                        </div>

                        @if ($warningLetter->cancellation_reason)
                            <div>
                                <p class="text-sm text-slate-500">
                                    Alasan Pembatalan
                                </p>

                                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-900">
                                    {{ $warningLetter->cancellation_reason }}
                                </p>
                            </div>
                        @endif
                    @endif

                </x-wirekit::stack>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </x-wirekit::stack>

</div>
