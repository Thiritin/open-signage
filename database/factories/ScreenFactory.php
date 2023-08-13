<?php

namespace Database\Factories;

use App\Models\Playlist;
use App\Models\Screen;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'playlist_id' => Playlist::factory(),
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'provisioned' => $this->faker->boolean,
        ];
    }
}
