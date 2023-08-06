<?php

namespace Database\Factories;

use App\Models\ScheduleEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScheduleEntry::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'starts_at' => $this->faker->dateTime(),
            'ends_at' => $this->faker->dateTime(),
            'is_moved' => $this->faker->boolean,
        ];
    }
}
