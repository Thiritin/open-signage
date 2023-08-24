<?php

namespace Database\Factories;

use App\Models\Layout;
use App\Models\Page;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaylistItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlaylistItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'playlist_id' => Playlist::factory(),
            'page_id' => Page::factory(),
            'layout_id' => Layout::factory(),
            'duration' => $this->faker->word,
        ];
    }
}
