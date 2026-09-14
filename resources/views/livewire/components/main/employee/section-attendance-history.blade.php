<x-wirekit::card>

    <x-wirekit::card.header>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <x-wirekit::stack gap="1">

                <h2 class="text-lg font-semibold text-slate-900">
                    Riwayat Presensi
                </h2>

                <p class="text-sm text-slate-500">
                    Riwayat kehadiran karyawan berdasarkan bulan.
                </p>

            </x-wirekit::stack>

            {{-- FILTER BULAN --}}
            <div class="w-full sm:w-52">

                <x-wirekit::select wire:model.live="attendanceMonth">

                    <option value="2026-09">
                        September 2026
                    </option>

                    <option value="2026-08">
                        Agustus 2026
                    </option>

                    <option value="2026-07">
                        Juli 2026
                    </option>

                    <option value="2026-06">
                        Juni 2026
                    </option>

                    <option value="2026-05">
                        Mei 2026
                    </option>

                    <option value="2026-04">
                        April 2026
                    </option>

                    <option value="2026-03">
                        Maret 2026
                    </option>

                    <option value="2026-02">
                        Februari 2026
                    </option>

                    <option value="2026-01">
                        Januari 2026
                    </option>

                </x-wirekit::select>

            </div>

        </div>

    </x-wirekit::card.header>


    <x-wirekit::card.body>

        <div class="overflow-hidden rounded-xl border border-slate-200">

            <div class="wk-scrollbar max-h-[500px] overflow-auto">

                <x-wirekit::table hoverable>

                    <x-wirekit::table.head>

                        <x-wirekit::table.row>

                            <x-wirekit::table.th>
                                Tanggal
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Check In
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Check Out
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Durasi
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Status
                            </x-wirekit::table.th>

                            <x-wirekit::table.th>
                                Aksi
                            </x-wirekit::table.th>

                        </x-wirekit::table.row>

                    </x-wirekit::table.head>


                    <x-wirekit::table.body>

                        @forelse ($attendanceHistory as $attendance)
                            <x-wirekit::table.row>

                                <x-wirekit::table.td>
                                    <span class="text-sm font-medium text-slate-800">
                                        {{ $attendance['date'] }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">
                                        {{ $attendance['check_in'] ?? '—' }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">
                                        {{ $attendance['check_out'] ?? '—' }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>
                                    <span class="text-sm text-slate-700">
                                        {{ $attendance['duration'] ?? '—' }}
                                    </span>
                                </x-wirekit::table.td>

                                <x-wirekit::table.td>

                                    @if ($attendance['status'] === 'present')
                                        <x-wirekit::badge variant="success">
                                            Hadir
                                        </x-wirekit::badge>
                                    @elseif ($attendance['status'] === 'late')
                                        <x-wirekit::badge variant="warning">
                                            Terlambat
                                        </x-wirekit::badge>
                                    @else
                                        <x-wirekit::badge variant="outline">
                                            Belum Hadir
                                        </x-wirekit::badge>
                                    @endif

                                </x-wirekit::table.td>

                                <x-wirekit::table.td>

                                    @if ($attendance['attendance_id'])
                                        <livewire:components.main.attendances.modal-detail-attendance :attendance-id="$attendance['attendance_id']"
                                            :key="'employee-attendance-detail-' . $attendance['attendance_id']">

                                            <x-wirekit::button type="button" variant="outline"
                                                class="px-3 py-1.5 text-xs">
                                                Detail
                                            </x-wirekit::button>

                                        </livewire:components.main.attendances.modal-detail-attendance>
                                    @else
                                        <span class="text-sm text-slate-400">
                                            —
                                        </span>
                                    @endif

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>

                        @empty

                            <x-wirekit::table.row>

                                <x-wirekit::table.td colspan="6">

                                    <div class="py-10 text-center">
                                        <span class="text-sm text-slate-500">
                                            Tidak ada data presensi.
                                        </span>
                                    </div>

                                </x-wirekit::table.td>

                            </x-wirekit::table.row>
                        @endforelse

                    </x-wirekit::table.body>

                </x-wirekit::table>

            </div>

        </div>

    </x-wirekit::card.body>

</x-wirekit::card>
