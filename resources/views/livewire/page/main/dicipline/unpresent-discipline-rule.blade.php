<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <x-wirekit::stack gap="1">

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Kedisiplinan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Aturan Tidak Hadir
        </h1>

        <p class="text-sm text-slate-500">
            Atur batas ketidakhadiran tanpa keterangan yang dapat
            memicu Surat Peringatan.
        </p>

    </x-wirekit::stack>


    {{-- RULE --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-base font-semibold text-slate-900">
                        Aturan Ketidakhadiran Tanpa Keterangan
                    </h2>

                    <p class="text-sm text-slate-500">
                        Konfigurasi batas ketidakhadiran yang berlaku.
                    </p>

                </x-wirekit::stack>

                @can('edit-unpresent-discipline-rule')
                    <livewire:components.main.dicipline.modal-edit-unpresent-discipline-rule>

                        <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                            Edit Aturan
                        </x-wirekit::button>

                    </livewire:components.main.dicipline.modal-edit-unpresent-discipline-rule>
                @endcan

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-4 md:grid-cols-3">

                {{-- THRESHOLD --}}
                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Batas Ketidakhadiran
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-800">
                        {{ $rule->threshold }} kali
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        ketidakhadiran tanpa keterangan
                    </p>

                </div>


                {{-- PERIOD --}}
                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Periode
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-800">
                        {{ $rule->period_type === 'monthly' ? 'Bulanan' : $rule->period_type }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        dihitung dalam bulan yang sama
                    </p>

                </div>


                {{-- CONSEQUENCE --}}
                <div class="rounded-xl bg-slate-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Konsekuensi
                    </p>

                    <p class="mt-1 text-lg font-semibold text-slate-800">
                        Surat Peringatan
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        setelah batas tercapai
                    </p>

                </div>

            </div>


            {{-- DESCRIPTION --}}
            <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4">

                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                    Deskripsi
                </p>

                <p class="mt-2 text-sm leading-6 text-slate-600">
                    {{ $rule->description ?: 'Belum ada deskripsi aturan.' }}
                </p>

            </div>


            {{-- INFORMATION --}}
            <div class="mt-5 rounded-xl border border-sky-100 bg-sky-50 p-4">

                <p class="text-sm font-semibold text-slate-800">
                    Cara Kerja Aturan
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Karyawan yang tidak hadir pada hari kerja tanpa
                    attendance dan tanpa ketidakhadiran yang disetujui
                    akan dihitung sebagai ketidakhadiran tanpa keterangan.
                </p>

                <div class="mt-3 rounded-lg border border-sky-100 bg-white px-4 py-3">

                    <p class="text-sm font-medium text-slate-800">
                        {{ $rule->threshold }} kali
                        dalam bulan yang sama
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        → Surat Peringatan
                    </p>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</div>
