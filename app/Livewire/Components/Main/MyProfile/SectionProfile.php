<?php

namespace App\Livewire\Components\Main\MyProfile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SectionProfile extends Component
{
    public User $user;

    #[Validate([
        'required',
        'string'
    ], message: [
        'name.required' => 'nama wajib di isi',
    ])]
    public ?string $name = "";

    public ?string $email = "";

    #[Validate([
        'required',
        'digits_between:10,15',
        'regex:/^08[0-9]+$/'
    ], message: [
        'noHp.required' => 'Nomor telepon wajib di isi',
        'noHp.regex' => 'Nomor telepon harus di awali 08',
        'noHp.digits_between' => 'Nomor Handphone harus di antara 10 sampai 15 angka',
    ])]
    public ?string $noHp = "";

    #[Validate(['required', 'in:male,female'])]
    public ?string $gender = "";

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user?->name;
        $this->email = $this->user?->email;
        $this->noHp = $this->user?->employees?->profile?->phone_number;
        $this->gender = $this->user?->employees?->profile?->gender;
    }

    public function update()
    {
        $this->validate();

        DB::transaction(function () {
            $this->user->update([
                'name' => $this->name
            ]);

            $this->user->employees->profile()->update([
                'phone_number' => $this->noHp,
                'gender' => $this->gender
            ]);
        });

        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Update Profile', message: 'Anda berhasil merubah informasi profile');
        $this->dispatch('change-profile');
    }
    public function render()
    {
        return view('livewire.components.main.my-profile.section-profile');
    }
}
