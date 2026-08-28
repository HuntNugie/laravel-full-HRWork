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
                Teams
            </h1>

            <p class="text-sm text-slate-500">
                Kelola team yang terdapat dalam struktur organisasi.
            </p>

        </x-wirekit::stack>


        @can('create-team')

            <livewire:components.main.team.form-add>
                <x-wirekit::button class="bg-[#30AFFF] text-white hover:bg-sky-500" >
                    <x-wirekit::icon name=plus/> Team
                </x-wirekit::button>
            </livewire:components.main.team.form-add>

        @endcan

    </div>


    {{-- =====================================================
    TEAM LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Team List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar team yang terdaftar dalam organisasi.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}
                <div class="w-full sm:w-64">

                    <x-wirekit::input placeholder="Cari nama team" name="search" class="text-black" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="name">
                                Team
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="division">
                                Division
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="manager">
                               Supervisor
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="members">
                                Members
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

                        {{-- =================================================
                        TEAM 1
                        ================================================== --}}
                      @forelse ($teams as $team)
                            <x-wirekit::table.row>

                            <x-wirekit::table.td>

                                <div class="flex items-center gap-3">


                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-semibold text-slate-800">
                                            {{ $team->name }}
                                        </p>

                                        <p class="truncate text-xs text-slate-400">
                                           {{$team->description}}
                                        </p>

                                    </div>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                              {{$team->divisi->name}}
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <div class="flex items-center gap-2">

                                    <span class="text-sm text-slate-700">
                                       {{$team->supervisor->user->name ?? "Tidak ada supervisor"}}
                                    </span>

                                </div>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>
                                {{ count($team->nonSupervisors) }} employees
                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                <span class="inline-flex items-center rounded-full
                                            px-2.5 py-1
                                           text-xs font-medium {{ $team->is_active === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                    {{ $team->is_active }}
                                </span>

                            </x-wirekit::table.td>


                            <x-wirekit::table.td>

                                @can('show-team')

                                    <x-wirekit::button type="button" class="px-3 py-1.5 text-xs" href="{{ route('team.show',$team->id) }}" wire:navigate>
                                        Detail
                                    </x-wirekit::button>

                                @endcan

                            </x-wirekit::table.td>

                        </x-wirekit::table.row>
                      @empty
                          <x-wirekit::table.row>
                              <x-wirekit::table.td colspan="6">
                                  <div class="flex flex-col items-center justify-center py-8">
                                      <x-wirekit::icon name="inbox" class="size-12 text-slate-300 mb-3" />
                                      <p class="text-sm font-medium text-slate-500">
                                          Tidak ada data team
                                      </p>
                                      <p class="text-xs text-slate-400 mt-1">
                                          Mulai dengan membuat team baru
                                      </p>
                                  </div>
                              </x-wirekit::table.td>
                          </x-wirekit::table.row>
                      @endforelse




                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>

</x-wirekit::stack>
