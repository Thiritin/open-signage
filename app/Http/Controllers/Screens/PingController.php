<?php

namespace App\Http\Controllers\Screens;

use App\Http\Controllers\Controller;
use App\Models\Screen;

class PingController extends Controller
{
    public function __invoke(Screen $screen)
    {
        $screen->update(['last_ping_at' => now()]); // Update the last_ping_at column to the current time
    }
}
