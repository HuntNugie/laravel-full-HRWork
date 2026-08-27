<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">

        <a href="{{ route('employee.view') }}" wire:navigate
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">
            <span aria-hidden="true">&larr;</span>
            Kembali
        </a>

        <span class="text-sm font-medium text-[#30AFFF]">
            Manajemen Karyawan
        </span>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900">
            Buat Contract
        </h1>

        <p class="text-sm text-slate-500">
            Tentukan posisi, ketentuan kerja, gaji, dan tunjangan karyawan.
        </p>

    </x-wirekit::stack>


    {{-- =====================================================
        MAIN CONTENT
    ====================================================== --}}
    <x-wirekit::form wire:submit="store">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_340px]">


            {{-- =================================================
            LEFT COLUMN
        ================================================== --}}

            <div class="space-y-4">


                {{-- =============================================
                POSITION & TEAM
            ============================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Position
                            </h2>

                            <p class="text-sm text-slate-500">
                                Tentukan Jabatan karyawan dalam organisasi.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="grid gap-5">

                            {{-- Position --}}
                            <div class="space-y-2">

                                <x-wirekit::select label="Position" name="form.positionId"
                                    wire:model.live="form.positionId" placeholder="Pilih position..."
                                    :options="$positions" />

                                <div class="rounded-lg border border-sky-100 bg-sky-50 px-3 py-2">

                                    <p class="text-xs font-medium text-sky-700">
                                        Minimum Salary / Day
                                    </p>

                                    <p class="mt-0.5 text-sm font-semibold text-sky-900">
                                        Rp{{ number_format($form->salary_position) }}
                                    </p>

                                </div>

                            </div>





                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =============================================
                CONTRACT INFORMATION
            ============================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Informasi Contract
                            </h2>

                            <p class="text-sm text-slate-500">
                                Tentukan periode dan ketentuan utama contract.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="grid gap-5 md:grid-cols-2">

                            {{-- Contract Number --}}
                            <x-wirekit::input class="text-black" label="Nomor Contract" name="contract_number"
                                wire:model="contract_number" readonly />

                            {{-- Employment Type --}}
                            <x-wirekit::select label="Jenis Employment" name="form.contractType"
                                placeholder="Pilih jenis employment..." :options="[
                                    'pkwt' => 'PKWT',
                                    'pkwtt' => 'PKWTT',
                                    'internship' => 'Internship',
                                    'freelance' => 'Freelance',
                                ]"
                                wire:model.live="form.contractType" />

                            {{-- Start Date --}}
                            <x-wirekit::input class="text-black" label="Tanggal Mulai" name="form.start_date"
                                type="date" wire:model.live="form.start_date" />

                            {{-- End Date --}}
                            <x-wirekit::input class="text-black" label="Tanggal Berakhir" name="form.end_date"
                                type="date" wire:model.live="form.end_date" :disabled="$is_active" />

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =============================================
                SALARY
            ============================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Compensation
                            </h2>

                            <p class="text-sm text-slate-500">
                                Tentukan gaji yang berlaku pada contract ini.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="grid gap-5 md:grid-cols-2">

                            {{-- Daily Salary --}}
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
                                    Minimum position saat ini: Rp{{ number_format($form->salary_position) }} / hari.
                                </p>

                            </div>


                            {{-- Status --}}
                            <x-wirekit::select label="Status Contract" name="form.statusContract"
                                placeholder="Pilih status contract..." :options="[
                                    'draft' => 'Draft',
                                    'active' => 'Active',
                                ]"
                                wire:model.live="form.statusContract" />

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =============================================
    BENEFITS
================================================= --}}
                <x-wirekit::card>
                    <x-wirekit::card.header>
                        <x-wirekit::stack gap="1">
                            <h2 class="text-lg font-semibold text-slate-900">
                                Tunjangan / Benefits
                            </h2>

                            <p class="text-sm text-slate-500">
                                Pilih tunjangan yang diberikan dan tentukan nominalnya.
                            </p>
                        </x-wirekit::stack>
                    </x-wirekit::card.header>

                    <x-wirekit::card.body>
                        <div class="space-y-3">

                            @foreach ($benefits as $benefit)
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
                            @endforeach



                        </div>
                    </x-wirekit::card.body>
                </x-wirekit::card>

                {{-- =============================================
                NOTES
            ============================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Catatan
                            </h2>

                            <p class="text-sm text-slate-500">
                                Tambahkan catatan jika diperlukan.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <x-wirekit::textarea class="text-black" label="Catatan Contract" name="notes" rows="4"
                            wire:model.live.500ms="form.note"
                            placeholder="Tambahkan catatan mengenai contract..."></x-wirekit::textarea>

                    </x-wirekit::card.body>

                </x-wirekit::card>

            </div>


            {{-- =================================================
            RIGHT COLUMN
        ================================================== --}}
            <div class="space-y-4 lg:sticky lg:top-6 lg:self-start">


                {{-- =============================================
                EMPLOYEE SUMMARY
            ============================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Employee
                            </h2>

                            <p class="text-sm text-slate-500">
                                Informasi karyawan.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="space-y-4">

                            <div class="flex items-center gap-3">

                                <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-sky-100">

                                    <span class="text-sm font-semibold text-sky-600">
                                        NP
                                    </span>

                                </div>

                                <div class="min-w-0">

                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        {{ $employee->user->name }}
                                    </p>

                                    <p class="truncate text-xs text-slate-400">
                                        {{ $employee->employee_code }}
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
                                            {{ $employee->profile->gender }}
                                        </p>

                                    </div>

                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Phone number
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-slate-700">
                                            {{ $employee->profile->phone_number }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =============================================
                SALARY SUMMARY
            ============================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.header>

                        <x-wirekit::stack gap="1">

                            <h2 class="text-lg font-semibold text-slate-900">
                                Salary Summary
                            </h2>

                            <p class="text-sm text-slate-500">
                                Perkiraan total penghasilan harian.
                            </p>

                        </x-wirekit::stack>

                    </x-wirekit::card.header>


                    <x-wirekit::card.body>

                        <div class="space-y-3">

                            {{-- Daily Salary --}}
                            <div class="flex items-center justify-between">

                                <span class="text-sm text-slate-500">
                                    Gaji Harian
                                </span>

                                <span class="text-sm font-medium text-slate-800">
                                    Rp{{ number_format($form->salary_daily) }}
                                </span>

                            </div>


                            {{-- Benefits --}}
                            <div class="space-y-2">
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
                            </div>


                            <div class="border-t border-slate-200 pt-3">

                                <div class="flex items-end justify-between">

                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Total Gaji / Day
                                        </p>

                                        <p class="mt-1 text-xl font-bold text-[#30AFFF]">
                                            Rp{{ number_format($this->totalBenefitSalaryDay) }}
                                        </p>

                                    </div>
                                    <div>

                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                            Total Gaji / Month
                                        </p>

                                        <p class="mt-1 text-xl font-bold text-[#30AFFF]">
                                            Rp{{ number_format($this->totalBenefitSalaryMonth) }}
                                        </p>

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
                                    position sebesar Rp250.000 / hari.
                                </p>

                            </div>
                            {{-- =====================================================
        FORM ACTION
    ====================================================== --}}
                            <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">

                                <x-wirekit::button type="button"
                                    class="border border-slate-200 bg-white text-black hover:bg-slate-50">
                                    Batal
                                </x-wirekit::button>

                                <x-wirekit::button type="submit" class="bg-[#30AFFF] text-white hover:bg-sky-500">
                                    Buat Contract
                                </x-wirekit::button>

                            </div>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>


                {{-- =============================================
                CONTRACT STATUS
            ============================================== --}}
                <x-wirekit::card>

                    <x-wirekit::card.body>

                        <div class="flex items-center justify-between gap-3">

                            <div>

                                <p class="text-sm font-medium text-slate-800">
                                    Contract Status
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Contract ini masih dalam tahap {{ $form->statusContract }}.
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
