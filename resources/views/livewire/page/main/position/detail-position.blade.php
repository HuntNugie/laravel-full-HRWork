<x-wirekit::stack gap="md">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                <a href="{{ route('position.view') }}" wire:navigate
                    class="mb-3 inline-flex items-center text-sm font-medium text-slate-500 hover:text-[#30AFFF]">
                    ← Kembali
                </a>
            </span>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                {{ $position->name }}
            </h1>

            <p class="text-sm text-slate-500">
                Detail informasi jabatan dan employee yang menjabat posisi ini.
            </p>

        </x-wirekit::stack>


        <div class="flex items-center gap-2">

            <x-wirekit::button type="button" class="px-3 py-1.5 text-sm">
                <x-wirekit::icon name="pencil" />
                Edit Position
            </x-wirekit::button>

        </div>

    </div>


    {{-- =====================================================
    POSITION INFORMATION
    ====================================================== --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Main Information --}}
        <x-wirekit::card class="lg:col-span-2">

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Position Information
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi utama mengenai jabatan.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                    {{-- Position Name --}}
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Position
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $position->name }}
                        </p>
                    </div>

             

                    {{-- Minimum Salary --}}
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Minimum Salary / Day
                        </p>

                        <p class="mt-1 text-lg font-semibold text-[#30AFFF]">
                            Rp{{ number_format($position->min_salary_daily, 0) }}
                        </p>
                    </div>


                    {{-- Status --}}
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Status
                        </p>

                        <span
                            class="mt-1 inline-flex items-center rounded-full
                       px-2.5 py-1
                        text-xs font-medium {{ $position->is_active == 'active' ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} ">
                            {{ $position->is_active }}
                        </span>
                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>


        {{-- Summary --}}
        <x-wirekit::card>

            <x-wirekit::card.header>

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Summary
                    </h2>

                    <p class="text-sm text-slate-500">
                        Ringkasan posisi.
                    </p>

                </x-wirekit::stack>

            </x-wirekit::card.header>


            <x-wirekit::card.body>

                <div class="space-y-5">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Total Employees
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-800">
                            {{ count($position->employees) }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Created At
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $position->created_at->format('d F Y') }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Last Updated
                        </p>

                        <p class="mt-1 text-sm text-slate-700">
                            {{ $position->created_at->diffForHumans() }}

                        </p>

                    </div>

                </div>

            </x-wirekit::card.body>

        </x-wirekit::card>

    </div>


    {{-- =====================================================
    DESCRIPTION
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Job Description
                </h2>

                <p class="text-sm text-slate-500">
                    Deskripsi dan tanggung jawab posisi.
                </p>

            </x-wirekit::stack>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="space-y-4">

                <p class="text-sm leading-6 text-slate-600">
                  {{$position->description}}
                </p>

                <div>

                    <h3 class="text-sm font-semibold text-slate-800">
                        Jobdesk utama
                    </h3>

                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    
                    @forelse ($position->jobdesk as $job)
                         <li class="flex gap-2">
                            <span class="text-[#30AFFF]">•</span>
                            {{ $job->jobdesk }}
                        </li>
                    @empty 
                    <li class="flex gap-2">
                           -
                            
                    </li>
                        
                    @endforelse
                       

                    

                    </ul>

                </div>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


    {{-- =====================================================
    EMPLOYEE LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Employees in this Position
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar employee yang sedang menjabat sebagai Software Engineer.
                    </p>

                </x-wirekit::stack>


                <div class="w-full sm:w-64">

                    <x-wirekit::input placeholder="Cari employee" name="search" class="text-black" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="name">
                                Employee
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="team">
                                Team
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="salary">
                                Salary / Day
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="status">
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Actions
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        {{-- Employee 1 --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-sky-100">
                                        <span class="text-sm font-semibold text-sky-600">
                                            AP
                                        </span>
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            Andi Pratama
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                            EMP-001
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm text-slate-700">
                                    Development
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm font-medium text-slate-700">
                                    Rp300.000
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="inline-flex items-center rounded-full
                                bg-emerald-50 px-2.5 py-1
                                text-xs font-medium text-emerald-600">
                                    Active
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::button type="button" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- Employee 2 --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-violet-100">
                                        <span class="text-sm font-semibold text-violet-600">
                                            BA
                                        </span>
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            Budi Ahmad
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                            EMP-004
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm text-slate-700">
                                    Development
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm font-medium text-slate-700">
                                    Rp275.000
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="inline-flex items-center rounded-full
                                bg-emerald-50 px-2.5 py-1
                                text-xs font-medium text-emerald-600">
                                    Active
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::button type="button" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>


                        {{-- Employee 3 --}}
                        <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center rounded-full bg-amber-100">
                                        <span class="text-sm font-semibold text-amber-600">
                                            RF
                                        </span>
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            Rizky Fadillah
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                            EMP-007
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm text-slate-700">
                                    Development
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="text-sm font-medium text-slate-700">
                                    Rp250.000
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="inline-flex items-center rounded-full
                                bg-emerald-50 px-2.5 py-1
                                text-xs font-medium text-emerald-600">
                                    Active
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <x-wirekit::button type="button" class="px-3 py-1.5 text-xs">
                                    Detail
                                </x-wirekit::button>

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>