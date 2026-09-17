<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\EmployeeContract;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Spatie\LaravelPdf\Facades\Pdf;

class PrintContractEmployeeController extends Controller
{
    public function __invoke(
        Employees $employee,
        EmployeeContract $contract
    ) {
        abort_if($contract->employee_id !== $employee->id, 404);

        $companyBusinessType = "Industri pengembangan perangkat lunak";

        /*
        |--------------------------------------------------------------------------
        | Load relasi employee
        |--------------------------------------------------------------------------
        */

        $employee->loadMissing([
            'user',
            'profile',
            'profile.addressProfile.village.district.regency.province',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Load relasi contract
        |--------------------------------------------------------------------------
        */

        $contract->loadMissing([
            'benefits' => function ($q) {
                $q->where('status', 'active');
            },

            'contractLeave.leaveType',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Alamat Employee
        |--------------------------------------------------------------------------
        */

        $employeeAddress = $employee->profile?->addressProfile;

        $fullEmployeeAddress = collect([
            $employeeAddress?->full_address,
            $employeeAddress?->village?->name,
            $employeeAddress?->village?->district?->name,
            $employeeAddress?->village?->district?->regency?->name,
            $employeeAddress?->village?->district?->regency?->province?->name,
        ])
            ->filter()
            ->implode(', ');


        /*
        |--------------------------------------------------------------------------
        | Gaji Pokok Harian
        |--------------------------------------------------------------------------
        */

        $dailySalary = (float) $contract->salary_daily;


        /*
        |--------------------------------------------------------------------------
        | Bulan Acuan
        |--------------------------------------------------------------------------
        |
        | Untuk "1 bulan penuh", menggunakan bulan dari start_date
        | dan menghitung seluruh hari Senin-Sabtu.
        |
        */

        $referenceMonth = Carbon::parse(
            $contract->start_date
        )->startOfMonth();

        $period = CarbonPeriod::create(
            $referenceMonth->copy()->startOfMonth(),
            $referenceMonth->copy()->endOfMonth()
        );

        $workingDaysPerMonth = collect($period)
            ->filter(
                fn(Carbon $date) =>
                $date->dayOfWeekIso <= 6
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Benefit
        |--------------------------------------------------------------------------
        |
        | Setiap amount pada contract_benefits dianggap sebagai
        | nominal benefit per 1 hari kerja.
        |
        */

        $benefits = $contract->benefits->map(
            function ($benefit) {
                return [
                    'name' =>
                    $benefit->name,

                    'description' =>
                    $benefit->description,

                    'amount' =>
                    (float) (
                        $benefit->pivot->amount ?? 0
                    ),
                ];
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Total Benefit
        |--------------------------------------------------------------------------
        */

        $dailyBenefitTotal = $benefits->sum('amount');

        $monthlyBenefitTotal =
            $dailyBenefitTotal * $workingDaysPerMonth;


        /*
        |--------------------------------------------------------------------------
        | Total Gaji
        |--------------------------------------------------------------------------
        */

        $monthlySalary =
            $dailySalary * $workingDaysPerMonth;

        $dailyTotalCompensation =
            $dailySalary + $dailyBenefitTotal;

        $monthlyTotalCompensation =
            $dailyTotalCompensation * $workingDaysPerMonth;


        /*
        |--------------------------------------------------------------------------
        | Jatah Cuti Contract
        |--------------------------------------------------------------------------
        |
        | Mengambil jenis cuti yang memang diberikan pada contract
        | beserta jumlah hari/jatahnya.
        |
        */

        $leaveEntitlements = $contract->contractLeave
            ->map(function ($entitlement) {
                return [
                    'name' =>
                    $entitlement->leaveType?->name ?? 'Jenis Cuti',

                    'days' =>
                    (int) $entitlement->days,
                ];
            });


        /*
        |--------------------------------------------------------------------------
        | Filename
        |--------------------------------------------------------------------------
        */

        $filename = 'kontrak-' . str_replace(
            ['/', '\\'],
            '-',
            $contract->contract_number
        ) . '.pdf';


        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        return Pdf::view('print.tempContract', [
            'employee' =>
            $employee,

            'contract' =>
            $contract,

            'employeeAddress' =>
            $employeeAddress,

            'fullEmployeeAddress' =>
            $fullEmployeeAddress,

            'employeeProfile' =>
            $employee->profile,

            'companyBusinessType' =>
            $companyBusinessType,

            'benefits' =>
            $benefits,

            'leaveEntitlements' =>
            $leaveEntitlements,

            'dailySalary' =>
            $dailySalary,

            'workingDaysPerMonth' =>
            $workingDaysPerMonth,

            'monthlySalary' =>
            $monthlySalary,

            'dailyBenefitTotal' =>
            $dailyBenefitTotal,

            'monthlyBenefitTotal' =>
            $monthlyBenefitTotal,

            'dailyTotalCompensation' =>
            $dailyTotalCompensation,

            'monthlyTotalCompensation' =>
            $monthlyTotalCompensation,

            'referenceMonth' =>
            $referenceMonth,
        ])
            ->driver('chrome')
            ->format('a4')
            ->orientation('portrait')
            ->name($filename);
    }
}
