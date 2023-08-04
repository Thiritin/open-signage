<?php

namespace App\Http\Controllers;

use App\Models\Screen;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfigController extends Controller
{
    private Screen|null $screen = null;

    public function __invoke(Request $request)
    {
        $kiosk = $request->get('kiosk');

        if (!empty($kiosk)) {
            $this->screen = Screen::firstOrCreate([
                'hostname' => $kiosk,
            ], [
                'name' => $kiosk,
                'slug' => $kiosk,
                'hostname' => $kiosk,
                'provisioned' => true,
            ]);
        }
        $settings = app(GeneralSettings::class)->toArray();
        $settings = array_merge($settings,($this->screen?->screenGroup?->settings ?? []));

        $settings['kiosk_config'] = route('config');
        if (!empty($kiosk)) {
            $settings['homepage'] = route('kiosk');
            $settings['hostname'] = $this->screen->hostname;
            $settings['ip_address'] = $this->screen->ip_address;
        } else {
            // Reboot after 60 seconds to restart client after first initialisation
            $settings['run_command'] = "( sleep 30; reboot; ) &";
        }

        // Make sure to exclude general settings
        $entries = collect($settings)->except(['name', 'starts_at', 'ends_at', 'playlist_id'])
            ->reject(fn($value) => empty($value) && !is_bool($value))
            ->map(fn($value, $key) => $key.'='.$this->convertValue($key, $value))
            ->toArray();

        return response()->streamDownload(function () use ($entries) {
            echo implode("\n", $entries);
        }, 'config.txt', [
            'Content-Type' => 'text/plain',
            'Content-Encoding' => 'ANSI'
        ]);

    }

    private function convertValue($key, $value)
    {
        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }
        if (is_array($value)) {
            return implode(' ', $value);
        }
        if ($key === 'wallpaper') {
            return Storage::drive('public')->url($value);
        }

        if ($key === 'screen_rotate') {
            return $this->screen?->orientation ?? 'normal';
        }

        return $value;
    }
}
