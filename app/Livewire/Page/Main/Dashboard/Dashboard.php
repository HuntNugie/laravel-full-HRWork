<?php

namespace App\Livewire\Page\Main\Dashboard;

use App\Service\DashboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.main',['title'=>'Halaman Dashboard'])]
class Dashboard extends Component
{
    
    public function render()
    {
        return view(DashboardService::matching(Auth::user()));
    }
}
