<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Page;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'component' => $this->faker->word,
            'schema' => '{}',
        ];
    }
}
