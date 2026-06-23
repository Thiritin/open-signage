<?php

namespace Database\Seeders;

use App\Enums\ResourceOwnership;
use App\Models\Announcement;
use App\Models\Room;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use App\Models\Project;
use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class StarterSeeder extends Seeder
{
    /**
     * Seed a generic "Starter" demo so a fresh clone running
     * `php artisan migrate --seed` gets a working demo screen at
     * `/screens/demo` playing a small playlist of example designs.
     */
    public function run(): void
    {
        $project = Project::updateOrCreate([
            'path' => 'Starter',
        ], [
            'name' => 'Starter',
            'path' => 'Starter',
            'type' => ResourceOwnership::USER,
        ]);

        /**
         * Pages (example designs). The matching Vue components are created
         * separately; here we only register the database records + schema.
         * Schema entries follow the shape used elsewhere:
         * ["name" => .., "property" => .., "type" => "TextInput"].
         */
        $welcomePage = $project->pages()->updateOrCreate([
            'component' => 'Welcome',
        ], [
            'name' => 'Welcome',
            'component' => 'Welcome',
            'schema' => [
                [
                    'name' => 'Title',
                    'property' => 'title',
                    'type' => 'TextInput',
                ],
                [
                    'name' => 'Subtitle',
                    'property' => 'subtitle',
                    'type' => 'TextInput',
                ],
            ],
        ]);

        $scheduleBoardPage = $project->pages()->updateOrCreate([
            'component' => 'ScheduleBoard',
        ], [
            'name' => 'Schedule Board',
            'component' => 'ScheduleBoard',
            'schema' => [
                [
                    'name' => 'Title',
                    'property' => 'title',
                    'type' => 'TextInput',
                ],
            ],
        ]);

        $announcementsPage = $project->pages()->updateOrCreate([
            'component' => 'Announcements',
        ], [
            'name' => 'Announcements',
            'component' => 'Announcements',
            'schema' => [
                [
                    'name' => 'Title',
                    'property' => 'title',
                    'type' => 'TextInput',
                ],
            ],
        ]);

        $roomGridPage = $project->pages()->updateOrCreate([
            'component' => 'RoomGrid',
        ], [
            'name' => 'Room Grid',
            'component' => 'RoomGrid',
            'schema' => [],
        ]);

        /**
         * Layouts.
         */
        $gridLayout = $project->layouts()->updateOrCreate([
            'component' => 'Grid',
        ], [
            'name' => 'Grid',
            'component' => 'Grid',
        ]);

        $noneLayout = $project->layouts()->updateOrCreate([
            'component' => 'None',
        ], [
            'name' => 'None',
            'component' => 'None',
        ]);

        /**
         * Playlist + items. `content` provides the schema props the Vue
         * components receive (keys must match the page `property` names).
         */
        $playlist = $project->playlists()->firstOrCreate([
            'name' => 'Starter Demo',
        ]);

        $items = [
            [
                'page_id' => $welcomePage->id,
                'layout_id' => $gridLayout->id,
                'title' => 'Welcome',
                'duration' => 15,
                'sort' => 1,
                'content' => [
                    'title' => 'Welcome to Open Signage',
                    'subtitle' => 'Build signage designs, fast',
                ],
            ],
            [
                'page_id' => $scheduleBoardPage->id,
                'layout_id' => $gridLayout->id,
                'title' => 'Schedule Board',
                'duration' => 15,
                'sort' => 2,
                'content' => [
                    'title' => "What's On",
                ],
            ],
            [
                'page_id' => $roomGridPage->id,
                'layout_id' => $gridLayout->id,
                'title' => 'Room Grid',
                'duration' => 15,
                'sort' => 3,
                'content' => [],
            ],
            [
                'page_id' => $announcementsPage->id,
                'layout_id' => $noneLayout->id,
                'title' => 'Announcements',
                'duration' => 15,
                'sort' => 4,
                'content' => [
                    'title' => 'Announcements',
                ],
            ],
        ];

        foreach ($items as $item) {
            $playlist->playlistItems()->updateOrCreate([
                'page_id' => $item['page_id'],
                'layout_id' => $item['layout_id'],
            ], [
                'title' => $item['title'],
                'duration' => $item['duration'],
                'sort' => $item['sort'],
                'is_active' => true,
                'content' => $item['content'],
            ]);
        }

        /**
         * Demo rooms.
         */
        $roomNames = ['Main Stage', 'Workshop A', 'Workshop B', 'Lounge', 'Info Desk'];
        $rooms = collect($roomNames)->map(fn (string $name) => Room::updateOrCreate(
            ['name' => $name],
            ['name' => $name],
        ));

        /**
         * Demo schedule entries spread across today. `flags` is a
         * non-nullable json column, so it is always provided; `delay` is set
         * on a couple of entries. `room_id` is required.
         */
        $today = Carbon::today();
        $titles = [
            'Opening Keynote',
            'Coffee & Networking',
            'Intro Workshop',
            'Hands-on Lab',
            'Panel Discussion',
            'Lunch Break',
            'Lightning Talks',
            'Deep Dive Session',
            'Community Meetup',
            'Closing Remarks',
        ];

        foreach ($titles as $index => $title) {
            $start = $today->copy()->addHours(9 + $index);
            $room = $rooms[$index % $rooms->count()];

            ScheduleEntry::updateOrCreate([
                'title' => $title,
                'room_id' => $room->id,
            ], [
                'title' => $title,
                'room_id' => $room->id,
                'starts_at' => $start,
                'ends_at' => $start->copy()->addMinutes(50),
                'flags' => [],
                'delay' => in_array($index, [2, 6]) ? 15 : 0,
            ]);
        }

        /**
         * Demo announcements.
         */
        $announcements = [
            [
                'title' => 'Welcome!',
                'content' => 'Welcome to the Open Signage starter demo.',
            ],
            [
                'title' => 'Wifi',
                'content' => 'Connect to the "OpenSignage" network. No password required.',
            ],
            [
                'title' => 'Help Desk',
                'content' => 'Visit the Info Desk for any assistance.',
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::updateOrCreate([
                'title' => $announcement['title'],
            ], [
                'title' => $announcement['title'],
                'content' => $announcement['content'],
                'starts_at' => $today->copy()->startOfDay(),
                'ends_at' => $today->copy()->endOfDay(),
            ]);
        }

        /**
         * Demo screen at /screens/demo.
         */
        $screen = Screen::updateOrCreate([
            'slug' => 'demo',
        ], [
            'name' => 'Demo Screen',
            'slug' => 'demo',
            'playlist_id' => $playlist->id,
            'provisioned' => true,
        ]);

        // Attach demo rooms with pivot sort order (other pivot fields use
        // their column defaults / nullable values).
        $screen->rooms()->sync(
            $rooms->mapWithKeys(fn (Room $room, int $index) => [
                $room->id => ['sort' => $index],
            ])->all()
        );

        /**
         * Make the Starter Demo playlist the default for newly auto-created
         * screens. SystemSeeder runs first and only sets this when empty
         * (pointing at the Screen Identification playlist); for a clean demo
         * we explicitly override it so the demo is what shows by default.
         */
        $settings = app(GeneralSettings::class);
        $settings->playlist_id = $playlist->id;
        $settings->save();
    }
}
