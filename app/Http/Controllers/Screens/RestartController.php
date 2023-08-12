<?php

namespace App\Http\Controllers\Screens;

use App\Http\Controllers\Controller;
use App\Models\Screen;

class RestartController extends Controller
{
    public function __invoke(Screen $screen)
    {
        if ($screen->should_restart) {
            $screen->update([
                'should_restart' => false,
            ]);

            return 'restart';
        }

        return '';
    }
}
