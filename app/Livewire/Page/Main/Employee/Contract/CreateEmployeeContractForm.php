<?php

namespace App\Livewire\Page\Main\Employee\Contract;

use Livewire\Attributes\Validate;
use Livewire\Form;


class CreateEmployeeContractForm extends Form
{
    #[Validate(['required', 'exists:positions,id'], message: [
        'positionId.required' => 'Position wajib di isi',
        'positionId.exists' => 'Position sudah tidak ada',
    ])]
    public $positionId = '';

    public $salary_position = 0;

    #[Validate(['required', 'in:pkwt,pkwtt,intership,freelance'], message: [
        'contractType.required' => "tipe contract wajib di isi",
        'contractType.in' => "tidak boleh di luar pilihan",
    ])]
    public string $contractType = '';

    #[Validate(['required', 'date'], message: [
        'start_date.required' => 'Tanggal mulai wajib di isi',
        'start_date.date' => 'Format tanggal tidak valid',
    ])]
    public string $start_date = '';

    #[Validate(['nullable', 'date', 'after:start_date'])]
    public ?string $end_date = null;

    #[Validate(['required', 'numeric'], message: [
        'salary_daily.required' => 'Gaji harian wajib di isi',
        'salary_daily.numeric' => 'Gaji harian harus berisi angka',

    ])]
    public $salary_daily = 0;


    #[Validate(['required', 'in:draft,active'], message: [
        'salary_daily.required' => 'Status contract wajib di isi',
        'salary_daily.in' => 'di luar pilihan',

    ])]
    public string $statusContract = "draft";

    public array $benefitSelect = [];

    #[Validate(['required', 'string'], message: [
        'salary_daily.required' => 'catatan wajib di isi',
        'salary_daily.string' => 'catatan harus berisi teks',

    ])]
    public string $note = "";
}
