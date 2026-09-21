<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\LateDisciplineRule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ModalEditLateDiciplineRule extends Component
{
    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public string $name = '';

    public int $threshold = 3;

    public string $actionAmount = '20000';

    public string $description = '';


    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $rule = LateDisciplineRule::query()
            ->firstOrFail();

        $this->name = $rule->name;

        $this->threshold = (int) $rule->threshold;

        $this->actionAmount = (string) $rule->action_amount;

        $this->description = $rule->description ?? '';
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(): void
    {
        abort_unless(
            Auth::user()->can('edit-late-discipline-rule'),
            403
        );

        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'threshold' => [
                'required',
                'integer',
                'min:1',
            ],

            'actionAmount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $rule = LateDisciplineRule::query()
            ->firstOrFail();

        DB::transaction(function () use ($validated, &$rule): void {
            /*
            |----------------------------------------------------------------------
            | HRWork saat ini hanya memiliki satu aturan keterlambatan aktif.
            | Saat aturan diedit, nonaktifkan aturan aktif lain agar konfigurasi
            | yang dipakai payroll tetap deterministic.
            |----------------------------------------------------------------------
            */
            LateDisciplineRule::query()
                ->where('id', '!=', $rule->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'inactive',
                ]);

            $rule->update([
                'name' => trim($validated['name']),
                'threshold' => (int) $validated['threshold'],
                'action_amount' => $validated['actionAmount'],
                'description' => filled($validated['description'])
                    ? trim($validated['description'])
                    : null,
                'status' => 'active',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | REFRESH FORM
        |--------------------------------------------------------------------------
        */

        $rule->refresh();

        $this->name = $rule->name;

        $this->threshold = (int) $rule->threshold;

        $this->actionAmount = (string) $rule->action_amount;

        $this->description = $rule->description ?? '';


        /*
        |--------------------------------------------------------------------------
        | REFRESH PARENT
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'late-discipline-rule-saved'
        );


        /*
        |--------------------------------------------------------------------------
        | TOAST
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-toast',
            variant: 'success',
            title: 'Berhasil',
            message: 'Aturan keterlambatan berhasil diperbarui.'
        );

        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-modal-close',
            name: 'edit-late-discipline-rule'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        return view(
            'livewire.components.main.dicipline.modal-edit-late-dicipline-rule'
        );
    }
}
