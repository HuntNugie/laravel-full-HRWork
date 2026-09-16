<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('employee.show', $employee->id) }}"
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Karyawan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Edit Contract
        </h1>

        <p class="text-sm text-slate-500">
            Perbarui posisi, ketentuan kerja, gaji, tunjangan, dan hak cuti contract.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        MAIN CONTENT
    ====================================================== --}}
    <x-wirekit::form>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_340px]">

            {{-- =================================================
                LEFT COLUMN
            ================================================== --}}
            <div class="space-y-4">


                {{-- =================================================
                    POSITION
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Jabatan
                            </h2>

                            <p class="text-sm text-slate-500">
                                Perbarui jabatan karyawan dalam organisasi.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="grid gap-5">

                            <div class="space-y-2">

                                <x-wirekit::select label="Jabatan" name="form.positionId" :options="$positions"
                                    wire:model.live='form.positionId' />

                                <div class="rounded-lg border border-sky-100 bg-sky-50 px-3 py-2">

                                    <p class="text-xs font-medium text-sky-700">
                                        Minimum Salary / Day
                                    </p>

                                    <p class="mt-0.5 text-sm font-semibold text-sky-900">
                                        Rp{{ number_format($this->form->salary_position) }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    CONTRACT INFORMATION
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Informasi Contract
                            </h2>

                            <p class="text-sm text-slate-500">
                                Perbarui periode dan ketentuan utama contract.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="grid gap-5 md:grid-cols-2">

                            {{-- Contract Number --}}
                            <x-wirekit::input class="text-black" label="Nomor Contract" name="contract_number"
                                wire:model="contract_number" readonly />

                            {{-- Employment Type --}}
                            <x-wirekit::select label="Jenis Employment" name="contract_type" :options="[
                                'pkwt' => 'PKWT',
                                'pkwtt' => 'PKWTT',
                                'internship' => 'Internship',
                                'freelance' => 'Freelance',
                            ]"
                                wire:model.live='form.contractType' />

                            {{-- Start Date --}}
                            <x-wirekit::input class="text-black" label="Tanggal Mulai" name="form.start_date"
                                type="date" wire:model.live='form.start_date' />

                            {{-- End Date --}}
                            <x-wirekit::input class="text-black" label="Tanggal Berakhir" name="end_date" type="date"
                                value="2027-09-30" :disabled="$is_active" wire:model.live='form.end_date' />

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    SALARY
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Compensation
                            </h2>

                            <p class="text-sm text-slate-500">
                                Perbarui gaji yang berlaku pada contract ini.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="grid gap-5 md:grid-cols-2">

                            {{-- Daily Salary --}}
                            <div>

                                <x-wirekit::field>

                                    <div>

                                        <div>

                                            <x-wirekit::field>

                                                <div x-data="{
                                                    value: @entangle('form.salary_daily').live,
                                                
                                                    format(value) {
                                                        if (!value) return '';
                                                
                                                        return new Intl.NumberFormat('id-ID').format(value);
                                                    },
                                                
                                                    parse(value) {
                                                        return value.replace(/\D/g, '');
                                                    },
                                                
                                                    onlyNumber(event) {
                                                        const allowedKeys = [
                                                            'Backspace',
                                                            'Delete',
                                                            'ArrowLeft',
                                                            'ArrowRight',
                                                            'ArrowUp',
                                                            'ArrowDown',
                                                            'Tab',
                                                            'Home',
                                                            'End'
                                                        ];
                                                
                                                        if (
                                                            allowedKeys.includes(event.key) ||
                                                            event.ctrlKey ||
                                                            event.metaKey
                                                        ) {
                                                            return;
                                                        }
                                                
                                                        if (!/^[0-9]$/.test(event.key)) {
                                                            event.preventDefault();
                                                        }
                                                    }
                                                }">
                                                    <label class="mb-1 block text-sm font-medium text-slate-700">
                                                        Gaji harian
                                                    </label>

                                                    <div class="relative">

                                                        <span
                                                            class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-500">
                                                            Rp.
                                                        </span>

                                                        <input type="text" inputmode="numeric"
                                                            class="w-full rounded-lg border border-slate-300 py-2 pl-10 pr-3 text-sm"
                                                            :value="format(value)" @keydown="onlyNumber($event)"
                                                            @input="value = parse($event.target.value)" placeholder="0">

                                                        @error('form.salary_daily')
                                                            <span class="text-sm text-red-600">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror

                                                    </div>
                                                </div>

                                            </x-wirekit::field>
                                            <p class="mt-1.5 text-xs text-slate-500">
                                                Minimum position saat ini: Rp{{ number_format($form->salary_position) }}
                                                / hari.
                                            </p>

                                        </div>

                                    </div>

                                </x-wirekit::field>

                            </div>


                            {{-- Status --}}
                            <x-wirekit::select label="Status Contract" name="form.statusContract" :options="[
                                'draft' => 'Draft',
                                'active' => 'Active',
                            ]"
                                wire:model.live='form.statusContract' />

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    BENEFITS
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Tunjangan / Benefits
                            </h2>

                            <p class="text-sm text-slate-500">
                                Perbarui tunjangan yang diberikan pada contract ini.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="space-y-3">


                            @forelse ($benefits as $benefit)
                                <div wire:key="benefit-{{ $benefit->id }}"
                                    class="rounded-lg border border-slate-200 bg-white p-4">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                                        <div class="flex flex-1 items-start gap-3">

                                            <x-wirekit::checkbox value="{{ $benefit->id }}"
                                                wire:model.live="form.benefitSelect.{{ $benefit->id }}.selected" />

                                            <div>
                                                <p class="text-sm font-medium text-slate-800">
                                                    {{ $benefit->name }}
                                                </p>

                                                <p class="mt-0.5 text-xs text-slate-500">
                                                    {{ $benefit->description }}
                                                </p>
                                            </div>

                                        </div>

                                        <div class="w-full sm:w-56">
                                            <x-wirekit::input
                                                class="text-black disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                                                label="Amount" type="number" min="0"
                                                name="form.benefitSelect.{{ $benefit->id }}.amount"
                                                wire:model.live.debounce.300ms="form.benefitSelect.{{ $benefit->id }}.amount"
                                                :disabled="!data_get(
                                                    $form->benefitSelect,
                                                    $benefit->id . '.selected',
                                                    false,
                                                )" placeholder="Contoh: 500000" />
                                        </div>

                                    </div>
                                </div>
                            @empty
                                <div class="rounded-lg border border-dashed border-slate-300 p-6 text-center">
                                    <p class="text-sm text-slate-500">
                                        Belum ada tunjangan.
                                    </p>
                                </div>
                            @endforelse





                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    HAK CUTI
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <div>

                            <h2 class="text-lg font-semibold text-slate-900">
                                Hak Cuti
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Perbarui jumlah hari cuti yang diberikan pada contract ini.
                            </p>

                        </div>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="space-y-5">


                            @forelse ($leaveType as $leave)
                                <div class="rounded-lg border border-slate-200 p-4">

                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                        <div class="space-y-1">

                                            <div class="flex items-center gap-2">

                                                <p class="text-sm font-semibold text-slate-900">
                                                    {{ $leave->name }}
                                                </p>

                                            </div>

                                            <p class="text-sm text-slate-500">
                                                {{ $leave->description }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                Jatah minimum: {{ $leave->default_days }} hari
                                            </p>

                                        </div>


                                        <div class="w-full sm:w-40">

                                            <x-wirekit::input type="number" label="Jatah Cuti" suffix="Hari"
                                                min="{{ $leave->default_days }}"
                                                wire:model='form.dayLeave.{{ $leave->id }}' />

                                        </div>

                                    </div>

                                </div>
                            @empty
                                <div
                                    class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                                    <p class="text-sm font-medium text-slate-700">
                                        Tidak ada hak cuti yang bisa dimasukkan.
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Silakan cek kriteria gender, status aktif, atau konfigurasi jenis cuti.
                                    </p>
                                </div>
                            @endforelse

                        </div>


                        {{-- INFORMATION --}}
                        <div class="mt-5 flex items-start gap-3 rounded-lg bg-sky-50 p-4">

                            <x-wirekit::icon name="information-circle" class="mt-0.5 size-5 shrink-0 text-sky-500" />

                            <div>

                                <p class="text-sm font-medium text-slate-800">
                                    Aturan Jatah Cuti
                                </p>

                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Jatah yang diberikan tidak boleh lebih kecil dari
                                    jatah minimum yang ditetapkan pada jenis cuti.
                                    Jatah dapat diberikan lebih besar sesuai kebijakan perusahaan.
                                </p>

                            </div>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    NOTES
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Catatan
                            </h2>

                            <p class="text-sm text-slate-500">
                                Perbarui catatan contract jika diperlukan.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <x-wirekit::textarea class="text-black" label="Catatan Contract" name="form.note"
                            rows="4" placeholder="Tambahkan catatan mengenai contract..."
                            wire:model.live.debounce.400ms='form.note'></x-wirekit::textarea>

                    </x-wirekit::card.body>

                </x-wirekit::card>


            </div>


            {{-- =================================================
                RIGHT COLUMN
            ================================================== --}}
            <div class="space-y-4 lg:sticky lg:top-6 lg:self-start">


                {{-- =================================================
                    EMPLOYEE
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Employee
                            </h2>

                            <p class="text-sm text-slate-500">
                                Informasi karyawan yang memiliki contract ini.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="space-y-5">


                            {{-- Employee --}}
                            <div class="flex items-center gap-3">

                                <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-sky-100">

                                    @if ($employee?->user?->getFirstMediaUrl('avatar'))
                                        <img src="{{ $employee?->user?->getFirstMediaUrl('avatar') }}" alt=""
                                            class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                                    @else
                                        <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                            class="block size-full rounded-full object-cover bg-[#92EEFF]/60">
                                    @endif

                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        {{ $employee?->user?->name }}
                                    </p>

                                    <p class="truncate text-xs text-slate-400">
                                        {{ $employee?->employee_code }}
                                    </p>

                                </div>

                            </div>


                            <div class="border-t border-slate-100 pt-4">

                                <div class="grid grid-cols-2 gap-4">

                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Gender
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $employee?->profile?->gender }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Phone Number
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $employee?->profile?->phone_number }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    CONTRACT SUMMARY
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Contract
                            </h2>

                            <p class="text-sm text-slate-500">
                                Ringkasan contract yang sedang diedit.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="space-y-5">


                            {{-- Contract Information --}}
                            <div>

                                <div class="mb-3 flex items-center justify-between">

                                    <p class="text-sm font-medium text-slate-700">
                                        Informasi Contract
                                    </p>

                                    <x-wirekit::badge>
                                        Draft
                                    </x-wirekit::badge>

                                </div>


                                <div class="grid grid-cols-2 gap-x-6 gap-y-4">

                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            No. Contract
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $employee?->latestEmployeeContract?->contract_number }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Tipe Contract
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ strtoupper($employee?->latestEmployeeContract?->employement_type) }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Tanggal Mulai
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $employee?->latestEmployeeContract?->start_date->format('d F Y') }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Tanggal Berakhir
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $employee?->latestEmployeeContract?->end_date->format('d F Y') }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Posisi
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ strtoupper($employee?->position?->name) }}
                                        </p>

                                    </div>


                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Gaji Harian
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            Rp{{ number_format($form->salary_daily) }}
                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- Benefits --}}
                            <div class="border-t border-slate-100 pt-5">

                                <p class="mb-3 text-sm font-medium text-slate-700">
                                    Tunjangan
                                </p>

                                <div class="space-y-2">

                                    @forelse ($employee?->latestEmployeeContract?->benefits as $benefit)
                                        <div
                                            class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">

                                            <span class="text-sm text-slate-600">
                                                {{ $benefit->name }}
                                            </span>

                                            <span class="text-sm font-medium text-slate-800">
                                                Rp{{ number_format($benefit->pivot->amount) }}
                                            </span>

                                        </div>
                                    @empty
                                        <div
                                            class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">

                                            <span>Belum ada tunjangan</span>

                                        </div>
                                    @endforelse

                                </div>

                            </div>


                            {{-- Leave --}}
                            <div class="border-t border-slate-100 pt-5">

                                <p class="mb-3 text-sm font-medium text-slate-700">
                                    Hak Cuti
                                </p>

                                <div class="space-y-2">

                                    @forelse ($employee?->latestEmployeeContract?->contractLeave as $leave)
                                        <div
                                            class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">

                                            <span class="text-sm text-slate-600">
                                                {{ $leave?->leaveType?->name }}
                                            </span>

                                            <span class="text-sm font-medium text-slate-800">
                                                {{ $leave?->days }} Hari
                                            </span>

                                        </div>
                                    @empty
                                        <div
                                            class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">

                                            <span class="text-sm text-slate-600">
                                                Belum ada cuti yang di tambahkan
                                            </span>

                                        </div>
                                    @endforelse

                                </div>

                            </div>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    SALARY SUMMARY
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Salary Summary
                            </h2>

                            <p class="text-sm text-slate-500">
                                Perkiraan total penghasilan berdasarkan contract ini.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="space-y-3">

                            <div class="flex items-center justify-between">

                                <span class="text-sm text-slate-500">
                                    Gaji Harian
                                </span>

                                <span class="text-sm font-medium text-slate-800">
                                    Rp{{ number_format($form->salary_daily) }}
                                </span>

                            </div>

                            @foreach ($form->benefitSelect as $benefitId => $benefit)
                                @if ($benefit['selected'] ?? false)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-500">
                                            {{ $benefits->find($benefitId)?->name }}
                                        </span>

                                        <span class="text-sm font-medium text-slate-800">
                                            Rp{{ number_format($benefit['amount'] ?? 0) }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach


                            <div class="border-t border-slate-200 pt-5">

                                {{-- =================================================
        COMPENSATION BREAKDOWN
    ================================================== --}}
                                <div class="space-y-4 border-t border-slate-200 pt-5">

                                    {{-- =================================================
        GAJI
    ================================================== --}}
                                    <div class="rounded-xl border border-slate-200 p-4">

                                        <div class="mb-4 flex items-center gap-3">

                                            <div
                                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-slate-100">
                                                <x-wirekit::icon name="banknotes" class="size-4.5 text-slate-600" />
                                            </div>

                                            <div>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    Gaji
                                                </p>

                                                <p class="text-xs text-slate-400">
                                                    Gaji pokok contract
                                                </p>
                                            </div>

                                        </div>


                                        <div class="grid grid-cols-2 divide-x divide-slate-200">

                                            {{-- Gaji / Hari --}}
                                            <div class="pr-4">

                                                <p class="text-xs text-slate-400">
                                                    Per Hari
                                                </p>

                                                <p class="mt-1 text-base font-semibold text-slate-800">
                                                    Rp{{ number_format($form->salary_daily) }}
                                                </p>

                                            </div>


                                            {{-- Gaji / Bulan --}}
                                            <div class="pl-4">

                                                <p class="text-xs text-slate-400">
                                                    Per Bulan<small class="text-red-400">*24 hari kerja</small>
                                                </p>

                                                <p class="mt-1 text-base font-semibold text-slate-800">
                                                    Rp{{ number_format($this->totalSalaryMonth) }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- =================================================
        TUNJANGAN
    ================================================== --}}
                                    <div class="rounded-xl border border-slate-200 p-4">

                                        <div class="mb-4 flex items-center gap-3">

                                            <div
                                                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-50">
                                                <x-wirekit::icon name="gift" class="size-4.5 text-sky-500" />
                                            </div>

                                            <div>
                                                <p class="text-sm font-semibold text-slate-800">
                                                    Tunjangan
                                                </p>

                                                <p class="text-xs text-slate-400">
                                                    Total tunjangan contract
                                                </p>
                                            </div>

                                        </div>


                                        <div class="grid grid-cols-2 divide-x divide-slate-200">

                                            {{-- Tunjangan / Hari --}}
                                            <div class="pr-4">

                                                <p class="text-xs text-slate-400">
                                                    Per Hari
                                                </p>

                                                <p class="mt-1 text-base font-semibold text-slate-800">
                                                    Rp{{ number_format($this->totalBenefitDay) }}
                                                </p>

                                            </div>


                                            {{-- Tunjangan / Bulan --}}
                                            <div class="pl-4">

                                                <p class="text-xs text-slate-400">
                                                    Per Bulan<small class="text-red-400">*24 hari kerja</small>
                                                </p>

                                                <p class="mt-1 text-base font-semibold text-slate-800">
                                                    Rp{{ number_format($this->totalBenefitMonth) }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>


                                    {{-- =================================================
        TOTAL
    ================================================== --}}
                                    <div class="rounded-xl bg-[#30AFFF]/10 p-4">

                                        <div class="mb-4 flex items-center justify-between">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-white">
                                                    <x-wirekit::icon name="calculator"
                                                        class="size-4.5 text-[#30AFFF]" />
                                                </div>

                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800">
                                                        Total Gaji + Tunjangan
                                                    </p>

                                                    <p class="text-xs text-slate-500">
                                                        Estimasi total kompensasi
                                                    </p>
                                                </div>

                                            </div>

                                        </div>


                                        <div class="grid grid-cols-2 gap-4">

                                            {{-- Total / Hari --}}
                                            <div class="rounded-lg bg-white/70 p-3">

                                                <p class="text-xs text-slate-400">
                                                    Per Hari
                                                </p>

                                                <p class="mt-1 text-lg font-bold text-[#30AFFF]">
                                                    Rp{{ number_format($this->totalBenefitSalaryDay) }}
                                                </p>

                                            </div>


                                            {{-- Total / Bulan --}}
                                            <div class="rounded-lg bg-white/70 p-3">

                                                <p class="text-xs text-slate-400">
                                                    Per Bulan<small class="text-red-400">*24 hari kerja</small>
                                                </p>

                                                <p class="mt-1 text-lg font-bold text-[#30AFFF]">
                                                    Rp{{ number_format($this->totalBenefitSalaryMonth) }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- Salary Warning --}}
                            <div class="mt-4 rounded-lg border border-amber-100 bg-amber-50 p-3">

                                <p class="text-xs font-medium text-amber-700">
                                    Minimum Salary
                                </p>

                                <p class="mt-1 text-xs leading-5 text-amber-600">
                                    Gaji harian contract berada di atas minimum
                                    position sebesar Rp{{ number_format($form->salary_position) }} / hari.
                                </p>

                            </div>


                            {{-- FORM ACTION --}}
                            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">

                                <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                                    Simpan Perubahan
                                </x-wirekit::button>

                            </div>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =================================================
                    CONTRACT STATUS
                ================================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.body>

                        <div class="flex items-center justify-between gap-3">

                            <div>

                                <p class="text-sm font-medium text-slate-800">
                                    Contract Status
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Contract ini masih dalam tahap draft
                                    dan masih dapat diperbarui.
                                </p>

                            </div>

                            <span
                                class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $form->statusContract === 'draft' ? 'text-slate-600  bg-slate-100' : 'text-emerald-600  bg-emerald-100' }}">
                                {{ $form->statusContract }}
                            </span>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>

            </div>

        </div>

    </x-wirekit::form>

</x-wirekit::stack>
