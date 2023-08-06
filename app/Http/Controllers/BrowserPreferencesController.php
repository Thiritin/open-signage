<?php

namespace App\Http\Controllers;

class BrowserPreferencesController extends Controller
{
    public function __invoke(string $browser)
    {
        abort_if(! in_array($browser, ['chrome', 'firefox']), 400, 'Browser not supported.');

        if ($browser === 'chrome') {
            $entries['AutoplayAllowed'] = true;

            $entries = collect($entries)->map(fn (
                $value,
                $key
            ) => "\"{$key}\"" . ': ' . (($value ? 'true' : 'false') . ','))->toArray();
        } else {
            $entries['media.autoplay.default'] = 0;
            $entries = collect($entries)->map(fn (
                $value,
                $key
            ) => $key . ' = ' . ($value))->toArray();
        }

        return response()->streamDownload(function () use ($entries) {
            echo implode("\n", $entries);
        }, 'config.txt', [
            'Content-Type' => 'text/plain',
            'Content-Encoding' => 'ANSI',
        ]);
    }
}
