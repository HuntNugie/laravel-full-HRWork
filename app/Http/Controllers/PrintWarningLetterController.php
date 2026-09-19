<?php

namespace App\Http\Controllers;

use App\Models\EmployeeWarningLetter;
use Spatie\LaravelPdf\Facades\Pdf;

class PrintWarningLetterController extends Controller
{
    public function __invoke(EmployeeWarningLetter $warningLetter)
    {
        abort_unless(
            auth()->user()->can('show-warning-letter'),
            403
        );

        $warningLetter->load([
            'employee.user',
            'issuer',
        ]);

        abort_unless(
            $warningLetter->status === 'issued',
            404
        );

        $filename = 'surat-peringatan-' . str_replace(
            ['/', '\\'],
            '-',
            $warningLetter->letter_number
        ) . '.pdf';

        return Pdf::view('print.tempWarningLetter', [
            'warningLetter' => $warningLetter,
        ])
            ->driver('chrome')
            ->format('a4')
            ->orientation('portrait')
            ->name($filename);
    }
}
