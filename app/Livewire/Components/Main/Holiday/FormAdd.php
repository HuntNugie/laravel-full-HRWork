<?php

namespace App\Livewire\Components\Main\Holiday;

use App\Models\Holidays;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormAdd extends Component
{

    #[Validate(['required', 'date', 'unique:holidays,date'], message: [
        'date.required' => 'tanggal wajib di isi',
        'date.date' => 'Harus berupa tanggal',
        'date.unique' => 'tanggal tersebut sudah di isi'
    ])]
    public $date;


    #[Validate(['required', "string"], message: [
        "name.required" => "nama event wajib di isi"
    ])]
    public string $name = "";

    #[Validate(['nullable', 'string'])]
    public string $desc = "";


    public function store()
    {
        $this->authorize('create', Holidays::class);
        $this->validate();
        Holidays::create([
            'name' => $this->name,
            'description' => $this->desc,
            'date' => $this->date
        ]);

        $this->dispatch('wirekit-modal-close', name: 'add-holiday');
        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Tambah jadwal libur',
            message: 'Jadwal berhasil di ditambahkan.'
        );
        $this->dispatch('refreshPage');
    }

    public function render()
    {
        return view('livewire.components.main.holiday.form-add');
    }
}
