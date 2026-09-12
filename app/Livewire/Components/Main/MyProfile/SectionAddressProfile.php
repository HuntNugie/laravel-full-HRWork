<?php

namespace App\Livewire\Components\Main\MyProfile;

use Aliziodev\Wilayah\Facades\Wilayah;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SectionAddressProfile extends Component
{
    public ?string $provinceCode = null;
    public ?string $regencyCode = null;
    public ?string $districtCode = null;
    public ?string $villageCode = null;

    #[Validate(['required'], message: ['detailAddress.required' => 'alamat lengkap wajib di isi'])]
    public ?string $detailAddress = null;
    public User $user;


    public function mount()
    {
        $this->provinceCode = $this->user->employees->profile->addressProfile->village->district->regency->province->code;
        $this->regencyCode = $this->user->employees->profile->addressProfile->village->district->regency->code;
        $this->districtCode = $this->user->employees->profile->addressProfile->village->district->code;
        $this->villageCode = $this->user->employees->profile->addressProfile->village->code;
        $this->detailAddress = $this->user->employees->profile->addressProfile->full_address;
    }

    public function canSubmit()
    {
        return filled($this->provinceCode) && filled($this->districtCode) && filled($this->villageCode) && filled($this->detailAddress);
    }
    public function save()
    {
        $this->validate();

        $this->user->employees->profile->addressProfile->update([
            'full_address' => $this->detailAddress,
            'village_code' => $this->villageCode,
        ]);

        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Update Profile', message: 'Anda berhasil merubah informasi Alamat profile');
        $this->dispatch('change-profile');
    }

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
    public function render()
    {
        return view('livewire.components.main.my-profile.section-address-profile');
    }
}
