<?php

namespace Database\Factories;

use App\Models\Eatery;
use App\Models\Permit;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Permit> */
class PermitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'eatery_id' => Eatery::factory(),
            'permit_no' => fake()->unique()->numerify('DVO-2026-#####'),
            'expires_on' => today()->addDays(fake()->numberBetween(30, 365)),
        ];
    }
}
