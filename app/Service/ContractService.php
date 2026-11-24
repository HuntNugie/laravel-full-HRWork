<?php

namespace App\Service;

use App\Models\Benefit;
use App\Models\ContractSequence;
use App\Models\EmployeeContract;
use App\Models\Employees;
use App\Models\LeaveType;
use App\Models\Position;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContractService
{
    public function previewContractNumber(): string
    {
        $date = now();
        $last = ContractSequence::where('year', $date->year)->where('month', $date->month)->value('last_number') ?? 0;
        return $this->number($date->year, $date->month, $last + 1);
    }

    private function number(int $year, int $month, int $number): string
    {
        return sprintf('CTR/%04d/%02d/%03d', $year, $month, $number);
    }

    private function nextNumber(): string
    {
        $date = now();
        $keys = ['year' => $date->year, 'month' => $date->month];
        DB::table('contract_sequences')->insertOrIgnore([...$keys, 'last_number' => 0, 'created_at' => $date, 'updated_at' => $date]);
        $sequence = ContractSequence::where($keys)->lockForUpdate()->firstOrFail();
        $sequence->increment('last_number');
        return $this->number($date->year, $date->month, (int) $sequence->last_number);
    }

    public function workingDays(?string $start): int
    {
        try { $month = Carbon::parse($start ?: now())->startOfMonth(); }
        catch (\Throwable) { $month = now()->startOfMonth(); }
        return collect(CarbonPeriod::create($month->copy(), $month->copy()->endOfMonth()))
            ->filter(fn (Carbon $day) => in_array($day->dayOfWeekIso, config('hrwork.contract_workdays'), true))->count();
    }

    public function save(Employees $employee, array $input, ?EmployeeContract $draft = null): EmployeeContract
    {
        if ($draft) { Gate::authorize('update', $draft); }
        else { Gate::authorize('create', EmployeeContract::class); }

        return DB::transaction(function () use ($employee, $input, $draft) {
            $employee = Employees::query()->lockForUpdate()->findOrFail($employee->id);
            if ($draft) {
                $draft = $employee->employeeContract()->lockForUpdate()->findOrFail($draft->id);
                abort_unless($draft->status === 'draft', 403);
                Gate::authorize('update', $draft);
            } elseif ($employee->employeeContract()->where('status', 'draft')->exists()) {
                throw ValidationException::withMessages(['form.statusContract' => 'Selesaikan kontrak draft sebelumnya terlebih dahulu.']);
            }

            $data = Validator::make(['form' => $input], [
                'form.positionId' => ['required', Rule::exists('positions', 'id')->where('is_active', 'active')],
                'form.contractType' => ['required', Rule::in(['pkwt', 'pkwtt', 'intership', 'freelance'])],
                'form.start_date' => ['required', 'date_format:Y-m-d'],
                'form.end_date' => ['nullable', 'required_unless:form.contractType,pkwtt', 'date_format:Y-m-d', 'after:form.start_date'],
                'form.salary_daily' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
                'form.statusContract' => ['required', Rule::in(['draft', 'active'])],
                'form.note' => ['nullable', 'string', 'max:10000'],
                'form.benefitSelect' => ['nullable', 'array'],
                'form.benefitSelect.*' => ['array'],
                'form.benefitSelect.*.selected' => ['sometimes', 'boolean'],
                'form.dayLeave' => ['nullable', 'array'],
                'form.dayLeave.*' => ['required', 'integer', 'min:0', 'max:366'],
            ])->validate()['form'];
            $position = Position::where('is_active', 'active')->findOrFail($data['positionId']);
            $start = Carbon::parse($data['start_date'])->startOfDay();
            $end = $data['contractType'] === 'pkwtt' ? null : Carbon::parse($data['end_date'])->startOfDay();
            $status = $data['statusContract'];

            if ($status === 'active' && ($start->isFuture() || ($end && $end->lt(today())))) {
                throw ValidationException::withMessages(['form.statusContract' => 'Kontrak aktif harus berlaku pada hari ini. Simpan sebagai draft untuk tanggal mulai mendatang.']);
            }

            $benefits = [];
            foreach ($data['benefitSelect'] ?? [] as $id => $selection) {
                if (! ($selection['selected'] ?? false)) { continue; }
                $benefit = Benefit::where('status', 'active')->find($id);
                $amount = $input['benefitSelect'][$id]['amount'] ?? null;
                if (! $benefit || ! is_numeric($amount) || $amount < 0 || $amount > 9999999999.99) {
                    throw ValidationException::withMessages(["form.benefitSelect.$id.amount" => 'Pilih benefit aktif dan nominal yang valid.']);
                }
                $benefits[$id] = ['amount' => $amount, 'name_snapshot' => $benefit->name, 'description_snapshot' => $benefit->description];
            }
            $eligibleLeave = LeaveType::where('status', 'active')->whereIn('gender', ['all', $employee->profile?->gender])->get()->keyBy('id');
            $leave = [];
            foreach ($data['dayLeave'] ?? [] as $id => $days) {
                $type = $eligibleLeave->get($id);
                if (! $type) { throw ValidationException::withMessages(["form.dayLeave.$id" => 'Jenis cuti tidak tersedia untuk karyawan ini.']); }
                $leave[$id] = ['leave_type_id' => $id, 'days' => $days, 'name_snapshot' => $type->name];
            }

            if ($status === 'active') {
                $previous = $employee->employeeContract()->where('status', 'active')->lockForUpdate()->get();
                foreach ($previous as $active) {
                    if ($active->start_date->gte($start)) {
                        throw ValidationException::withMessages(['form.start_date' => 'Tanggal kontrak baru harus setelah tanggal mulai kontrak aktif sebelumnya.']);
                    }
                    if ($active->end_date && $active->end_date->lt($start)) {
                        $active->update(['status' => 'expired', 'ended_at' => $active->end_date, 'end_reason' => 'Masa berlaku berakhir']);
                    } else {
                        $active->update(['status' => 'terminated', 'ended_at' => $start->copy()->subDay(), 'end_reason' => 'Digantikan kontrak baru']);
                    }
                }
                $overlap = $employee->employeeContract()->whereIn('status', ['expired', 'terminated'])
                    ->when($end, fn ($q) => $q->whereDate('start_date', '<=', $end))
                    ->where(fn ($q) => $q->whereDate('ended_at', '>=', $start)->orWhere(fn ($q) => $q->whereNull('ended_at')->whereDate('end_date', '>=', $start)))->exists();
                if ($overlap) { throw ValidationException::withMessages(['form.start_date' => 'Tanggal mulai bertumpang tindih dengan riwayat kontrak.']); }
            }

            $attributes = [
                'position_id' => $position->id, 'position_name' => $position->name,
                'employement_type' => $data['contractType'], 'start_date' => $start, 'end_date' => $end,
                'salary_daily' => $data['salary_daily'], 'status' => $status, 'notes' => $data['note'] ?? null,
            ];
            if ($draft) { $draft->update($attributes); $contract = $draft; }
            else { $contract = $employee->employeeContract()->create([...$attributes, 'contract_number' => $this->nextNumber()]); }
            $contract->benefits()->sync($benefits);
            $contract->contractLeave()->delete();
            foreach ($leave as $entitlement) { $contract->contractLeave()->create($entitlement); }

            if ($status === 'active') {
                $employee->update(['position_id' => $position->id]);
                $this->changeStatus($employee, 'active', $start, 'Aktivasi kontrak', auth()->id());
            }
            return $contract->refresh();
        }, 3);
    }

    public function terminate(EmployeeContract $contract, string $reason): void
    {
        Gate::authorize('terminate-contract');
        Validator::make(['reason' => $reason], ['reason' => 'required|string|max:1000'])->validate();
        DB::transaction(function () use ($contract, $reason) {
            $employee = Employees::lockForUpdate()->findOrFail($contract->employee_id);
            $contract = $employee->employeeContract()->lockForUpdate()->findOrFail($contract->id);
            abort_unless($contract->status === 'active', 422, 'Hanya kontrak aktif yang dapat diakhiri.');
            $contract->update(['status' => 'terminated', 'ended_at' => today(), 'end_reason' => $reason]);
            if (! $employee->employeeContract()->where('status', 'active')->exists()) {
                $this->changeStatus($employee, 'inactive', today(), 'Pengakhiran kontrak: '.$reason, auth()->id());
            }
        }, 3);
    }

    public function expire(EmployeeContract $contract): bool
    {
        return DB::transaction(function () use ($contract) {
            $employee = Employees::lockForUpdate()->findOrFail($contract->employee_id);
            $contract = $employee->employeeContract()->lockForUpdate()->findOrFail($contract->id);
            if ($contract->status !== 'active' || ! $contract->end_date || ! $contract->end_date->lt(today())) { return false; }
            $contract->update(['status' => 'expired', 'ended_at' => $contract->end_date, 'end_reason' => 'Masa berlaku berakhir']);
            if (! $employee->employeeContract()->where('status', 'active')->exists()) {
                $this->changeStatus($employee, 'inactive', $contract->end_date->copy()->addDay(), 'Kontrak kedaluwarsa', null);
            }
            return true;
        }, 3);
    }

    private function changeStatus(Employees $employee, string $new, Carbon $effective, string $reason, ?int $actor): void
    {
        $old = $employee->status_employee;
        if ($old === $new) { return; }
        $employee->update(['status_employee' => $new]);
        $employee->statusHistory()->create(['old_status' => $old, 'new_status' => $new, 'effective_date' => $effective, 'reason' => $reason, 'changed_by' => $actor]);
    }
}
