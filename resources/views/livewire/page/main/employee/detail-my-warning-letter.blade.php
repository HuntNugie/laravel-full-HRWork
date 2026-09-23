<div class="space-y-6">
    <div>
        <a
            href="{{ route('warning-letter.my.view') }}"
            wire:navigate
            class="mb-3 inline-flex items-center text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]"
        >
            ← Kembali ke Surat Peringatan Saya
        </a>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-[#30AFFF]">Kedisiplinan Saya</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Detail Surat Peringatan</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $warningLetter->letter_number ?? 'Nomor surat belum tersedia' }}</p>
            </div>

            <x-wirekit::badge intent="success">
                Diterbitkan
            </x-wirekit::badge>
        </div>
    </div>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Informasi Surat</h2>
                <p class="text-sm text-slate-500">Informasi surat peringatan yang diterbitkan untuk kamu.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Level</p>
                    <div class="mt-2">
                        <x-wirekit::badge intent="warning">{{ $warningLetter->warning_level }}</x-wirekit::badge>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Nomor Surat</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $warningLetter->letter_number ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Tanggal Surat</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $warningLetter->issued_date?->translatedFormat('d F Y') ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Diterbitkan</p>
                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $warningLetter->issued_at?->translatedFormat('d F Y H:i') ?? '—' }}</p>
                </div>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.header>
            <x-wirekit::stack gap="1">
                <h2 class="text-lg font-semibold text-slate-900">Detail Pelanggaran</h2>
                <p class="text-sm text-slate-500">Alasan dan keterangan yang tercantum pada surat.</p>
            </x-wirekit::stack>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <x-wirekit::stack gap="lg">
                <div>
                    <p class="text-sm font-medium text-slate-500">Alasan</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-900">{{ $warningLetter->reason }}</p>
                </div>

                @if ($warningLetter->description)
                    <div>
                        <p class="text-sm font-medium text-slate-500">Keterangan</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-900">{{ $warningLetter->description }}</p>
                    </div>
                @endif
            </x-wirekit::stack>
        </x-wirekit::card.body>
    </x-wirekit::card>

    <x-wirekit::card>
        <x-wirekit::card.body>
            <div class="flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                    <x-wirekit::icon name="information-circle" class="size-5" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Informasi</p>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Surat yang ditampilkan pada halaman ini merupakan Surat Peringatan yang telah diterbitkan.
                    </p>
                </div>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>
</div>
