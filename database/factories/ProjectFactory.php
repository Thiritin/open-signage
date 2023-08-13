<?php

namespace Database\Factories;

use App\Enums\ResourceOwnership;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => ResourceOwnership::USER->value,
            'path' => $this->faker->word(),
        ];
    }
}
