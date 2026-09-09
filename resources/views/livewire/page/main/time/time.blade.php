<div class="space-y-6">

    {{-- =====================================================
        PAGE HEADING
    ====================================================== --}}
    <x-wirekit::stack gap="sm">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">
                Waktu Kerja
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Atur jadwal jam kerja yang berlaku untuk seluruh karyawan.
            </p>
        </div>
    </x-wirekit::stack>


    {{-- =====================================================
        WORK TIME CARD
    ====================================================== --}}

    <x-wirekit::form wire:submit="submit">

        <x-wirekit::card>

            {{-- HEADER --}}
            <x-wirekit::card.header>

                <x-wirekit::row justify="between" align="center" gap="md">

                    <x-wirekit::stack gap="xs">

                        <h2 class="text-lg font-semibold text-slate-900">
                            Jadwal Waktu Kerja
                        </h2>

                        <p class="text-sm text-slate-500">
                            Jadwal kerja mingguan yang berlaku untuk seluruh karyawan.
                        </p>

                    </x-wirekit::stack>


                    @can('update-work-time')
                        <x-wirekit::button variant="outline" size="sm" wire:click="toggleEdit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.651-1.651a2.1 2.1 0 013 3l-1.651 1.651M16.862 4.487L7.5 13.849V17h3.151l9.362-9.362m-3-3L21.5 7.5" />
                            </svg>

                            Edit Jadwal
                        </x-wirekit::button>
                    @endcan


                </x-wirekit::row>

            </x-wirekit::card.header>


            {{-- BODY --}}
            <x-wirekit::card.body>

                <x-wirekit::stack gap="sm">


                    @foreach ($times as $id => $time)
                        {{-- SENIN --}}
                        <div
                            class="grid grid-cols-1 gap-4 rounded-xl border border-slate-200 p-4 md:grid-cols-[1fr_220px_220px] md:items-end">

                            <div>
                                <p class="text-sm font-medium text-slate-900">
                                    {{ $time['day_of_week'] }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $time['is_working_day'] ? 'Hari Kerja' : 'Hari libur' }}
                                </p>
                            </div>

                            @if ($time['is_working_day'])
                                <x-wirekit::time-picker label="Jam Mulai" value="{{ $time['start_time'] }}"
                                    name="monday_start" wire:model="updateWorkTime.{{ $id }}.start_time"
                                    format="24" step="60" :disabled="!$is_edit" />

                                <x-wirekit::time-picker label="Jam Selesai" name="monday_end"
                                    value="{{ $time['end_time'] }}"
                                    wire:model="updateWorkTime.{{ $id }}.end_time" format="24"
                                    step="60" :disabled="!$is_edit" />
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                                    Hari Libur
                                </span>
                            @endif



                        </div>
                    @endforeach






                </x-wirekit::stack>

            </x-wirekit::card.body>


            {{-- FOOTER --}}
            @can('update-work-time')
                <x-wirekit::card.footer>

                    <x-wirekit::row justify="end" align="center" gap="sm">


                        <x-wirekit::button type="submit" :disabled="!$is_edit">
                            Simpan Perubahan
                        </x-wirekit::button>

                    </x-wirekit::row>

                </x-wirekit::card.footer>
            @endcan


        </x-wirekit::card>
    </x-wirekit::form>

</div>
