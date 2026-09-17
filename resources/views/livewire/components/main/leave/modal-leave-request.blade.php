<x-wirekit::modal name="create-leave-request">

    <x-slot:trigger>
        {{ $slot }}
    </x-slot:trigger>

    {{-- HEADER --}}
    <x-wirekit::modal.header>
        <x-wirekit::stack gap="xs">

            <h2 class="text-lg font-semibold text-slate-900">
                Ajukan Cuti
            </h2>

            <p class="text-sm text-slate-500">
                Ajukan cuti sesuai dengan hak cuti Anda.
            </p>

        </x-wirekit::stack>
    </x-wirekit::modal.header>


    {{-- BODY --}}
    <x-wirekit::form wire:submit="submit">

        <x-wirekit::modal.body>

            <x-wirekit::stack gap="md">

                {{-- JENIS CUTI --}}
                <x-wirekit::select label="Jenis Cuti" name="leaveTypeId" wire:model.live="leaveTypeId">
                    <option value="">
                        Pilih jenis cuti
                    </option>

                    @foreach ($entitlements as $entitlement)
                        <option value="{{ $entitlement->leave_type_id }}">
                            {{ $entitlement->leaveType?->name }}
                            — Sisa
                            {{ $this->remainingLeave($entitlement) }}
                            Hari
                        </option>
                    @endforeach
                </x-wirekit::select>


                {{-- SISA CUTI --}}
                @if ($this->selectedEntitlement)
                    <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">

                        <div class="flex items-center justify-between gap-4">

                            <div class="flex items-center gap-3">

                                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white">
                                    <x-wirekit::icon name="calendar" class="size-5 text-sky-500" />
                                </div>

                                <div>

                                    <p class="text-xs font-medium text-slate-500">
                                        Sisa Cuti
                                    </p>

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $this->remainingLeave($this->selectedEntitlement) }}
                                        Hari
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>
                @endif


                {{-- TANGGAL --}}
                <div class="grid gap-4 sm:grid-cols-2">

                    <x-wirekit::input type="date" label="Tanggal Mulai" wire:model.live="startDate"
                        name="startDate" />

                    <x-wirekit::input type="date" label="Tanggal Selesai" wire:model.live="endDate"
                        name="endDate " />

                </div>


                {{-- DURASI --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-xs font-medium text-slate-500">
                                Durasi Cuti
                            </p>

                            <p class="mt-1 text-lg font-semibold text-slate-800">
                                {{ $totalDays }} Hari
                            </p>

                        </div>

                        <x-wirekit::icon name="clock" class="size-5 text-slate-400" />

                    </div>

                </div>


                {{-- ALASAN --}}
                <x-wirekit::textarea label="Alasan" placeholder="Jelaskan alasan pengajuan cuti Anda..." name="reason"
                    rows="4" wire:model="reason" />

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

                <x-wirekit::button type="submit">

                    <span wire:loading.remove wire:target="submit">
                        Ajukan
                    </span>

                    <span wire:loading wire:target="submit">
                        Mohon tunggu sebentar
                    </span>

                </x-wirekit::button>

            </x-wirekit::row>

        </x-wirekit::modal.footer>

    </x-wirekit::form>

</x-wirekit::modal>
