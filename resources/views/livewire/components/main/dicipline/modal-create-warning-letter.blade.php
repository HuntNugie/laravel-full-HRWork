<x-wirekit::modal name="create-warning-letter" size="lg">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>

        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Buat Surat Peringatan
            </h2>

            <p class="text-sm text-slate-500">
                Buat surat peringatan untuk karyawan.
                Surat akan disimpan sebagai draft.
            </p>

        </x-wirekit::stack>

    </x-wirekit::modal.header>

    {{-- FORM --}}
    <x-wirekit::form wire:submit="save">

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- EMPLOYEE --}}
                <div class="relative" wire:click.outside="closeEmployeeDropdown">

                    <label for="warningLetterEmployeeSearch" class="mb-2 block text-sm font-medium text-slate-700">
                        Karyawan
                    </label>

                    <div class="relative">
                        <input
                            id="warningLetterEmployeeSearch"
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
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $errors->first('employeeId') }}
                        </p>
                    @endif

                    @if ($employeeDropdownOpen && ! $employeeId)
                        <div class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">

                            @if (mb_strlen(trim($employeeSearch)) < 2)
                                <div class="px-4 py-4 text-sm text-slate-500">
                                    Ketik minimal 2 karakter untuk mencari karyawan.
                                </div>
                            @elseif ($employees->isEmpty())
                                <div class="px-4 py-4 text-sm text-slate-500">
                                    Karyawan yang sesuai tidak ditemukan.
                                </div>
                            @else
                                <div class="max-h-64 overflow-y-auto py-1">
                                    @foreach ($employees as $employee)
                                        <button
                                            type="button"
                                            wire:key="warning-letter-employee-{{ $employee->id }}"
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
                                <p class="truncate text-xs font-medium text-sky-900">
                                    Karyawan terpilih
                                </p>

                                <p class="truncate text-sm text-sky-700">
                                    {{ $selectedEmployeeName }}
                                </p>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- LEVEL --}}
                <x-wirekit::select label="Level Surat Peringatan" wire:model="warningLevel">
                    <option value="SP1">
                        SP1
                    </option>

                    <option value="SP2">
                        SP2
                    </option>

                    <option value="SP3">
                        SP3
                    </option>
                </x-wirekit::select>

                {{-- NOMOR SURAT --}}
                <x-wirekit::input label="Nomor Surat" wire:model="letterNumber" readonly disabled
                    hint="Nomor surat dibuat otomatis oleh sistem." />

                {{-- TANGGAL --}}
                <x-wirekit::input type="date" label="Tanggal Surat" wire:model="issuedDate" />

                {{-- ALASAN --}}
                <x-wirekit::textarea label="Alasan" wire:model="reason" rows="3"
                    placeholder="Masukkan alasan diterbitkannya surat peringatan..." />

                {{-- KETERANGAN --}}
                <x-wirekit::textarea label="Keterangan" wire:model="description" rows="5"
                    placeholder="Jelaskan detail pelanggaran atau kejadian..." />

                {{-- STATUS INFO --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Status
                    </p>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        Draft
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Surat belum diterbitkan secara resmi.
                        Setelah direview, HR dapat menerbitkannya.
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

                <x-wirekit::button type="submit" wire:loading.attr="disabled" wire:target="save">

                    <span wire:loading.remove wire:target="save">
                        Simpan Draft
                    </span>

                    <span wire:loading wire:target="save">
                        mohon tunggu sebentar
                    </span>

                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
