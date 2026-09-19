<?php

namespace App\Livewire\Page\Main\Dicipline;

use App\Models\EmployeeWarningLetter;
use App\Service\UnpresentDisciplineService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Surat Peringatan'])]
class WarningLetter extends Component
{
    use WithPagination;

    public string $search = '';

    public string $level = 'all';

    public string $status = 'all';

    public int $perPage = 10;


    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function summary(): array
    {
        return [
            'draft' => EmployeeWarningLetter::query()
                ->where('status', 'draft')
                ->count(),

            'issued' => EmployeeWarningLetter::query()
                ->where('status', 'issued')
                ->count(),

            'cancelled' => EmployeeWarningLetter::query()
                ->where('status', 'cancelled')
                ->count(),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | UNPRESENT INDICATIONS
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function unpresentIndications()
    {
        return app(UnpresentDisciplineService::class)
            ->getCandidates()
            ->filter(function (array $indication) {
                if (blank($this->search)) {
                    return true;
                }

                $search = strtolower(trim($this->search));

                $employeeName = strtolower(
                    $indication['employee']->user?->name ?? ''
                );

                $employeeCode = strtolower(
                    $indication['employee']->employee_code ?? ''
                );

                return str_contains($employeeName, $search)
                    || str_contains($employeeCode, $search);
            })
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | WARNING LETTERS
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function warningLetters()
    {
        return EmployeeWarningLetter::query()
            ->with([
                'employee.user',
                'creator',
                'issuer',
                'canceller',
            ])
            ->when(
                filled($this->search),
                function (Builder $query) {
                    $search = trim($this->search);

                    $query->whereHas(
                        'employee',
                        function (Builder $employeeQuery) use ($search) {
                            $employeeQuery->where(function (Builder $query) use ($search) {

                                $query
                                    ->where(
                                        'employee_code',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereHas(
                                        'user',
                                        function (Builder $userQuery) use ($search) {
                                            $userQuery->where(
                                                'name',
                                                'like',
                                                "%{$search}%"
                                            );
                                        }
                                    );
                            });
                        }
                    );
                }
            )
            ->when(
                $this->level !== 'all',
                function (Builder $query) {
                    $query->where(
                        'warning_level',
                        $this->level
                    );
                }
            )
            ->when(
                $this->status !== 'all',
                function (Builder $query) {
                    $query->where(
                        'status',
                        $this->status
                    );
                }
            )
            ->latest('id')
            ->paginate($this->perPage);
    }


    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'issued' => 'Diterbitkan',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    }


    public function statusIntent(string $status): string
    {
        return match ($status) {
            'draft' => 'secondary',
            'issued' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
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


    public function updatedLevel(): void
    {
        $this->resetPage();
    }


    public function updatedStatus(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | REFRESH
    |--------------------------------------------------------------------------
    */

    #[On('warning-letter-saved')]
    public function refreshWarningLetterPage(): void
    {
        unset(
            $this->summary,
            $this->unpresentIndications,
            $this->warningLetters
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */
    #[On('warning-letter-indication-not-found')]
    public function indicationNotFound(): void
    {
        $this->dispatch(
            'wirekit-toast',
            variant: 'error',
            title: 'Gagal',
            message: 'Data indikasi pelanggaran tidak ditemukan.',
        );
    }

    public function render(): View
    {
        return view(
            'livewire.page.main.dicipline.warning-letter'
        );
    }
}
