<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class ImagesConvertCommand extends Command
{
    protected $signature = 'images:convert {artworkId?} {--folder}';

    protected $description = 'Converts images to webp and optimizes them.';

    public function handle(): void
    {
        $query = Artwork::query();

        if($this->hasOption('folder')) {
            // Get all files in the public folder and put them into an array
            // Remove all that have a duplicate .webp file or end in .webp
            // Create a webp version of them
            $files = Storage::allFiles();
            $webPFiles = collect($files)->filter(fn($file) => Str::endsWith($file, ['.webp']));
            $files = collect($files)->filter(fn($file) => Str::endsWith($file, ['.jpg', '.png', '.jpeg']));
            $files = $files->reject(fn($file) => $webPFiles->contains($file.'.webp'));
            $files->each(function ($file) {
                $this->info("Converting {$file}...");
                $imageContents = Storage::readStream($file);
                $encoded = Image::read($imageContents)->encode(new WebpEncoder(80));
                Storage::put($file.'.webp', (string) $encoded);
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
                $imageContents = Storage::readStream($artwork->{$file});
                $encoded = Image::read($imageContents)->encode(new WebpEncoder(80));
                Storage::put($artwork->{$file}.'.webp', (string) $encoded);
            });
        });
    }
}
