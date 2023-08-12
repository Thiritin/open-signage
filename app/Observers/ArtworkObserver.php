<?php

namespace App\Observers;

use App\Models\Artwork;
use Illuminate\Support\Facades\Artisan;

class ArtworkObserver
{
    public function created(Artwork $artwork): void
    {
        Artisan::call('images:convert',['artworkId' => $artwork->id]);
    }

    public function updated(Artwork $artwork): void
    {
        Artisan::call('images:convert',['artworkId' => $artwork->id]);
    }

    public function deleted(Artwork $artwork): void
    {

    }

    public function restored(Artwork $artwork): void
    {
    }

    public function forceDeleted(Artwork $artwork): void
    {
    }
}
