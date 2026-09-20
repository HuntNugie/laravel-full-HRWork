<x-wirekit::stack gap="md">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

        <x-wirekit::stack gap="xs">

            <a href="{{ route('payroll.view') }}" wire:navigate
                class="inline-flex w-fit items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-[#30AFFF]">
                <span aria-hidden="true">&larr;</span>
                Kembali ke Penggajian
            </a>

            <span class="text-sm font-medium text-[#30AFFF]">
                Penggajian
            </span>

            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    {{ $period->name }}
                </h1>

                @switch($period->status)
                    @case('draft')
                        <x-wirekit::badge intent="warning">
                            Draft
                        </x-wirekit::badge>
                    @break

                    @case('processing')
                        <x-wirekit::badge intent="info">
                            Processing
                        </x-wirekit::badge>
                    @break

                    @case('processed')
                        <x-wirekit::badge intent="primary">
                            Processed
                        </x-wirekit::badge>
                    @break

                    @case('paid')
                        <x-wirekit::badge intent="success">
                            Paid
                        </x-wirekit::badge>
                    @break

                    @case('cancelled')
                        <x-wirekit::badge intent="danger">
                            Cancelled
                        </x-wirekit::badge>
                    @break
                @endswitch

            </div>

            <p class="text-sm text-slate-500">
                Detail dan pengelolaan payroll untuk periode
                {{ $period->start_date->format('d M Y') }}
                —
                {{ $period->end_date->format('d M Y') }}.
            </p>

        </x-wirekit::stack>


        {{-- =================================================
            HEADER ACTION
        ================================================== --}}

        <div class="flex flex-wrap gap-2">

            @if ($period->status === 'draft')

                @can('edit-period-payroll')
                    <livewire:components.main.payroll.modal-edit-payroll-period :period="$period" :key="'edit-period-' . $period->id">

                        <x-wirekit::button type="button" variant="outline">
                            <x-wirekit::icon name="pencil" />
                            Edit Periode
                        </x-wirekit::button>

                    </livewire:components.main.payroll.modal-edit-payroll-period>
                @endcan


                @can('create-period-payroll')
                    @if ($this->generatedEmployeeCount === 0)
                        <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            wire:click="generatePayroll"
                            wire:confirm="Generate payroll untuk seluruh karyawan yang memenuhi syarat pada periode ini?"
                            loading-target="generatePayroll">
                            <x-wirekit::icon name="refresh" />
                            Generate Payroll
                        </x-wirekit::button>
                    @elseif ($this->missingEmployeeCount > 0)
                        <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500"
                            wire:click="generatePayroll"
                            wire:confirm="Sinkronkan payroll untuk {{ $this->missingEmployeeCount }} karyawan yang belum memiliki payroll pada periode ini?"
                            loading-target="generatePayroll">
                            <x-wirekit::icon name="refresh" />
                            Sinkronkan Payroll
                        </x-wirekit::button>
                    @else
                        <x-wirekit::button type="button" variant="outline" disabled>
                            <x-wirekit::icon name="check" />
                            Payroll Lengkap
                        </x-wirekit::button>
                    @endif
                @endcan

            @endif


            @if ($period->status === 'draft')
                @can('delete-period-payroll')
                    <x-wirekit::button type="button" variant="outline" intent="danger" wire:click="deletePeriod"
                        wire:confirm="Hapus payroll periode ini? Semua payroll karyawan yang masih draft di dalam periode ini juga akan dihapus.">
                        <x-wirekit::icon name="trash" />
                        Hapus
                    </x-wirekit::button>
                @endcan
            @endif

            {{-- Tambahkan di HEADER ACTION, setelah action periode yang sudah ada. --}}
            @if (in_array($period->status, ['processed', 'paid'], true))
                @can('show-payroll')
                    <x-wirekit::button type="button" variant="outline"
                        href="{{ route('payroll.print.summary', ['period' => $period->id]) }}" target="_blank">
                        <x-wirekit::icon name="printer" />
                        Cetak Rekap
                    </x-wirekit::button>

                    <x-wirekit::button type="button" variant="outline"
                        href="{{ route('payroll.print.payments', ['period' => $period->id]) }}" target="_blank">
                        <x-wirekit::icon name="printer" />
                        Cetak Pembayaran
                    </x-wirekit::button>
                @endcan
            @endif


        </div>

    </div>



    {{-- =====================================================
        PERIOD INFORMATION
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Informasi Periode
                </h2>

                <p class="text-sm text-slate-500">
                    Informasi dasar periode payroll.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Start Date --}}
                <div>

                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Mulai
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $period->start_date->format('d M Y') }}
                    </p>

                </div>


                {{-- End Date --}}
                <div>

                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Selesai
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $period->end_date->format('d M Y') }}
                    </p>

                </div>


                {{-- Payment Date --}}
                <div>

                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Tanggal Pembayaran
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $period->payment_date?->format('d M Y') ?? 'Belum ditentukan' }}
                    </p>

                </div>


                {{-- Created By --}}
                <div>

                    <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                        Dibuat Oleh
                    </span>

                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $period->creator?->name ?? 'System' }}
                    </p>

                </div>

            </div>


            <div class="mt-6 border-t border-slate-100 pt-6">

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Created At --}}
                    <div>

                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Dibuat Pada
                        </span>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $period->created_at?->format('d M Y H:i') ?? '-' }}
                        </p>

                    </div>


                    {{-- Processed By --}}
                    <div>

                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Diproses Oleh
                        </span>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $period->processor?->name ?? '-' }}
                        </p>

                    </div>


                    {{-- Processed At --}}
                    <div>

                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Diproses Pada
                        </span>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $period->processed_at?->format('d M Y H:i') ?? '-' }}
                        </p>

                    </div>


                    {{-- Paid At --}}
                    <div>

                        <span class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Dibayar Pada
                        </span>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $period->paid_at?->format('d M Y H:i') ?? '-' }}
                        </p>

                    </div>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>



    {{-- =====================================================
        SUMMARY
    ====================================================== --}}

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

        {{-- Total Employee Eligible --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-50">

                        <x-wirekit::icon name="users" class="size-5 text-sky-500" />

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Karyawan Eligible
                        </p>

                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($this->eligibleEmployeeCount) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Karyawan yang memenuhi syarat payroll
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Generated --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50">

                        <x-wirekit::icon name="check" class="size-5 text-emerald-500" />

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Payroll Dibuat
                        </p>

                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($this->generatedEmployeeCount) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Payroll karyawan yang sudah dibuat
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Missing --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-amber-50">

                        <x-wirekit::icon name="warning" class="size-5 text-amber-500" />

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Belum Dibuat
                        </p>

                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ number_format($this->missingEmployeeCount) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Karyawan eligible tanpa payroll
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Gross --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50">

                        <x-wirekit::icon name="arrow-up" class="size-5 text-emerald-500" />

                    </div>

                    <div class="min-w-0">

                        <p class="text-sm text-slate-500">
                            Total Penghasilan
                        </p>

                        <p class="mt-1 truncate text-xl font-bold text-slate-900">
                            {{ $this->money($this->summary['gross_amount']) }}
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Deduction --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-red-50">

                        <x-wirekit::icon name="arrow-down" class="size-5 text-red-500" />

                    </div>

                    <div class="min-w-0">

                        <p class="text-sm text-slate-500">
                            Total Potongan
                        </p>

                        <p class="mt-1 truncate text-xl font-bold text-slate-900">
                            {{ $this->money($this->summary['deduction_amount']) }}
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Net --}}
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-center gap-4">

                    <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-[#E8F9FF]">

                        <x-wirekit::icon name="banknotes" class="size-5 text-[#30AFFF]" />

                    </div>

                    <div class="min-w-0">

                        <p class="text-sm text-slate-500">
                            Total Gaji Bersih
                        </p>

                        <p class="mt-1 truncate text-xl font-bold text-slate-900">
                            {{ $this->money($this->summary['net_amount']) }}
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>



    </div>
    {{-- =====================================================
    GLOBAL PAYROLL ITEMS
====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Komponen Payroll Global
                    </h2>

                    <p class="text-sm text-slate-500">
                        Komponen penghasilan atau potongan yang berlaku
                        untuk seluruh payroll dalam periode ini.
                    </p>

                </x-wirekit::stack>


                @if ($period->status === 'draft' && $this->generatedEmployeeCount > 0)
                    @can('edit-period-payroll')
                        <livewire:components.main.payroll.modal-global-payroll-item :period="$period" :key="'create-global-payroll-item-' . $period->id">
                            <x-wirekit::button type="button">
                                <x-wirekit::icon name="plus" />
                                Tambah Komponen
                            </x-wirekit::button>
                        </livewire:components.main.payroll.modal-global-payroll-item>
                    @endcan
                @endif

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            @if ($this->generatedEmployeeCount === 0)

                <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">

                    <x-wirekit::icon name="document-text" class="mx-auto size-7 text-slate-400" />

                    <p class="mt-3 text-sm font-semibold text-slate-700">
                        Payroll belum dibuat
                    </p>

                    <p class="mt-1 text-sm text-slate-400">
                        Generate payroll terlebih dahulu sebelum menambahkan
                        komponen global.
                    </p>

                </div>
            @elseif ($this->globalPayrollItems->isEmpty())
                <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">

                    <x-wirekit::icon name="document-text" class="mx-auto size-7 text-slate-400" />

                    <p class="mt-3 text-sm font-semibold text-slate-700">
                        Belum ada komponen global
                    </p>

                    <p class="mt-1 text-sm text-slate-400">
                        Tambahkan komponen yang berlaku untuk seluruh payroll
                        karyawan pada periode ini.
                    </p>

                </div>
            @else
                <div class="overflow-x-auto">

                    <x-wirekit::table>

                        <x-wirekit::table.head>

                            <x-wirekit::table.row>

                                <x-wirekit::table.th>
                                    Komponen
                                </x-wirekit::table.th>

                                <x-wirekit::table.th>
                                    Tipe
                                </x-wirekit::table.th>

                                <x-wirekit::table.th align="right">
                                    Nominal / Karyawan
                                </x-wirekit::table.th>

                                <x-wirekit::table.th>
                                    Keterangan
                                </x-wirekit::table.th>

                                <x-wirekit::table.th align="right">
                                    Aksi
                                </x-wirekit::table.th>

                            </x-wirekit::table.row>

                        </x-wirekit::table.head>


                        <x-wirekit::table.body>

                            @foreach ($this->globalPayrollItems as $item)
                                <x-wirekit::table.row>

                                    <x-wirekit::table.td>

                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $item->name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Berlaku untuk seluruh karyawan
                                        </p>

                                    </x-wirekit::table.td>


                                    <x-wirekit::table.td>

                                        @if ($item->type === 'earning')
                                            <x-wirekit::badge intent="success">
                                                Penghasilan
                                            </x-wirekit::badge>
                                        @else
                                            <x-wirekit::badge intent="danger">
                                                Potongan
                                            </x-wirekit::badge>
                                        @endif

                                    </x-wirekit::table.td>


                                    <x-wirekit::table.td align="right">

                                        <span class="text-sm font-semibold text-slate-800">
                                            {{ $this->money($item->amount) }}
                                        </span>

                                    </x-wirekit::table.td>


                                    <x-wirekit::table.td>

                                        <span class="text-sm text-slate-600">
                                            {{ $item->description ?? '-' }}
                                        </span>

                                    </x-wirekit::table.td>


                                    <x-wirekit::table.td align="right">

                                        @if ($period->status === 'draft')
                                            @can('edit-period-payroll')
                                                <livewire:components.main.payroll.modal-global-payroll-item
                                                    :period="$period" :item="$item" :key="'edit-global-payroll-item-' . $item->id">
                                                    <x-wirekit::button type="button" variant="outline"
                                                        class="px-3 py-1.5 text-xs">
                                                        <x-wirekit::icon name="pencil" />
                                                        Edit
                                                    </x-wirekit::button>
                                                </livewire:components.main.payroll.modal-global-payroll-item>
                                            @endcan
                                        @else
                                            <span class="text-xs text-slate-400">
                                                Terkunci
                                            </span>
                                        @endif

                                    </x-wirekit::table.td>

                                </x-wirekit::table.row>
                            @endforeach

                        </x-wirekit::table.body>

                    </x-wirekit::table>

                </div>

            @endif

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
        INCOMPLETE WARNING
    ====================================================== --}}

    @if ($period->status === 'draft' && $this->generatedEmployeeCount > 0 && $this->missingEmployeeCount > 0)
        <x-wirekit::card>

            <x-wirekit::card.body>

                <div class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50 p-4">

                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white">

                        <x-wirekit::icon name="warning" class="size-5 text-amber-600" />

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-amber-800">
                            Payroll belum lengkap
                        </p>

                        <p class="mt-1 text-sm leading-6 text-amber-700">
                            Terdapat
                            <strong>
                                {{ number_format($this->missingEmployeeCount) }}
                            </strong>
                            karyawan eligible yang belum memiliki payroll.
                            Gunakan <strong>Sinkronkan Payroll</strong> untuk menambahkan payroll karyawan yang belum
                            masuk tanpa menghapus payroll draft yang sudah ada.
                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>
    @endif



    {{-- =====================================================
        PAYROLL LIST
    ====================================================== --}}

    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Payroll Karyawan
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar payroll karyawan yang termasuk dalam periode ini.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}

                <div class="w-full lg:w-80">

                    <x-wirekit::input type="search" label="Cari" name="search" class="text-black"
                        placeholder="Nama atau kode karyawan..." wire:model.live.debounce.500ms="search" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Karyawan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Jabatan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Penghasilan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Potongan
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Gaji Bersih
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th align="right">
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($this->payrolls as $payroll)

                            <x-wirekit::table.row>

                                {{-- Employee --}}
                                <x-wirekit::table.td>

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-50">

                                            @if ($payroll->employees?->user?->getFirstMediaUrl('avatar'))
                                                <img src="{{ $payroll->employees->user->getFirstMediaUrl('avatar') }}"
                                                    alt="{{ $payroll->employees?->user?->name ?? 'Employee' }}"
                                                    class="block size-full rounded-full object-cover">
                                            @else
                                                <img src="{{ asset('assets/nonProfile.jpg') }}" alt=""
                                                    class="block size-full rounded-full object-cover">
                                            @endif

                                        </div>


                                        <div class="min-w-0">

                                            <p class="truncate text-sm font-semibold text-slate-800">

                                                {{ $payroll->employees?->user?->name ?? 'Karyawan tidak ditemukan' }}

                                            </p>

                                            <p class="truncate text-xs text-slate-400">

                                                {{ $payroll->employee?->employee_code ?? '-' }}

                                            </p>

                                        </div>

                                    </div>

                                </x-wirekit::table.td>


                                {{-- Position --}}
                                <x-wirekit::table.td>

                                    <span class="text-sm text-slate-700">
                                        {{ $payroll->position_name ?? '-' }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Gross --}}
                                <x-wirekit::table.td align="right">

                                    <span class="text-sm font-medium text-slate-700">
                                        {{ $this->money($payroll->gross_amount) }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Deduction --}}
                                <x-wirekit::table.td align="right">

                                    <span class="text-sm font-medium text-slate-700">
                                        {{ $this->money($payroll->deduction_amount) }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Net --}}
                                <x-wirekit::table.td align="right">

                                    <span class="text-sm font-semibold text-slate-900">
                                        {{ $this->money($payroll->net_amount) }}
                                    </span>

                                </x-wirekit::table.td>


                                {{-- Status --}}
                                <x-wirekit::table.td>

                                    @switch($payroll->status)
                                        @case('draft')
                                            <x-wirekit::badge intent="warning">
                                                Draft
                                            </x-wirekit::badge>
                                        @break

                                        @case('processed')
                                            <x-wirekit::badge intent="primary">
                                                Processed
                                            </x-wirekit::badge>
                                        @break

                                        @case('paid')
                                            <x-wirekit::badge intent="success">
                                                Paid
                                            </x-wirekit::badge>
                                        @break

                                        @case('cancelled')
                                            <x-wirekit::badge intent="danger">
                                                Cancelled
                                            </x-wirekit::badge>
                                        @break

                                        @default
                                            <x-wirekit::badge intent="secondary">
                                                {{ ucfirst($payroll->status) }}
                                            </x-wirekit::badge>
                                    @endswitch

                                </x-wirekit::table.td>


                                {{-- Action --}}
                                <x-wirekit::table.td align="right">
                                    <div class="flex flex-wrap justify-end gap-2">

                                        @can('show-payroll')
                                            <x-wirekit::button type="button" variant="outline"
                                                class="px-3 py-1.5 text-xs"
                                                href="{{ route('payroll.employee.show', [
                                                    'period' => $period->id,
                                                    'payroll' => $payroll->id,
                                                ]) }}"
                                                wire:navigate>
                                                Detail
                                            </x-wirekit::button>
                                        @endcan

                                        @can('mark-paid-payroll')
                                            @if ($period->status === 'processed' && $payroll->status === 'processed')
                                                <x-wirekit::button type="button" variant="outline" intent="success"
                                                    class="px-3 py-1.5 text-xs"
                                                    wire:click="markPayrollAsPaid({{ $payroll->id }})"
                                                    wire:confirm="Tandai payroll {{ $payroll->employees?->user?->name ?? 'karyawan ini' }} sebagai sudah dibayar?">
                                                    <x-wirekit::icon name="check" />
                                                    Mark as Paid
                                                </x-wirekit::button>
                                            @elseif ($payroll->status === 'paid')
                                                <span
                                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                                                    <x-wirekit::icon name="check-circle" class="size-4" />
                                                    Paid
                                                </span>
                                            @endif
                                        @endcan

                                        @can('show-payroll')
                                            @if (in_array($period->status, ['processed', 'paid'], true) && in_array($payroll->status, ['processed', 'paid'], true))
                                                <x-wirekit::button type="button" variant="outline"
                                                    class="px-3 py-1.5 text-xs"
                                                    href="{{ route('payroll.print.slip', [
                                                        'period' => $period->id,
                                                        'payroll' => $payroll->id,
                                                    ]) }}"
                                                    target="_blank">
                                                    <x-wirekit::icon name="printer" />
                                                    Slip Gaji
                                                </x-wirekit::button>
                                            @endif
                                        @endcan

                                    </div>
                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                            @empty

                                <x-wirekit::table.row>

                                    <x-wirekit::table.td colspan="7">

                                        <div class="py-10 text-center">

                                            <div
                                                class="mx-auto flex size-12 items-center justify-center rounded-xl bg-slate-50">

                                                <x-wirekit::icon name="document-text" class="size-6 text-slate-400" />

                                            </div>

                                            <p class="mt-3 text-sm font-semibold text-slate-700">
                                                Belum ada payroll
                                            </p>

                                            <p class="mt-1 text-sm text-slate-400">
                                                Payroll karyawan untuk periode ini belum dibuat.
                                            </p>


                                            @can('create-period-payroll')
                                                @if ($period->status === 'draft')
                                                    <div class="mt-4">

                                                        <x-wirekit::button type="button"
                                                            class="bg-[#30AFFF] text-white hover:bg-sky-500"
                                                            wire:click="generatePayroll"
                                                            wire:confirm="Generate payroll untuk seluruh karyawan yang memenuhi syarat pada periode ini?"
                                                            loading-target="generatePayroll">
                                                            <x-wirekit::icon name="refresh" />
                                                            Generate Payroll
                                                        </x-wirekit::button>

                                                    </div>
                                                @endif
                                            @endcan

                                        </div>

                                    </x-wirekit::table.td>

                                </x-wirekit::table.row>

                            @endforelse

                        </x-wirekit::table.body>

                    </x-wirekit::table>

                </div>


                {{-- Pagination --}}

                @if ($this->payrolls->hasPages())
                    <div class="mt-5 border-t border-slate-100 pt-5">

                        {{ $this->payrolls->links() }}

                    </div>
                @endif

            </x-wirekit::card.body>

        </x-wirekit::card>



        {{-- =====================================================
        PROCESS / PAYMENT ACTION
    ====================================================== --}}

        @if ($period->status === 'draft')

            @can('process-payroll')
                <x-wirekit::card>

                    <x-wirekit::card.body>

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                            <div>

                                <h3 class="text-sm font-semibold text-slate-900">
                                    Periode siap diproses?
                                </h3>

                                @if ($this->eligibleEmployeeCount === 0)
                                    <p class="mt-1 max-w-2xl text-sm text-slate-500">
                                        Belum ada karyawan yang memenuhi syarat untuk payroll periode ini.
                                    </p>
                                @elseif ($this->generatedEmployeeCount === 0)
                                    <p class="mt-1 max-w-2xl text-sm text-slate-500">
                                        Payroll belum dibuat. Generate payroll terlebih dahulu.
                                    </p>
                                @elseif ($this->missingEmployeeCount > 0)
                                    <p class="mt-1 max-w-2xl text-sm text-amber-600">
                                        Masih ada
                                        <strong>{{ number_format($this->missingEmployeeCount) }}</strong>
                                        karyawan eligible yang belum memiliki payroll.
                                    </p>
                                @else
                                    <p class="mt-1 max-w-2xl text-sm text-slate-500">
                                        Seluruh payroll karyawan sudah dibuat.
                                        Pastikan seluruh data sudah diperiksa sebelum diproses.
                                        Setelah diproses, payroll periode ini tidak dapat diedit lagi.
                                    </p>
                                @endif

                            </div>


                            @if ($this->eligibleEmployeeCount > 0 && $this->missingEmployeeCount === 0)
                                <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500"
                                    wire:click="processPayroll"
                                    wire:confirm="Proses payroll periode ini? Setelah diproses, seluruh payroll akan dikunci dan tidak dapat diedit.">
                                    <x-wirekit::icon name="check" />
                                    Process Payroll
                                </x-wirekit::button>
                            @else
                                <x-wirekit::button type="button" class="bg-[#30AFFF] text-white hover:bg-sky-500" disabled>
                                    <x-wirekit::icon name="check" />
                                    Process Payroll
                                </x-wirekit::button>
                            @endif

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>
            @endcan
        @elseif ($period->status === 'processed')
            @can('mark-paid-payroll')
                <x-wirekit::card>

                    <x-wirekit::card.body>

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                            <div>

                                <h3 class="text-sm font-semibold text-slate-900">
                                    Payroll sudah diproses
                                </h3>

                                <p class="mt-1 max-w-2xl text-sm text-slate-500">
                                    Setelah transfer gaji benar-benar dilakukan oleh perusahaan,
                                    tandai periode ini sebagai sudah dibayar.
                                </p>

                            </div>


                            <x-wirekit::button type="button" intent="success" wire:click="markAsPaid"
                                wire:confirm="Tandai payroll periode ini sebagai sudah dibayar?">
                                <x-wirekit::icon name="check" />
                                Tandai Semua Sudah Dibayar
                            </x-wirekit::button>

                        </div>

                    </x-wirekit::card.body>

                </x-wirekit::card>
            @endcan
        @elseif ($period->status === 'paid')
            <x-wirekit::card>

                <x-wirekit::card.body>

                    <div class="flex items-start gap-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4">

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white">

                            <x-wirekit::icon name="check-circle" class="size-5 text-emerald-600" />

                        </div>

                        <div>

                            <p class="text-sm font-semibold text-emerald-800">
                                Payroll sudah dibayarkan
                            </p>

                            <p class="mt-1 text-sm text-emerald-700">
                                Periode payroll ini telah ditandai sebagai sudah dibayar.
                                Tidak ada perubahan yang dapat dilakukan.
                            </p>

                        </div>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>
        @elseif ($period->status === 'cancelled')
            <x-wirekit::card>

                <x-wirekit::card.body>

                    <div class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 p-4">

                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white">

                            <x-wirekit::icon name="close" class="size-5 text-red-600" />

                        </div>

                        <div>

                            <p class="text-sm font-semibold text-red-800">
                                Payroll periode dibatalkan
                            </p>

                            <p class="mt-1 text-sm text-red-700">
                                Periode payroll ini sudah dibatalkan dan tidak dapat diubah.
                            </p>

                        </div>

                    </div>

                </x-wirekit::card.body>

            </x-wirekit::card>

        @endif

    </x-wirekit::stack>
