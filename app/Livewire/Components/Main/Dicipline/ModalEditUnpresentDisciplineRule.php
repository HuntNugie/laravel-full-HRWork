<?php

namespace App\Livewire\Components\Main\Dicipline;

use App\Models\UnpresentDisciplineRule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ModalEditUnpresentDisciplineRule extends Component
{
    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public int $threshold = 3;

    public string $periodType = 'monthly';

    public string $description = '';


    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $rule = UnpresentDisciplineRule::query()
            ->firstOrFail();

        $this->threshold = (int) $rule->threshold;

        $this->periodType = $rule->period_type;

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
            Auth::user()->can('edit-unpresent-discipline-rule'),
            403
        );

        $validated = $this->validate([
            'threshold' => [
                'required',
                'integer',
                'min:1',
            ],

            'periodType' => [
                'required',
                'in:monthly',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $rule = UnpresentDisciplineRule::query()
            ->firstOrFail();

        $rule->update([
            'threshold' => (int) $validated['threshold'],
            'period_type' => $validated['periodType'],
            'description' => filled($validated['description'])
                ? trim($validated['description'])
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | REFRESH FORM
        |--------------------------------------------------------------------------
        */

        $rule->refresh();

        $this->threshold = (int) $rule->threshold;

        $this->periodType = $rule->period_type;

        $this->description = $rule->description ?? '';


        /*
        |--------------------------------------------------------------------------
        | REFRESH PARENT
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'unpresent-discipline-rule-saved'
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'wirekit-modal-close',
            name: 'edit-unpresent-discipline-rule'
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
            message: 'Aturan ketidakhadiran berhasil diperbarui.'
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
            'livewire.components.main.dicipline.modal-edit-unpresent-discipline-rule'
        );
    }
}
