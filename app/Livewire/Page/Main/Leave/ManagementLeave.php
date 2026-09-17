<?php

namespace App\Livewire\Page\Main\Leave;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.main', ['title' => 'Halaman Management Cuti'])]
class ManagementLeave extends Component
{
    use WithPagination;

    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    public string $segment = 'all';

    public string $search = '';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;


    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

    public int $perPage = 5;


    /*
    |--------------------------------------------------------------------------
    | RESET PAGE SAAT FILTER BERUBAH
    |--------------------------------------------------------------------------
    */

    public function updatedSegment(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | RESET FILTER
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->reset([
            'segment',
            'search',
            'dateFrom',
            'dateTo',
        ]);

        $this->segment = 'all';

        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | REFRESH DATA
    |--------------------------------------------------------------------------
    |
    | Dipanggil setelah approve, reject, atau aksi lain terhadap
    | pengajuan cuti.
    |
    */

    #[On('leave-management-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

    private function leaveRequestQuery(): Builder
    {
        return LeaveRequest::query()
            ->with([
                'employees.user',
                'leaveType',
                'employeeContract',
            ])
            ->when(
                filled($this->search),
                function (Builder $query) {
                    $query->whereHas(
                        'employees',
                        function (Builder $employeeQuery) {
                            $employeeQuery->where(function (Builder $query) {
                                $query
                                    ->whereHas(
                                        'user',
                                        function (Builder $userQuery) {
                                            $userQuery->where(
                                                'name',
                                                'like',
                                                '%' . $this->search . '%'
                                            );
                                        }
                                    )
                                    ->orWhere(
                                        'employee_code',
                                        'like',
                                        '%' . $this->search . '%'
                                    );
                            });
                        }
                    );
                }
            )
            ->when(
                $this->segment !== 'all',
                function (Builder $query) {
                    $query->where(
                        'status',
                        $this->segment
                    );
                }
            )
            ->when(
                filled($this->dateFrom),
                function (Builder $query) {
                    $query->whereDate(
                        'end_date',
                        '>=',
                        $this->dateFrom
                    );
                }
            )
            ->when(
                filled($this->dateTo),
                function (Builder $query) {
                    $query->whereDate(
                        'start_date',
                        '<=',
                        $this->dateTo
                    );
                }
            )
            ->latest('created_at');
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.page.main.leave.management-leave',
            [
                'leaveRequests' => $this->leaveRequestQuery()
                    ->paginate($this->perPage),
            ]
        );
    }
}
