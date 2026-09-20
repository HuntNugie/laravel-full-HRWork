<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPdf\Facades\Pdf;

class PrintPayrollSummaryController extends Controller
{
    public function __invoke(PayrollPeriod $period)
    {
        abort_unless(Auth::user()->can('show-payroll'), 403);

        abort_unless(
            in_array($period->status, ['processed', 'paid'], true),
            404
        );

        $payrolls = Payroll::query()
            ->where('payroll_period_id', $period->id)
            ->orderBy('id')
            ->get();

        $employees = Employees::query()
            ->with('user')
            ->whereIn('id', $payrolls->pluck('employee_id'))
            ->get()
            ->keyBy('id');

        $rows = $payrolls->map(
            fn(Payroll $payroll) => [
                'payroll' => $payroll,
                'employee' => $employees->get($payroll->employee_id),
            ]
        );

        $filename = 'rekap-payroll-' . $this->safeFilename($period->name) . '.pdf';

        return Pdf::view('print.payroll.rekap-payroll', [
            'period' => $period,
            'rows' => $rows,
            'employeeCount' => $payrolls->count(),
            'grossTotal' => $payrolls->sum('gross_amount'),
            'deductionTotal' => $payrolls->sum('deduction_amount'),
            'netTotal' => $payrolls->sum('net_amount'),
        ])
            ->driver('chrome')
            ->format('a4')
            ->orientation('landscape')
            ->name($filename);
    }

    private function safeFilename(string|int $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $value);

        return trim($value, '-') ?: 'payroll';
    }
}
