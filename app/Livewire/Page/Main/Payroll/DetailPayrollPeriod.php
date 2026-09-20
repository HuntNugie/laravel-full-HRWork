<?php

namespace App\Livewire\Page\Main\Payroll;

use App\Models\Employees;
use App\Service\EmployeeDailyStatusService;
use App\Models\LateDisciplineRule;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman detail periode payroll'])]
class DetailPayrollPeriod extends Component
{
    use WithPagination;

    public PayrollPeriod $period;

    public string $search = '';

    public int $perPage = 10;

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(PayrollPeriod $period): void
    {
        abort_unless(
            Auth::user()->can('show-payroll'),
            403
        );

        $this->period = $period->load([
            'creator',
            'processor',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | COMPUTED: ELIGIBLE EMPLOYEES
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function eligibleEmployees()
    {
        return Employees::query()
            ->with([
                'user',
                'employeeContract.benefits',
            ])
            ->whereHas('user.roles', function (Builder $query) {
                $query->where('name', 'employee');
            })
            ->whereHas('employeeContract', function (Builder $query) {
                $query
                    ->where('status', 'active')
                    ->whereDate(
                        'start_date',
                        '<=',
                        $this->period->end_date
                    )
                    ->where(function (Builder $query) {
                        $query
                            ->whereNull('end_date')
                            ->orWhereDate(
                                'end_date',
                                '>=',
                                $this->period->start_date
                            );
                    });
            })
            ->get();
    }

    #[Computed]
    public function eligibleEmployeeCount(): int
    {
        return $this->eligibleEmployees->count();
    }

    #[Computed]
    public function generatedEmployeeCount(): int
    {
        return $this->period
            ->payrolls()
            ->count();
    }

    #[Computed]
    public function missingEmployeeCount(): int
    {
        $eligibleEmployeeIds = $this->eligibleEmployees
            ->pluck('id')
            ->unique();

        if ($eligibleEmployeeIds->isEmpty()) {
            return 0;
        }

        $generatedEmployeeIds = $this->period
            ->payrolls()
            ->pluck('employee_id')
            ->unique();

        return $eligibleEmployeeIds
            ->diff($generatedEmployeeIds)
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE / SYNC PAYROLL
    |--------------------------------------------------------------------------
    |
    | Full reconciliation untuk payroll periode berstatus draft.
    |
    | - Eligible baru        -> CREATE payroll baru
    | - Masih eligible       -> RECALCULATE / UPDATE payroll lama
    | - Tidak eligible lagi  -> DELETE payroll lama
    | - Payroll item manual  -> DIPERTAHANKAN
    | - Payroll item system  -> DIBANGUN ULANG
    |
    */

    public function generatePayroll(): void
    {
        abort_unless(
            Auth::user()->can('create-period-payroll'),
            403
        );

        $this->period->refresh();

        if ($this->period->status !== 'draft') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Payroll hanya dapat dibuat pada periode dengan status draft.'
            );

            return;
        }

        try {
            $result = $this->syncPayrollForPeriod();
        } catch (\RuntimeException $e) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: $e->getMessage()
            );

            return;
        }

        unset(
            $this->payrolls,
            $this->summary,
            $this->eligibleEmployees,
            $this->eligibleEmployeeCount,
            $this->generatedEmployeeCount,
            $this->missingEmployeeCount,
            $this->globalPayrollItems
        );

        $this->period->refresh();

        $this->dispatch('payroll-period-refresh');

        $messages = [];

        if ($result['created'] > 0) {
            $messages[] = "{$result['created']} karyawan baru dibuatkan payroll";
        }

        if ($result['updated'] > 0) {
            $messages[] = "{$result['updated']} payroll dihitung ulang";
        }

        if ($result['deleted'] > 0) {
            $messages[] = "{$result['deleted']} payroll yang tidak lagi eligible dihapus";
        }

        if ($messages === []) {
            $messages[] = 'Payroll sudah sinkron dengan periode saat ini';
        }

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: implode('. ', $messages) . '.'
        );
    }

    /**
     * Sinkronisasi penuh payroll terhadap periode terbaru.
     *
     * Method ini sengaja tidak memakai raw SQL. Semua operasi menggunakan
     * Eloquent model yang memang sudah digunakan oleh project ini.
     *
     * @return array{created:int,updated:int,deleted:int}
     */
    private function syncPayrollForPeriod(): array
    {
        return DB::transaction(function (): array {
            $period = PayrollPeriod::query()
                ->whereKey($this->period->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($period->status !== 'draft') {
                throw new \RuntimeException(
                    'Payroll periode sudah tidak dalam status draft.'
                );
            }

            $lateRule = LateDisciplineRule::query()->first();

            if (!$lateRule) {
                throw new \RuntimeException(
                    'Aturan keterlambatan belum dikonfigurasi.'
                );
            }

            $lateThreshold = (int) $lateRule->threshold;
            $lateActionAmount = (float) $lateRule->action_amount;

            if ($lateThreshold < 1) {
                throw new \RuntimeException(
                    'Threshold keterlambatan harus lebih besar dari 0.'
                );
            }

            $employees = $this->eligibleEmployeesQueryForPeriod($period)
                ->get();

            $eligibleEmployeeIds = $employees
                ->pluck('id')
                ->unique()
                ->values();

            $existingPayrolls = Payroll::query()
                ->where('payroll_period_id', $period->id)
                ->lockForUpdate()
                ->get()
                ->keyBy('employee_id');

            /*
            |----------------------------------------------------------------------
            | Snapshot global manual items sebelum payroll yang tidak eligible
            | dihapus. Ini diperlukan agar payroll baru yang masuk setelah periode
            | diperbesar tetap mendapatkan komponen global yang sama.
            |----------------------------------------------------------------------
            */

            $existingPayrollIds = $existingPayrolls
                ->pluck('id')
                ->values();

            $globalItems = collect();

            if ($existingPayrollIds->isNotEmpty()) {
                $globalItems = PayrollItem::query()
                    ->whereIn('payroll_id', $existingPayrollIds)
                    ->where('category', 'global')
                    ->where('source', 'manual')
                    ->orderBy('id')
                    ->get()
                    ->unique('name')
                    ->values();
            }

            $dailyStatusService = app(EmployeeDailyStatusService::class);

            $created = 0;
            $updated = 0;
            $deleted = 0;

            /*
            |----------------------------------------------------------------------
            | DELETE PAYROLL YANG SUDAH TIDAK ELIGIBLE
            |----------------------------------------------------------------------
            */

            $extraEmployeeIds = $existingPayrolls
                ->keys()
                ->diff($eligibleEmployeeIds);

            if ($extraEmployeeIds->isNotEmpty()) {
                Payroll::query()
                    ->where('payroll_period_id', $period->id)
                    ->whereIn('employee_id', $extraEmployeeIds)
                    ->delete();

                $deleted = $extraEmployeeIds->count();
            }

            foreach ($employees as $employee) {
                $contract = $employee->employeeContract
                    ->filter(function ($contract) use ($period) {
                        if ($contract->status !== 'active') {
                            return false;
                        }

                        if ($contract->start_date->gt($period->end_date)) {
                            return false;
                        }

                        if (
                            $contract->end_date &&
                            $contract->end_date->lt($period->start_date)
                        ) {
                            return false;
                        }

                        return true;
                    })
                    ->sortByDesc('start_date')
                    ->first();

                if (!$contract) {
                    continue;
                }

                $calculation = $this->calculateEmployeePayroll(
                    employee: $employee,
                    contract: $contract,
                    period: $period,
                    dailyStatusService: $dailyStatusService,
                    lateThreshold: $lateThreshold,
                    lateActionAmount: $lateActionAmount,
                );

                $existingPayroll = $existingPayrolls->get($employee->id);

                if ($existingPayroll) {
                    /*
                    |------------------------------------------------------------------
                    | Pertahankan seluruh item manual milik payroll ini.
                    |------------------------------------------------------------------
                    */

                    $manualItems = PayrollItem::query()
                        ->where('payroll_id', $existingPayroll->id)
                        ->where('source', 'manual')
                        ->get();

                    $manualEarningTotal = $manualItems
                        ->where('type', 'earning')
                        ->sum(fn(PayrollItem $item) => (float) $item->amount);

                    $manualDeductionTotal = $manualItems
                        ->where('type', 'deduction')
                        ->sum(fn(PayrollItem $item) => (float) $item->amount);

                    /*
                    |------------------------------------------------------------------
                    | Item system lama harus dibuang supaya tidak terduplikasi.
                    |------------------------------------------------------------------
                    */

                    PayrollItem::query()
                        ->where('payroll_id', $existingPayroll->id)
                        ->where('source', 'system')
                        ->delete();

                    $grossAmount =
                        $calculation['salary_amount']
                        + $calculation['benefit_total']
                        + $manualEarningTotal;

                    $deductionAmount =
                        $calculation['late_deduction_total']
                        + $manualDeductionTotal;

                    $netAmount = $grossAmount - $deductionAmount;

                    $existingPayroll->update([
                        'employee_contract_id' => $contract->id,
                        'position_name' => $contract->position_name,
                        'salary_daily' => $calculation['salary_daily'],
                        'working_days' => $calculation['working_days'],
                        'present_days' => $calculation['present_days'],
                        'late_days' => $calculation['late_days'],
                        'absent_days' => $calculation['absent_days'],
                        'paid_leave_days' => $calculation['paid_leave_days'],
                        'unpaid_leave_days' => 0,
                        'paid_days' => $calculation['paid_days'],
                        'gross_amount' => $grossAmount,
                        'deduction_amount' => $deductionAmount,
                        'net_amount' => $netAmount,
                    ]);

                    $this->createSystemPayrollItems(
                        payroll: $existingPayroll,
                        calculation: $calculation,
                    );

                    $updated++;

                    continue;
                }

                /*
                |----------------------------------------------------------------------
                | Payroll baru
                |----------------------------------------------------------------------
                */

                $globalEarningTotal = $globalItems
                    ->where('type', 'earning')
                    ->sum(fn(PayrollItem $item) => (float) $item->amount);

                $globalDeductionTotal = $globalItems
                    ->where('type', 'deduction')
                    ->sum(fn(PayrollItem $item) => (float) $item->amount);

                $grossAmount =
                    $calculation['salary_amount']
                    + $calculation['benefit_total']
                    + $globalEarningTotal;

                $deductionAmount =
                    $calculation['late_deduction_total']
                    + $globalDeductionTotal;

                $netAmount = $grossAmount - $deductionAmount;

                $payroll = Payroll::create([
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                    'employee_contract_id' => $contract->id,
                    'position_name' => $contract->position_name,
                    'salary_daily' => $calculation['salary_daily'],
                    'working_days' => $calculation['working_days'],
                    'present_days' => $calculation['present_days'],
                    'late_days' => $calculation['late_days'],
                    'absent_days' => $calculation['absent_days'],
                    'paid_leave_days' => $calculation['paid_leave_days'],
                    'unpaid_leave_days' => 0,
                    'paid_days' => $calculation['paid_days'],
                    'gross_amount' => $grossAmount,
                    'deduction_amount' => $deductionAmount,
                    'net_amount' => $netAmount,
                    'status' => 'draft',
                    'notes' => null,
                    'processed_at' => null,
                    'paid_at' => null,
                ]);

                $this->createSystemPayrollItems(
                    payroll: $payroll,
                    calculation: $calculation,
                );

                /*
                |----------------------------------------------------------------------
                | Clone komponen payroll global untuk employee baru.
                |----------------------------------------------------------------------
                */

                $sortOrder = $calculation['next_sort_order'];

                foreach ($globalItems as $globalItem) {
                    PayrollItem::create([
                        'payroll_id' => $payroll->id,
                        'name' => $globalItem->name,
                        'type' => $globalItem->type,
                        'category' => 'global',
                        'amount' => $globalItem->amount,
                        'quantity' => $globalItem->quantity,
                        'rate' => $globalItem->rate,
                        'source' => 'manual',
                        'description' => $globalItem->description,
                        'sort_order' => $sortOrder++,
                    ]);
                }

                $created++;
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'deleted' => $deleted,
            ];
        });
    }

    /**
     * Query employee eligible berdasarkan PayrollPeriod tertentu.
     * Dipisahkan agar proses sync tidak bergantung pada computed property Livewire.
     */
    private function eligibleEmployeesQueryForPeriod(PayrollPeriod $period): Builder
    {
        return Employees::query()
            ->with([
                'user',
                'employeeContract.benefits',
            ])
            ->whereHas('user.roles', function (Builder $query) {
                $query->where('name', 'employee');
            })
            ->whereHas('employeeContract', function (Builder $query) use ($period) {
                $query
                    ->where('status', 'active')
                    ->whereDate(
                        'start_date',
                        '<=',
                        $period->end_date
                    )
                    ->where(function (Builder $query) use ($period) {
                        $query
                            ->whereNull('end_date')
                            ->orWhereDate(
                                'end_date',
                                '>=',
                                $period->start_date
                            );
                    });
            });
    }

    /**
     * Hitung angka payroll untuk satu employee.
     * Belum memasukkan payroll item manual/global.
     *
     * @return array{
     *   salary_daily:float,
     *   salary_amount:float,
     *   working_days:int,
     *   present_days:int,
     *   late_days:int,
     *   absent_days:int,
     *   paid_leave_days:int,
     *   paid_days:int,
     *   benefit_total:float,
     *   late_deduction_total:float,
     *   late_deduction_items:Collection<int,array{month:string,late_count:int,deduction_units:int,amount:float}>,
     *   benefit_items:Collection<int,array{benefit:object,amount:float,quantity:int,rate:float}>,
     *   next_sort_order:int
     * }
     */
    private function calculateEmployeePayroll(
        Employees $employee,
        $contract,
        PayrollPeriod $period,
        EmployeeDailyStatusService $dailyStatusService,
        int $lateThreshold,
        float $lateActionAmount,
    ): array {
        $eligibleStart = $contract->start_date->greaterThan($period->start_date)
            ? $contract->start_date->copy()
            : $period->start_date->copy();

        $eligibleEnd = $contract->end_date && $contract->end_date->lessThan($period->end_date)
            ? $contract->end_date->copy()
            : $period->end_date->copy();

        if ($eligibleStart->gt($eligibleEnd)) {
            return [
                'salary_daily' => (float) $contract->salary_daily,
                'salary_amount' => 0.0,
                'working_days' => 0,
                'present_days' => 0,
                'late_days' => 0,
                'absent_days' => 0,
                'paid_leave_days' => 0,
                'paid_days' => 0,
                'benefit_total' => 0.0,
                'late_deduction_total' => 0.0,
                'late_deduction_items' => collect(),
                'benefit_items' => collect(),
                'next_sort_order' => 2,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DAILY STATUS
        |--------------------------------------------------------------------------
        |
        | EmployeeDailyStatusService menjadi satu-satunya sumber penentuan
        | status kalender/attendance/leave/absence untuk payroll.
        |
        */

        $statuses = $dailyStatusService->getStatuses(
            employee: $employee,
            startDate: $eligibleStart,
            endDate: $eligibleEnd,
        );

        $workingDays = $statuses
            ->filter(fn(array $state) => $state['is_working_day'] === true)
            ->count();

        $presentDays = $statuses
            ->filter(
                fn(array $state) =>
                    in_array(
                        $state['status'],
                        [
                            EmployeeDailyStatusService::STATUS_PRESENT,
                            EmployeeDailyStatusService::STATUS_LATE,
                        ],
                        true
                    )
            )
            ->count();

        $lateStatuses = $statuses
            ->filter(fn(array $state) => $state['is_late'] === true)
            ->values();

        $lateDays = $lateStatuses->count();

        $paidLeaveDays = $statuses
            ->filter(
                fn(array $state) =>
                    $state['status'] === EmployeeDailyStatusService::STATUS_PAID_LEAVE
            )
            ->count();

        $paidDays = $statuses
            ->filter(fn(array $state) => $state['is_paid'] === true)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | LATE DISCIPLINE
        |--------------------------------------------------------------------------
        |
        | Hanya status late yang masuk ke perhitungan potongan keterlambatan.
        |
        */

        $lateDeductionTotal = 0.0;

        $lateDeductionItems = $lateStatuses
            ->groupBy(
                fn(array $state) => Carbon::parse($state['date'])->format('Y-m')
            )
            ->map(
                function ($states, string $monthKey) use (
                    $lateThreshold,
                    $lateActionAmount,
                    &$lateDeductionTotal
                ) {
                    $lateCount = $states->count();
                    $deductionUnits = intdiv(
                        $lateCount,
                        $lateThreshold
                    );

                    if ($deductionUnits <= 0) {
                        return null;
                    }

                    $amount = $deductionUnits * $lateActionAmount;
                    $lateDeductionTotal += $amount;

                    return [
                        'month' => $monthKey,
                        'late_count' => $lateCount,
                        'deduction_units' => $deductionUnits,
                        'amount' => $amount,
                    ];
                }
            )
            ->filter()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | PAYROLL TOTALS
        |--------------------------------------------------------------------------
        |
        | absent_days tetap berarti seluruh hari kerja yang tidak dibayar.
        | Jadi field ini sengaja tidak diubah menjadi "unpresent_days".
        |
        */

        $absentDays = max(
            0,
            $workingDays - $paidDays
        );

        $salaryDaily = (float) $contract->salary_daily;
        $salaryAmount = $paidDays * $salaryDaily;

        $benefitTotal = 0.0;
        $benefitItems = collect();

        foreach ($contract->benefits as $benefit) {
            $benefitDaily = (float) (
                $benefit->pivot->amount ?? 0
            );

            $benefitAmount = $paidDays * $benefitDaily;

            if ($benefitAmount <= 0) {
                continue;
            }

            $benefitTotal += $benefitAmount;

            $benefitItems->push([
                'benefit' => $benefit,
                'amount' => $benefitAmount,
                'quantity' => $paidDays,
                'rate' => $benefitDaily,
            ]);
        }

        return [
            'salary_daily' => $salaryDaily,
            'salary_amount' => $salaryAmount,
            'working_days' => $workingDays,
            'present_days' => $presentDays,
            'late_days' => $lateDays,
            'absent_days' => $absentDays,
            'paid_leave_days' => $paidLeaveDays,
            'paid_days' => $paidDays,
            'benefit_total' => $benefitTotal,
            'late_deduction_total' => $lateDeductionTotal,
            'late_deduction_items' => $lateDeductionItems,
            'benefit_items' => $benefitItems,
            'next_sort_order' => 2
                + $benefitItems->count()
                + $lateDeductionItems->count(),
        ];
    }

    /**
     * Rebuild hanya payroll item yang bersumber dari system.
     */
    private function createSystemPayrollItems(
        Payroll $payroll,
        array $calculation,
    ): void {
        if ($calculation['salary_amount'] > 0) {
            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'name' => 'Gaji Harian',
                'type' => 'earning',
                'category' => 'salary',
                'amount' => $calculation['salary_amount'],
                'quantity' => $calculation['paid_days'],
                'rate' => $calculation['salary_daily'],
                'source' => 'system',
                'description' =>
                "Gaji harian {$calculation['paid_days']} hari × Rp" .
                    number_format(
                        $calculation['salary_daily'],
                        0,
                        ',',
                        '.'
                    ),
                'sort_order' => 1,
            ]);
        }

        $sortOrder = 2;

        foreach ($calculation['benefit_items'] as $benefitItem) {
            $benefit = $benefitItem['benefit'];

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'name' => $benefit->name,
                'type' => 'earning',
                'category' => 'benefit',
                'amount' => $benefitItem['amount'],
                'quantity' => $benefitItem['quantity'],
                'rate' => $benefitItem['rate'],
                'source' => 'system',
                'description' =>
                "Tunjangan {$benefitItem['quantity']} hari × Rp" .
                    number_format(
                        $benefitItem['rate'],
                        0,
                        ',',
                        '.'
                    ),
                'sort_order' => $sortOrder++,
            ]);
        }

        foreach ($calculation['late_deduction_items'] as $lateDeduction) {
            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'name' => 'Potongan Keterlambatan',
                'type' => 'deduction',
                'category' => 'late',
                'amount' => $lateDeduction['amount'],
                'quantity' => $lateDeduction['deduction_units'],
                'rate' => $lateDeduction['amount'] / $lateDeduction['deduction_units'],
                'source' => 'system',
                'description' =>
                "Keterlambatan {$lateDeduction['late_count']} kali pada " .
                    $lateDeduction['month'] .
                    " menghasilkan {$lateDeduction['deduction_units']} × Rp" .
                    number_format(
                        $lateDeduction['amount'] / $lateDeduction['deduction_units'],
                        0,
                        ',',
                        '.'
                    ),
                'sort_order' => $sortOrder++,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | COMPUTED: PAYROLL LIST
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function payrolls()
    {
        return $this->period
            ->payrolls()
            ->with([
                'employees.user',
            ])
            ->when(
                filled($this->search),
                function ($query) {
                    $search = trim($this->search);

                    $query->whereHas('employees', function ($employeeQuery) use ($search) {
                        $employeeQuery
                            ->where('employee_code', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            });
                    });
                }
            )
            ->orderBy('id')
            ->paginate($this->perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | COMPUTED: SUMMARY
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function summary(): array
    {
        $payrolls = $this->period->payrolls();

        return [
            'employee_count' => $payrolls->count(),
            'gross_amount' => (float) $payrolls->sum('gross_amount'),
            'deduction_amount' => (float) $payrolls->sum('deduction_amount'),
            'net_amount' => (float) $payrolls->sum('net_amount'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | REFRESH AFTER PERIOD UPDATE
    |--------------------------------------------------------------------------
    |
    | Event ini sudah dipakai oleh komponen edit periode yang sekarang.
    | Saat payroll sudah pernah dibuat dan periode masih draft, lakukan full
    | synchronization agar snapshot payroll mengikuti tanggal baru.
    |
    */

    #[On('payroll-period-updated')]
    public function refreshPeriod(): void
    {
        $this->period->refresh();

        if (
            $this->period->status === 'draft' &&
            Auth::user()->can('edit-period-payroll') &&
            $this->period->payrolls()->exists()
        ) {
            try {
                $this->syncPayrollForPeriod();
            } catch (\RuntimeException $e) {
                $this->dispatch(
                    'wirekit-toast',
                    variant: 'danger',
                    title: 'Payroll Belum Sinkron',
                    message: $e->getMessage()
                );
            }
        }

        $this->period->refresh();
        $this->period->load([
            'creator',
            'processor',
        ]);

        unset(
            $this->eligibleEmployees,
            $this->eligibleEmployeeCount,
            $this->generatedEmployeeCount,
            $this->missingEmployeeCount,
            $this->payrolls,
            $this->summary,
            $this->globalPayrollItems
        );

        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | PROCESS PAYROLL
    |--------------------------------------------------------------------------
    */

    public function processPayroll(): void
    {
        abort_unless(
            Auth::user()->can('process-payroll'),
            403
        );

        $this->period->refresh();

        if ($this->period->status !== 'draft') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Payroll periode ini sudah tidak dapat diproses.'
            );

            return;
        }

        $payrollCount = $this->period
            ->payrolls()
            ->count();

        if ($payrollCount === 0) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Belum ada payroll karyawan pada periode ini.'
            );

            return;
        }

        $eligibleEmployeeIds = $this->eligibleEmployees
            ->pluck('id')
            ->unique();

        $generatedEmployeeIds = $this->period
            ->payrolls()
            ->pluck('employee_id')
            ->unique();

        $missingEmployeeIds = $eligibleEmployeeIds->diff($generatedEmployeeIds);
        $extraPayrollEmployeeIds = $generatedEmployeeIds->diff($eligibleEmployeeIds);

        if ($missingEmployeeIds->isNotEmpty() || $extraPayrollEmployeeIds->isNotEmpty()) {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Payroll Belum Sinkron',
                message: sprintf(
                    'Payroll belum sinkron. %d karyawan eligible belum memiliki payroll dan %d payroll berasal dari karyawan yang saat ini tidak lagi eligible.',
                    $missingEmployeeIds->count(),
                    $extraPayrollEmployeeIds->count()
                )
            );

            return;
        }

        DB::transaction(function () {
            $period = PayrollPeriod::query()
                ->whereKey($this->period->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($period->status !== 'draft') {
                abort(422, 'Payroll periode sudah tidak dalam status draft.');
            }

            $eligibleEmployeeIds = $this->eligibleEmployees
                ->pluck('id')
                ->unique();

            $generatedEmployeeIds = $period
                ->payrolls()
                ->pluck('employee_id')
                ->unique();

            if (
                $eligibleEmployeeIds->diff($generatedEmployeeIds)->isNotEmpty()
                || $generatedEmployeeIds->diff($eligibleEmployeeIds)->isNotEmpty()
            ) {
                abort(
                    422,
                    'Jumlah dan cakupan payroll karyawan belum sinkron untuk diproses.'
                );
            }

            $period->update([
                'status' => 'processing',
            ]);

            $period->payrolls()
                ->lockForUpdate()
                ->get();

            $period->payrolls()->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);

            $period->update([
                'status' => 'processed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
        });

        $this->period->refresh();

        unset(
            $this->payrolls,
            $this->summary,
            $this->generatedEmployeeCount,
            $this->missingEmployeeCount
        );

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Payroll berhasil diproses dan dikunci.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS PAID - INDIVIDUAL
    |--------------------------------------------------------------------------
    */

    public function markPayrollAsPaid(int $payrollId): void
    {
        abort_unless(
            Auth::user()->can('mark-paid-payroll'),
            403
        );

        $this->period->refresh();

        if ($this->period->status !== 'processed') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Payroll hanya dapat ditandai dibayar setelah periode diproses.'
            );

            return;
        }

        DB::transaction(function () use ($payrollId) {
            $period = PayrollPeriod::query()
                ->whereKey($this->period->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($period->status !== 'processed') {
                abort(
                    422,
                    'Payroll periode sudah tidak dalam status processed.'
                );
            }

            $payroll = Payroll::query()
                ->where('payroll_period_id', $period->id)
                ->whereKey($payrollId)
                ->lockForUpdate()
                ->first();

            if (!$payroll) {
                abort(
                    404,
                    'Payroll karyawan tidak ditemukan dalam periode ini.'
                );
            }

            if ($payroll->status === 'paid') {
                abort(
                    422,
                    'Payroll karyawan ini sudah ditandai sebagai dibayar.'
                );
            }

            if ($payroll->status !== 'processed') {
                abort(
                    422,
                    'Payroll karyawan harus berstatus processed sebelum dibayar.'
                );
            }

            $paidAt = now();

            $payroll->update([
                'status' => 'paid',
                'paid_at' => $paidAt,
            ]);

            $remainingUnpaid = $period
                ->payrolls()
                ->where('status', '!=', 'paid')
                ->count();

            if ($remainingUnpaid === 0) {
                $period->update([
                    'status' => 'paid',
                    'paid_at' => $paidAt,
                ]);
            }
        });

        unset(
            $this->payrolls,
            $this->summary
        );

        $this->period->refresh();

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Payroll karyawan berhasil ditandai sebagai sudah dibayar.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL PAYROLL ITEMS
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function globalPayrollItems(): Collection
    {
        $payrollIds = Payroll::query()
            ->where('payroll_period_id', $this->period->id)
            ->pluck('id');

        if ($payrollIds->isEmpty()) {
            return collect();
        }

        return PayrollItem::query()
            ->whereIn('payroll_id', $payrollIds)
            ->where('category', 'global')
            ->where('source', 'manual')
            ->orderBy('id')
            ->get()
            ->unique('name')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS PAID - ALL
    |--------------------------------------------------------------------------
    */

    public function markAsPaid(): void
    {
        abort_unless(
            Auth::user()->can('mark-paid-payroll'),
            403
        );

        $this->period->refresh();

        if ($this->period->status !== 'processed') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Payroll hanya dapat ditandai dibayar setelah diproses.'
            );

            return;
        }

        DB::transaction(function () {
            $period = PayrollPeriod::query()
                ->whereKey($this->period->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($period->status !== 'processed') {
                abort(422, 'Payroll periode sudah tidak dalam status processed.');
            }

            $paidAt = now();

            $period->payrolls()->update([
                'status' => 'paid',
                'paid_at' => $paidAt,
            ]);

            $period->update([
                'status' => 'paid',
                'paid_at' => $paidAt,
            ]);
        });

        $this->period->refresh();

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Payroll periode berhasil ditandai sebagai sudah dibayar.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE PERIOD
    |--------------------------------------------------------------------------
    */

    public function deletePeriod(): void
    {
        abort_unless(
            Auth::user()->can('delete-period-payroll'),
            403
        );

        $this->period->refresh();

        if ($this->period->status !== 'draft') {
            $this->dispatch(
                'wirekit-toast',
                variant: 'danger',
                title: 'Gagal',
                message: 'Hanya payroll periode berstatus draft yang dapat dihapus.'
            );

            return;
        }

        DB::transaction(function () {
            $this->period->delete();
        });

        session()->flash(
            'success',
            'Payroll periode berhasil dihapus.'
        );

        $this->redirectRoute(
            'payroll.view',
            navigate: true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function statusLabel(): string
    {
        return match ($this->period->status) {
            'draft' => 'Draft',
            'processing' => 'Processing',
            'processed' => 'Processed',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->period->status),
        };
    }

    public function money(float|int|string|null $value): string
    {
        return 'Rp' . number_format(
            (float) ($value ?? 0),
            0,
            ',',
            '.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        return view(
            'livewire.page.main.payroll.detail-payroll-period'
        );
    }
}