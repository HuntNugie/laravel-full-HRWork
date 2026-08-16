<x-wirekit::stack gap="md">

    {{-- =====================================================
    PAGE HEADING
    ====================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                Organization
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Divisions
            </h1>

            <p class="text-sm text-slate-500">
                Kelola divisi yang terdapat dalam struktur organisasi.
            </p>

        </x-wirekit::stack>

        @can('create-divisi')
            {{-- Add Division --}}
            <livewire:components.main.divisi.form-add />
        @endcan


    </div>


    {{-- =====================================================
    DIVISION LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Division List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar divisi yang terdaftar dalam organisasi.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}
                <div class="w-full sm:w-64">

                    <input type="text" placeholder="Search division..." class="w-full rounded-lg border border-slate-200
                               bg-white px-3 py-2.5 text-sm
                               text-slate-900 outline-none
                               transition
                               focus:border-[#30AFFF]
                               focus:ring-2 focus:ring-[#30AFFF]/20">

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    {{-- =================================================
                    TABLE HEADER
                    ================================================== --}}
                    <thead>

                        <tr class="border-b border-slate-200">

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                #
                            </th>

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Division
                            </th>

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Description
                            </th>

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Teams
                            </th>

                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Status
                            </th>

                            <th
                                class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    {{-- =================================================
                    TABLE BODY
                    ================================================== --}}
                    <tbody class="divide-y divide-slate-100">


                        {{-- Division 1 --}}
                        @forelse ($divisis as $divisi)
                            <tr class="transition hover:bg-slate-50">

                                <td class="px-4 py-4 text-sm text-slate-400">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-4 py-4">

                                    <div class="flex items-center gap-3">



                                        <div>

                                            <p class="text-sm font-semibold text-slate-800">
                                                {{$divisi->name}}
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <td class="px-4 py-4 text-sm text-slate-500">
                                    {{$divisi->description}}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-600">
                                    {{ count($divisi->team) }}
                                </td>

                                <td class="px-4 py-4">

                                    <span
                                        class="inline-flex items-center rounded-full
                                                    px-2.5 py-1
                                                   text-xs font-medium {{ $divisi->is_active == 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                        {{ $divisi->is_active }}
                                    </span>

                                </td>

                                <td class="px-4 py-4">

                                    <div class="flex justify-end gap-2">

                                        <x-wirekit::button type="button" class="px-3 py-1.5 text-xs">
                                            Edit
                                        </x-wirekit::button>

                                        <x-wirekit::button type="button" class="px-3 py-1.5 text-xs
                                                       bg-red-50 text-red-600
                                                       hover:bg-red-100">
                                            Delete
                                        </x-wirekit::button>
                                    </div>

                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    No data available
                                </td>
                            </tr>
                        @endforelse




                    </tbody>


                </table>
                {{ $divisis->links() }}

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>