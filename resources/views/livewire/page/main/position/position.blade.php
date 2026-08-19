<x-wirekit::stack gap="md">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <x-wirekit::stack gap="sm">

            <span class="text-sm font-medium text-[#30AFFF]">
                Organization
            </span>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Positions
            </h1>

            <p class="text-sm text-slate-500">
                Kelola jabatan dan ketentuan gaji minimum dalam organisasi.
            </p>

        </x-wirekit::stack>

        @can('create-position')
            <livewire:components.main.position.form-add>
                <x-wirekit::button class="bg-[#30AFFF] text-white hover:bg-sky-500">
                    <x-wirekit::icon name=plus /> Position
                </x-wirekit::button>
            </livewire:components.main.position.form-add>
        @endcan

    </div>


    {{-- =====================================================
    POSITION LIST
    ====================================================== --}}
    <x-wirekit::card>

        <x-wirekit::card.header>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <x-wirekit::stack gap="1">

                    <h2 class="text-lg font-semibold text-slate-900">
                        Position List
                    </h2>

                    <p class="text-sm text-slate-500">
                        Daftar jabatan yang tersedia dalam organisasi.
                    </p>

                </x-wirekit::stack>


                {{-- Search --}}
                <div class="w-full sm:w-64">

                    <x-wirekit::input placeholder="Cari nama jabatan" wire:model.live.debounce.500ms="search" name="search" class="text-black" />

                </div>

            </div>

        </x-wirekit::card.header>


        <x-wirekit::card.body>

            <div class="overflow-x-auto">

                <x-wirekit::table alpine-sort hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th sortable column="name">
                                Position
                            </x-wirekit::table.th>


                            <x-wirekit::table.th sortable column="minimum_salary_daily">
                                Minimum Salary / Day
                            </x-wirekit::table.th>

                            <x-wirekit::table.th sortable column="employees">
                                Employees
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
                        POSITION 1
                        ================================================== --}}
                      @forelse ($positions as $position)
                          
                      <x-wirekit::table.row>

                          {{-- Position --}}
                          <x-wirekit::table.td>

                              <div class="min-w-0">

                                  <p class="truncate text-sm font-semibold text-slate-800">
                                     {{$position->name}}
                                  </p>

                                  <p class="truncate text-xs text-slate-400">
                                      {{ $position->description }}
                                  </p>

                              </div>

                          </x-wirekit::table.td>


                        

                          {{-- Minimum Salary --}}
                          <x-wirekit::table.td>

                              <span class="text-sm font-medium text-slate-700">
                                  Rp{{ number_format(($position->min_salary_daily),0,'.') }}
                              </span>

                          </x-wirekit::table.td>


                          {{-- Employees --}}
                          <x-wirekit::table.td>

                              <span class="text-sm text-slate-700">
                                 {{ count($position->employees) }} employees
                              </span>

                          </x-wirekit::table.td>


                          {{-- Status --}}
                          <x-wirekit::table.td>

                              <span class="inline-flex items-center rounded-full
                                   px-2.5 py-1
                                  text-xs font-medium {{ $position->is_active == 'active' ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }}">
                                  {{ $position->is_active }}
                              </span>

                          </x-wirekit::table.td>


                          {{-- Actions --}}
                          <x-wirekit::table.td>

                              <x-wirekit::button type="button" class="px-3 py-1.5 text-xs">
                                  Detail
                              </x-wirekit::button>

                          </x-wirekit::table.td>

                      </x-wirekit::table.row>
                      @empty
                          <x-wirekit::table.row>
                              <x-wirekit::table.td colspan="6" class="text-center py-8">
                                  <p class="text-sm text-slate-500">Tidak ada data posisi yang tersedia.</p>
                              </x-wirekit::table.td>
                          </x-wirekit::table.row>
                      @endforelse


                  
                    </x-wirekit::table.body>

                </x-wirekit::table>
                {{ $positions->links() }}
            </div>

        </x-wirekit::card.body>

    </x-wirekit::card>


</x-wirekit::stack>