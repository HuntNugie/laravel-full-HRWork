<?php

namespace App\Livewire\Page\Main\Dicipline;

use App\Models\UnpresentDisciplineRule as ModelsUnpresentDisciplineRule;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.main', ['title' => 'Aturan Tidak Hadir'])]
class UnpresentDisciplineRule extends Component
{
    #[On('unpresent-discipline-rule-saved')]
    public function refreshRule(): void
    {
        // refresh data
    }
    public function render(): View
    {
        $rule = ModelsUnpresentDisciplineRule::query()
            ->firstOrFail();

        return view(
            'livewire.page.main.dicipline.unpresent-discipline-rule',
            [
                'rule' => $rule,
            ]
        );
    }
}
