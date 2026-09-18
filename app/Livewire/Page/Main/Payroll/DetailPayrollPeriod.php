<?php

namespace App\Livewire\Page\Main\Payroll;

use App\Models\Attendances;
use App\Models\Employees;
use App\Models\Holidays;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\WorkTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
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
        return max(
            0,
            $this->eligibleEmployeeCount - $this->generatedEmployeeCount
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE PAYROLL
    |--------------------------------------------------------------------------
    |
    | Generate membuat payroll dalam status draft.
    |
    | Perhitungan saat ini:
    | - paid_days = hari hadir
    | - gaji harian = paid_days × salary_daily
    | - semua benefit yang tercantum pada contract dihitung per hari
    | - benefit = paid_days × contract benefit amount
    | - gross = gaji harian + seluruh benefit earning
    | - deduction = 0 karena potongan manual belum dibuat
    |
    | Cuti / izin / sakit belum dimasukkan ke paid_days sampai aturan
    | paid / unpaid dikunci.
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
                'toast',
                type: 'error',
                message: 'Payroll hanya dapat dibuat pada periode dengan status draft.'
            );

            return;
        }

        $existingPayrollCount = $this->period
            ->payrolls()
            ->count();

        if ($existingPayrollCount > 0) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Payroll untuk periode ini sudah pernah dibuat.'
            );

            return;
        }

        $employees = $this->eligibleEmployees;

        if ($employees->isEmpty()) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Tidak ada karyawan yang memenuhi syarat untuk payroll periode ini.'
            );

            return;
        }

        DB::transaction(function () use ($employees) {
            /*
            |------------------------------------------------------------------
            | LOCK PERIOD
            |------------------------------------------------------------------
            */

            $period = PayrollPeriod::query()
                ->whereKey($this->period->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($period->status !== 'draft') {
                abort(
                    422,
                    'Payroll periode sudah tidak dalam status draft.'
                );
            }

            foreach ($employees as $employee) {
                /*
                |------------------------------------------------------------------
                | ACTIVE CONTRACT YANG BERLAKU PADA PERIOD
                |------------------------------------------------------------------
                */

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

                /*
                |------------------------------------------------------------------
                | TANGGAL ELIGIBLE
                |------------------------------------------------------------------
                */

                $eligibleStart = $contract->start_date->greaterThan(
                    $period->start_date
                )
                    ? $contract->start_date->copy()
                    : $period->start_date->copy();

                $eligibleEnd = $contract->end_date &&
                    $contract->end_date->lessThan($period->end_date)
                    ? $contract->end_date->copy()
                    : $period->end_date->copy();

                if ($eligibleStart->gt($eligibleEnd)) {
                    continue;
                }

                /*
                |------------------------------------------------------------------
                | WORKING DAYS
                |------------------------------------------------------------------
                |
                | Untuk saat ini HRWork menggunakan:
                | Senin - Sabtu = hari kerja
                | Minggu       = libur
                |
                */

                /*
|--------------------------------------------------------------------------
| WORK TIME
|--------------------------------------------------------------------------
| Ambil konfigurasi hari kerja dari master WorkTime.
|
*/

                $workTimes = WorkTime::query()
                    ->get()
                    ->keyBy('day_of_week');

                $holidayDates = Holidays::query()
                    ->whereBetween('date', [
                        $eligibleStart->toDateString(),
                        $eligibleEnd->toDateString(),
                    ])
                    ->pluck('date')
                    ->map(fn($date) => Carbon::parse($date)->toDateString())
                    ->flip();

                $dayNames = [
                    1 => 'senin',
                    2 => 'selasa',
                    3 => 'rabu',
                    4 => 'kamis',
                    5 => 'jumat',
                    6 => 'sabtu',
                    7 => 'minggu',
                ];

                /*
|--------------------------------------------------------------------------
| WORKING DAYS
|--------------------------------------------------------------------------
| Hari kerja ditentukan oleh WorkTime.
| Hari yang tercatat di Holidays dikeluarkan.
|
*/

                $workingDates = collect(
                    CarbonPeriod::create($eligibleStart, $eligibleEnd)
                )->filter(function (Carbon $date) use (
                    $workTimes,
                    $holidayDates,
                    $dayNames
                ) {
                    $dayName = $dayNames[$date->dayOfWeekIso];

                    $workTime = $workTimes->get($dayName);

                    if (!$workTime) {
                        return false;
                    }

                    if (!$workTime->is_working_day) {
                        return false;
                    }

                    if ($holidayDates->has($date->toDateString())) {
                        return false;
                    }

                    return true;
                })->values();

                $workingDays = $workingDates->count();

                /*
                |------------------------------------------------------------------
                | ATTENDANCE
                |------------------------------------------------------------------
                */

                $attendances = Attendances::query()
                    ->where('employee_id', $employee->id)
                    ->whereBetween('date', [
                        $eligibleStart->toDateString(),
                        $eligibleEnd->toDateString(),
                    ])
                    ->whereNotNull('check_in_at')
                    ->get();

                $presentDates = $attendances
                    ->pluck('date')
                    ->map(function ($date) {
                        return Carbon::parse($date)->toDateString();
                    })
                    ->filter(function (string $date) use ($workingDates) {
                        return $workingDates->contains(
                            fn(Carbon $workingDate) =>
                            $workingDate->toDateString() === $date
                        );
                    })
                    ->unique()
                    ->values();

                $presentDays = $presentDates->count();

                /*
                |------------------------------------------------------------------
                | PAID DAYS
                |------------------------------------------------------------------
                |
                | Untuk tahap pertama:
                | paid_days = hari hadir.
                |
                | Cuti / izin / sakit akan kita integrasikan setelah aturan
                | paid / unpaid sudah dikunci.
                |
                */

                $paidDays = $presentDays;

                /*
                |------------------------------------------------------------------
                | ABSENT DAYS
                |------------------------------------------------------------------
                */

                $absentDays = max(
                    0,
                    $workingDays - $presentDays
                );

                /*
                |------------------------------------------------------------------
                | SALARY
                |------------------------------------------------------------------
                */

                $salaryDaily = (float) $contract->salary_daily;
                $salaryAmount = $paidDays * $salaryDaily;

                /*
                |------------------------------------------------------------------
                | BENEFITS
                |------------------------------------------------------------------
                |
                | Benefit diambil dari contract->benefits.
                |
                | amount pada pivot contract_benefits diperlakukan sebagai
                | nominal benefit per hari kerja.
                |
                | Semua benefit yang tercantum pada contract dihitung.
                | Tidak ada filter status master benefit di sini karena
                | contract sudah menjadi sumber benefit karyawan pada periode.
                |
                */

                $benefitTotal = 0.0;

                foreach ($contract->benefits as $benefit) {
                    $benefitDaily = (float) ($benefit->pivot->amount ?? 0);
                    $benefitAmount = $paidDays * $benefitDaily;

                    if ($benefitAmount <= 0) {
                        continue;
                    }

                    $benefitTotal += $benefitAmount;
                }

                /*
                |------------------------------------------------------------------
                | GROSS / DEDUCTION / NET
                |------------------------------------------------------------------
                */

                $grossAmount = $salaryAmount + $benefitTotal;
                $deductionAmount = 0.0;
                $netAmount = $grossAmount - $deductionAmount;

                /*
                |------------------------------------------------------------------
                | CREATE PAYROLL
                |------------------------------------------------------------------
                */

                $payroll = Payroll::create([
                    'payroll_period_id' => $period->id,
                    'employee_id' => $employee->id,
                    'employee_contract_id' => $contract->id,

                    /* Snapshot */
                    'position_name' => $contract->position_name,
                    'salary_daily' => $salaryDaily,

                    /* Attendance snapshot */
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'late_days' => 0,
                    'absent_days' => $absentDays,
                    'paid_leave_days' => 0,
                    'unpaid_leave_days' => 0,
                    'paid_days' => $paidDays,

                    /* Amount */
                    'gross_amount' => $grossAmount,
                    'deduction_amount' => $deductionAmount,
                    'net_amount' => $netAmount,

                    /* Status */
                    'status' => 'draft',
                    'notes' => null,
                    'processed_at' => null,
                    'paid_at' => null,
                ]);

                /*
                |------------------------------------------------------------------
                | CREATE SYSTEM PAYROLL ITEM: GAJI HARIAN
                |------------------------------------------------------------------
                */

                if ($salaryAmount > 0) {
                    PayrollItem::create([
                        'payroll_id' => $payroll->id,
                        'name' => 'Gaji Harian',
                        'type' => 'earning',
                        'category' => 'salary',
                        'amount' => $salaryAmount,
                        'quantity' => $paidDays,
                        'rate' => $salaryDaily,
                        'source' => 'system',
                        'description' =>
                        "Gaji harian {$paidDays} hari × Rp" .
                            number_format(
                                $salaryDaily,
                                0,
                                ',',
                                '.'
                            ),
                        'sort_order' => 1,
                    ]);
                }

                /*
                |------------------------------------------------------------------
                | CREATE SYSTEM PAYROLL ITEMS: BENEFITS
                |------------------------------------------------------------------
                */

                $sortOrder = 2;

                foreach ($contract->benefits as $benefit) {
                    $benefitDaily = (float) ($benefit->pivot->amount ?? 0);
                    $benefitAmount = $paidDays * $benefitDaily;

                    if ($benefitAmount <= 0) {
                        continue;
                    }

                    PayrollItem::create([
                        'payroll_id' => $payroll->id,
                        'name' => $benefit->name,
                        'type' => 'earning',
                        'category' => 'benefit',
                        'amount' => $benefitAmount,
                        'quantity' => $paidDays,
                        'rate' => $benefitDaily,
                        'source' => 'system',
                        'description' =>
                        "Tunjangan {$paidDays} hari × Rp" .
                            number_format(
                                $benefitDaily,
                                0,
                                ',',
                                '.'
                            ),
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | REFRESH COMPUTED
        |--------------------------------------------------------------------------
        */

        unset(
            $this->payrolls,
            $this->summary,
            $this->eligibleEmployees
        );

        $this->period->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Payroll berhasil dibuat sebagai draft. Gaji dan benefit harian sudah dihitung.'
        );
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
        $summary = $this->period
            ->payrolls()
            ->selectRaw('
                COUNT(*) as employees_count,
                COALESCE(SUM(gross_amount), 0) as gross_amount,
                COALESCE(SUM(deduction_amount), 0) as deduction_amount,
                COALESCE(SUM(net_amount), 0) as net_amount
            ')
            ->first();

        return [
            'employee_count' => (int) ($summary->employees_count ?? 0),
            'gross_amount' => (float) ($summary->gross_amount ?? 0),
            'deduction_amount' => (float) ($summary->deduction_amount ?? 0),
            'net_amount' => (float) ($summary->net_amount ?? 0),
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
    | REFRESH
    |--------------------------------------------------------------------------
    */

    #[On('payroll-period-updated')]
    public function refreshPeriod(): void
    {
        $this->period->refresh();

        $this->period->load([
            'creator',
            'processor',
        ]);
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
                'toast',
                type: 'error',
                message: 'Payroll periode ini sudah tidak dapat diproses.'
            );

            return;
        }

        $payrollCount = $this->period
            ->payrolls()
            ->count();

        if ($payrollCount === 0) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'Belum ada payroll karyawan pada periode ini.'
            );

            return;
        }

        if ($payrollCount !== $this->eligibleEmployeeCount) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: "Payroll belum lengkap. Eligible {$this->eligibleEmployeeCount} karyawan, payroll dibuat {$payrollCount} karyawan."
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

            $generatedCount = $period
                ->payrolls()
                ->count();

            if ($generatedCount !== $this->eligibleEmployeeCount) {
                abort(
                    422,
                    'Jumlah payroll karyawan belum lengkap untuk diproses.'
                );
            }

            /*
            |------------------------------------------------------------------
            | PROCESSING
            |------------------------------------------------------------------
            |
            | Status sementara selama transaksi berlangsung.
            |
            */

            $period->update([
                'status' => 'processing',
            ]);

            /*
            |------------------------------------------------------------------
            | LOCK ALL PAYROLLS
            |------------------------------------------------------------------
            */

            $period->payrolls()
                ->lockForUpdate()
                ->get();

            /*
            |------------------------------------------------------------------
            | PROCESS PAYROLL
            |------------------------------------------------------------------
            */

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

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Payroll berhasil diproses dan dikunci.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MARK AS PAID
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
                'toast',
                type: 'error',
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

            $period->payrolls()->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $period->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });

        $this->period->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
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
                'toast',
                type: 'error',
                message: 'Hanya payroll periode berstatus draft yang dapat dihapus.'
            );

            return;
        }

        DB::transaction(function () {
            /*
            |------------------------------------------------------------------
            | Payroll dan payroll_items akan ikut terhapus melalui
            | cascade foreign key.
            |------------------------------------------------------------------
            */

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
