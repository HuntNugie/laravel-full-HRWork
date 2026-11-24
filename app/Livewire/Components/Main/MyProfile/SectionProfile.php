<?php

namespace App\Livewire\Components\Main\MyProfile;

use Livewire\Component;

class SectionProfile extends Component
{
    public string $name = '';
    public string $email = '';
    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }
    public function update(): void
    {
        auth()->user()->update($this->validate(['name' => 'required|string|max:100']));
        $this->dispatch('change-profile');
    }
    public function render()
    {
        return view('livewire.components.main.my-profile.section-profile');
    }
}
