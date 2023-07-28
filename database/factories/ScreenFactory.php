<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Playlist;
use App\Models\Screen;

class ScreenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Screen::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'playlist_id' => 1,
            'name' => $this->faker->name,
            'last_ping_at' => $this->faker->dateTime(),
        ];
    }
}
