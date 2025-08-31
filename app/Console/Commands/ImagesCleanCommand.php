<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use App\Models\ScreenGroup;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagesCleanCommand extends Command
{
    protected $signature = 'images:clean';

    protected $description = 'Removes images that are not being used anymore.';

    public function handle(): void
    {
        $this->info('Cleaning images...');
        $GlobalWallpaper = app(GeneralSettings::class)->wallpaper;

        $files = collect(Storage::allFiles())
            ->filter(fn($file) => Str::endsWith($file, '.png') || Str::endsWith($file, '.jpg'))
            ->unique()
            ->each(function ($file) use ($GlobalWallpaper) {
                $existsAsArtwork = Artwork::where('file_banner', $file)
                    ->orWhere('file_horizontal', $file)
                    ->orWhere('file_vertical', $file)->exists();
                $existsInProject = ScreenGroup::whereJsonContains('settings->wallpaper',$file)->exists();
                $existsInSettings = $GlobalWallpaper === $file;
                if (!$existsAsArtwork && !$existsInProject && !$existsInSettings) {
                    $this->info("Removing {$file}...");
                    Storage::delete($file);
                    Storage::delete($file.'.webp');
                }

            });
    }
}
