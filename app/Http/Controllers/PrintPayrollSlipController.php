<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPdf\Facades\Pdf;

class PrintPayrollSlipController extends Controller
{
    public function __invoke(PayrollPeriod $period, Payroll $payroll)
    {
        $user = Auth::user();

        if ($user->can('show-payroll')) {
            // HR / Administrator can print any payroll they are authorized to view.
        } elseif ($user->can('show-payroll-my')) {
            $employee = $user->employees;

            abort_unless(
                $employee && (int) $payroll->employee_id === (int) $employee->id,
                404
            );
        } else {
            abort(403);
        }

        abort_if((int) $payroll->payroll_period_id !== (int) $period->id, 404);

        abort_unless(
            in_array($period->status, ['processed', 'paid'], true),
            404
        );

        abort_unless(
            in_array($payroll->status, ['processed', 'paid'], true),
            404
        );

        $employee = Employees::query()
            ->with('user')
            ->findOrFail($payroll->employee_id);

        $items = PayrollItem::query()
            ->where('payroll_id', $payroll->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $filename = 'slip-gaji-' . $this->safeFilename(
            $employee->employee_code ?? $employee->id
        ) . '-' . $this->safeFilename($period->name) . '.pdf';

        return Pdf::view('print.payroll.slip-gaji', [
            'period' => $period,
            'payroll' => $payroll,
            'employee' => $employee,
            'items' => $items,
            'earnings' => $items->where('type', 'earning')->values(),
            'deductions' => $items->where('type', 'deduction')->values(),
        ])
            ->driver('chrome')
            ->format('a4')
            ->orientation('portrait')
            ->name($filename);
    }

    private function safeFilename(string|int $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $value);

        return trim($value, '-') ?: 'payroll';
    }
}
