<x-wirekit::stack gap="md">

    <x-wirekit::stack gap="sm">
        <a
            href="{{ route('termination.view') }}"
            wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-black transition hover:text-[#30AFFF]"
        >
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">SDM</span>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat PHK</h1>
        <p class="text-sm text-slate-500">Buat pengajuan PHK untuk karyawan aktif dan kirim ke General Manager untuk persetujuan.</p>
    </x-wirekit::stack>

    @if ($errors->has('action'))
        <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('action') }}
        </div>
    @endif

    <x-wirekit::card>
        <x-wirekit::card.header>
            <h2 class="text-lg font-semibold text-slate-900">Informasi PHK</h2>
        </x-wirekit::card.header>

        <x-wirekit::card.body>
            <div class="grid gap-5 md:grid-cols-2">

                <div class="relative" wire:click.outside="closeEmployeeDropdown">
                    <label for="employeeSearch" class="mb-2 block text-sm font-medium text-slate-700">
                        Employee
                    </label>

                    <div class="relative">
                        <input
                            id="employeeSearch"
                            type="text"
                            value="{{ $employeeSearch }}"
                            placeholder="Cari nama atau employee code..."
                            autocomplete="off"
                            wire:model.live.debounce.300ms="employeeSearch"
                            wire:focus="openEmployeeDropdown"
                            wire:keydown.escape="closeEmployeeDropdown"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 outline-none transition focus:border-[#30AFFF] focus:ring-2 focus:ring-[#30AFFF]/20"
                        />

                        @if ($employeeId)
                            <button
                                type="button"
                                wire:click="clearSelectedEmployee"
                                class="absolute right-2 top-1/2 inline-flex -translate-y-1/2 items-center justify-center rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                                aria-label="Hapus karyawan terpilih"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        @endif
                    </div>

                    @if ($errors->has('employeeId'))
                        <p class="mt-1.5 text-xs text-red-600">{{ $errors->first('employeeId') }}</p>
                    @endif

                    @if ($employeeDropdownOpen && ! $employeeId)
                        <div class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                            @if (mb_strlen(trim($employeeSearch)) < 2)
                                <div class="px-4 py-4 text-sm text-slate-500">
                                    Ketik minimal 2 karakter untuk mencari karyawan.
                                </div>
                            @elseif ($employees->isEmpty())
                                <div class="px-4 py-4 text-sm text-slate-500">
                                    Karyawan aktif yang sesuai tidak ditemukan.
                                </div>
                            @else
                                <div class="max-h-64 overflow-y-auto py-1">
                                    @foreach ($employees as $employee)
                                        <button
                                            type="button"
                                            wire:key="termination-employee-{{ $employee->id }}"
                                            wire:click="selectEmployee({{ $employee->id }})"
                                            class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-sky-50"
                                        >
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-medium text-slate-900">
                                                    {{ $employee->user?->name ?? '—' }}
                                                </p>
                                                <p class="mt-0.5 truncate text-xs text-slate-500">
                                                    {{ $employee->employee_code ?? '—' }}
                                                </p>
                                            </div>

                                            <span class="shrink-0 text-xs font-medium text-[#30AFFF]">
                                                Pilih
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($employeeId && $selectedEmployeeName)
                        <div class="mt-2 flex items-center gap-2 rounded-xl border border-sky-100 bg-sky-50 px-3 py-2.5">
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-semibold text-[#30AFFF]">
                                ✓
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-xs font-medium text-sky-900">Karyawan terpilih</p>
                                <p class="truncate text-sm text-sky-700">{{ $selectedEmployeeName }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <x-wirekit::select
                    name="reasonType"
                    label="Jenis Alasan"
                    placeholder="Pilih jenis alasan"
                    :options="$reasonTypes"
                    wire:model="reasonType"
                />

                <x-wirekit::input
                    type="date"
                    name="proposedEffectiveDate"
                    label="Tanggal Efektif PHK"
                    wire:model="proposedEffectiveDate"
                />

                <div class="rounded-xl bg-slate-50 px-4 py-3">
                    <p class="text-xs font-medium text-slate-400">Catatan Proses</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Employee tetap berstatus aktif sampai PHK disetujui, seluruh proses exit selesai, dan tanggal efektif tercapai.
                    </p>
                </div>

                <div class="md:col-span-2">
                    <x-wirekit::textarea
                        name="reason"
                        label="Detail Alasan PHK"
                        placeholder="Jelaskan alasan PHK secara lengkap..."
                        rows="6"
                        wire:model="reason"
                    />
                </div>

                <div class="md:col-span-2">
                    <x-wirekit::textarea
                        name="notes"
                        label="Catatan Tambahan"
                        placeholder="Catatan tambahan untuk proses PHK (opsional)..."
                        rows="4"
                        wire:model="notes"
                    />
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <x-wirekit::button
                    type="button"
                    class="bg-[#30AFFF] text-white hover:bg-sky-500"
                    wire:click="save"
                >
                    Ajukan PHK
                </x-wirekit::button>
            </div>
        </x-wirekit::card.body>
    </x-wirekit::card>

</x-wirekit::stack>
