<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImagesConvertCommand extends Command
{
    protected $signature = 'images:convert {artworkId?} {--folder}';

    protected $description = 'Converts images to webp and optimizes them.';

    public function handle(): void
    {
        $query = Artwork::query();

        if($this->hasOption('folder')) {
            // Get all files in the public folder and put them into an arry
            // Remove all that have a dupelicate .webp file or end in .webp
            // Create a webp version of them
            $files = Storage::drive('public')->allFiles();
            $webPFiles = collect($files)->filter(fn($file) => Str::endsWith($file, ['.webp']));
            $files = collect($files)->filter(fn($file) => Str::endsWith($file, ['.jpg', '.png', '.jpeg']));
            $files = $files->reject(fn($file) => $webPFiles->contains($file.'.webp'));
            $files->each(function ($file) {
                $this->info("Converting {$file}...");
                $image = Storage::drive('public')->get($file);
                Image::make($image)
                    ->save(storage_path('app/public/'.$file.'.webp'),80,'webp');
            });
            return;
        }

        if($this->argument('artworkId')) {
            $query = $query->where('id', $this->argument('artworkId'));
        }

        $query->get()->each(function (Artwork $artwork) {
            $this->info("Converting {$artwork->name}...");
            collect(['file_banner', 'file_horizontal', 'file_vertical'])
                ->reject(fn($file) => empty($artwork->{$file}))
                ->each(function ($file) use ($artwork) {
                $this->info("Converting {$artwork->name} {$file}...");
                $image = Storage::drive('public')->get($artwork->{$file});
                Image::make($image)
                    ->save(storage_path('app/public/'.$artwork->{$file}.'.webp'),80,'webp');
            });
        });
    }
}
