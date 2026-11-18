<?php

use Illuminate\Support\Facades\Schedule;

// Every morning at 8:00 every member with an overdue book gets an email.
Schedule::command('library:overdue-notices')->dailyAt('08:00');
