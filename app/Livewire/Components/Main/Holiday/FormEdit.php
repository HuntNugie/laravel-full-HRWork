<?php

namespace App\Livewire\Components\Main\Holiday;

use App\Models\Holidays;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormEdit extends Component
{
    public Holidays $holiday;

    #[Validate(['required', 'date',], message: [
        'date.required' => 'tanggal wajib di isi',
        'date.date' => 'Harus berupa tanggal',
    ])]
    public $date;


    #[Validate(['required', "string"], message: [
        "name.required" => "nama event wajib di isi"
    ])]
    public string $name = "";

    #[Validate(['nullable', 'string'])]
    public string $desc = "";

    public function mount()
    {
        $this->date = $this->holiday->date->format('Y-m-d');
        $this->name = $this->holiday->name;
        $this->desc = $this->holiday->description;
    }

    public function update()
    {
        $this->authorize('update', $this->holiday);
        $this->validate([
            "date" => [Rule::unique("holidays", "date")->ignore($this->holiday->id)]
        ]);

        $this->holiday->update([
            'name' => $this->name,
            'description' => $this->desc,
            'date' => $this->date
        ]);

        $this->dispatch('wirekit-modal-close', name: 'edit-holiday');
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Edit jadwal libur',
            message: 'Jadwal berhasil di di update.'
        );
        $this->dispatch('refreshPage');
    }
    public function render()
    {
        return view('livewire.components.main.holiday.form-edit');
    }
}
