<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\PlaylistItem;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use App\Observers\AnnouncementObserver;
use App\Observers\PlaylistItemObserver;
use App\Observers\ScheduleObserver;
use App\Observers\ScreenObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Screen::observe(ScreenObserver::class);
        PlaylistItem::observe(PlaylistItemObserver::class);
        Announcement::observe(AnnouncementObserver::class);
        ScheduleEntry::observe(ScheduleObserver::class);
    }
}
