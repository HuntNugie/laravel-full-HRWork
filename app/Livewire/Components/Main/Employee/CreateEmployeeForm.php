<?php
namespace App\Livewire\Components\Main\Employee;

use Aliziodev\Wilayah\Facades\Wilayah;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateEmployeeForm extends Form
{

    public ?string $provinceCode = null;
    public ?string $regencyCode = null;
    public ?string $districtCode = null;
    public ?string $villageCode = null;


    #[Validate(['required', 'min:2', 'string'], message: [
        'fullname.required' => "Nama wajib di isi",
        'fullname.min' => 'Minimal 2 karakter',
        'fullname.string' => "Nama wajib menggunakan teks"
    ])]
    public string $fullname = '';

    #[Validate(['required', 'email:dns', 'unique:users'], message: [
        'email.required' => 'email wajib di isi',
        'email.email' => 'Format Email tidak valid',
        'email.unique' => 'Email sudah di gunakan orang lain'
    ])]
    public string $email = '';

    #[Validate([
        'required',
        'string',
        'min:8',
    ], message: [
        'password.required' => 'password wajib di isi',
        'password.min' => 'password minimal 8 karakter'
    ])]
    public string $password = '';


    #[Validate(['required', 'string', 'same:password'], message: [
        'password_confirmation.required' => 'konfirmasi password wajib di isi',
        'password_confirmation.same' => 'Konfirmasi password tidak sama dengan password',
    ])]
    public string $password_confirmation = '';

    #[Validate(['required', 'digits:16', 'unique:employee_profiles,nik'], message: [
        'nik.required' => 'NIK wajib di isi',
        'nik.digits' => 'Nik harus terdiri dari 16 karakter',
        'nik.unique' => 'Nik sudah terdaftar atau di gunakan orang lain'
    ])]
    public string $nik = '';

    #[Validate('required,in:male,female', message: [
        'gender.required' => 'Jenis kelamin wajib di isi',
        'gender.in' => 'Jenis kelamin di luar pilihan',
    ])]
    public string $gender = '';

    #[Validate([
        'required',
        'digits_between:10,15',
        'regex:/^08[0-9]+$/'
    ], message: [
        'phone.required' => 'Nomor telepon wajib di isi',
        'phone.regex' => 'Nomor telepon harus di awali 08',
        'phone.digits_between' => 'Nomor Handphone harus di antara 10 sampai 15 angka',
    ])]
    public string $phone = '';

    #[Validate(['required'], message: ['detailAddress.required' => 'alamat lengkap wajib di isi'])]
    public string $detailAddress = '';

    #[Validate(['required','exists:teams,id'],message:[
        'teamId.required' => 'team wajib di isi',
        'teamId.exists' => 'Team tidak ada di database'
    ])]
    public $teamId = '';

    #[Validate(['required','exists:positions,id'],message:[
        'positionId.required' => 'Jabatan wajib di isi',
        'positionId.exists' => 'Jabatan tidak ada di database'
    ])]
    public $positionId = '';
    public function provinceOptions(): array
    {
        return collect(
            Wilayah::forSelect('provinces')
        )->mapWithKeys(
                fn($item) => [$item['value'] => $item['label']]
            )->toArray();
    }

    public function regencyOptions(): array
    {
        if (!$this->provinceCode) {
            return [];
        }

        return collect(
            Wilayah::forSelect(
                'regencies',
                province: $this->provinceCode
            )
        )->mapWithKeys(
                fn($item) => [$item['value'] => $item['label']]
            )->toArray();
    }

    public function districtOptions(): array
    {
        if (!$this->regencyCode) {
            return [];
        }

        return collect(
            Wilayah::forSelect(
                'districts',
                regency: $this->regencyCode
            )
        )->mapWithKeys(
                fn($item) => [$item['value'] => $item['label']]
            )->toArray();
    }

    public function villageOptions(): array
    {
        if (!$this->districtCode) {
            return [];
        }

        return collect(
            Wilayah::forSelect(
                'villages',
                district: $this->districtCode
            )
        )->mapWithKeys(
                fn($item) => [$item['value'] => $item['label']]
            )->toArray();
    }

    public function updatedProvinceCode(): void
    {
        $this->regencyCode = null;
        $this->districtCode = null;
        $this->villageCode = null;
    }

    public function updatedRegencyCode(): void
    {
        $this->districtCode = null;
        $this->villageCode = null;
    }

    public function updatedDistrictCode(): void
    {
        $this->villageCode = null;
    }
}