<?php

namespace App\Livewire\Page\Main\Dicipline;

use App\Models\AttedanceSetting;
use App\Models\LateDisciplineRule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Aturan Keterlambatan'])]
class LateDiciplineRule extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public int $perPage = 10;

    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE SETTINGS
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function attendanceSetting(): ?AttedanceSetting
    {
        return AttedanceSetting::query()
            ->latest('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | ATURAN AKTIF
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function activeRules()
    {
        return LateDisciplineRule::query()
            ->where('status', 'active')
            ->when(
                filled($this->search),
                function ($query) {
                    $search = trim($this->search);

                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere(
                                'description',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->orderBy('id')
            ->get();
    }

    /*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

    public function openCreateModal(): void
    {
        abort_unless(
            Auth::user()->can('create-late-discipline-rule'),
            403
        );

        $this->dispatch(
            'late-discipline-rule-create'
        );
    }

    public function openEditModal(int $ruleId): void
    {
        abort_unless(
            Auth::user()->can('edit-late-discipline-rule'),
            403
        );

        $this->dispatch(
            'late-discipline-rule-edit',
            ruleId: $ruleId
        );
    }

    /*
|--------------------------------------------------------------------------
| REFRESH
|--------------------------------------------------------------------------
*/

    #[On('late-discipline-rule-saved')]
    public function refreshRules(): void
    {
        unset(
            $this->rules,
        );
    }
    /*
    |--------------------------------------------------------------------------
    | RIWAYAT ATURAN
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function rules()
    {
        return LateDisciplineRule::query()
            ->latest('id')
            ->paginate($this->perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function money(float|int|string|null $value): string
    {
        return 'Rp' . number_format(
            (float) ($value ?? 0),
            0,
            ',',
            '.'
        );
    }

    public function thresholdLabel(
        int|float|string|null $threshold
    ): string {
        return number_format(
            (float) ($threshold ?? 0),
            0,
            ',',
            '.'
        ) . ' kali';
    }

    public function actionLabel(string $actionType): string
    {
        return match ($actionType) {
            'payroll_deduction' => 'Potongan Gaji',
            default => ucfirst(
                str_replace('_', ' ', $actionType)
            ),
        };
    }

    public function periodLabel(string $periodType): string
    {
        return match ($periodType) {
            'monthly' => 'Bulanan',
            default => ucfirst(
                str_replace('_', ' ', $periodType)
            ),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | PREVIEW
    |--------------------------------------------------------------------------
    */

    public function previewAmount(
        LateDisciplineRule $rule,
        int $lateCount
    ): float {
        if ($rule->threshold <= 0) {
            return 0;
        }

        $occurrences = intdiv(
            $lateCount,
            $rule->threshold
        );

        return $occurrences * (float) $rule->action_amount;
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        return view(
            'livewire.page.main.dicipline.late-dicipline-rule'
        );
    }
}
