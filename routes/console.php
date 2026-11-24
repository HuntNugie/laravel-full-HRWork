<?php
use Illuminate\Support\Facades\Schedule;
Schedule::command('app:expire-contracts')->daily()->withoutOverlapping();
