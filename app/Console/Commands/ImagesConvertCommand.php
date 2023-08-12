<?php

namespace App\Console\Commands;

use App\Models\Artwork;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImagesConvertCommand extends Command
{
    protected $signature = 'images:convert {artworkId?}';

    protected $description = 'Converts images to webp and optimizes them.';

    public function handle(): void
    {
        $query = Artwork::query();

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
