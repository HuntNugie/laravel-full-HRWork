<?php

namespace App\Livewire\Components\Main\MyProfile;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;


class SectionPhotoProfile extends Component
{
    use WithFileUploads;

    #[Validate(['required', 'image', 'mimes:jpg,jpeg,webp,png', 'max:5120'], message: [
        'photo.required' => 'Photo wajib di isi',
        'photo.image' => 'file yang di upload Harus berupa gambar',
        'photo.mimes' => 'file harus berupa jpg,jpeg,webp,png',
        'photo.max' => 'File maximal harus 5MB',
    ])]
    public $photo;

    public function save()
    {
        $this->validate();

        Auth::user()->addMedia($this->photo)
            ->usingFileName(Str::uuid() . '.' . $this->photo->getClientOriginalExtension())
            ->toMediaCollection('avatar');

        $this->reset('photo');
        $this->dispatch('wirekit-toast', variant: 'success', title: 'Berhasil Update Photo', message: 'Anda berhasil mengupload photo profile');
        $this->dispatch('change-profile');
    }

    public function canSubmit()
    {
        return filled($this->photo);
    }

    public function render()
    {
        return view('livewire.components.main.my-profile.section-photo-profile');
    }
}
