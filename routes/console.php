<?php

use Illuminate\Support\Facades\Schedule;

// Price checks by plan
Schedule::command('price:check free')->daily();
Schedule::command('price:check starter')->everySixHours()->at('00:00');
Schedule::command('price:check pro')->everySixHours();
Schedule::command('price:check enterprise')->hourly();

// Cleanup
Schedule::command('price:cleanup-history')->daily()->at('03:00');
