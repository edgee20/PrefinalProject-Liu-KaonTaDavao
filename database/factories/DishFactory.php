<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\Eatery;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Dish> */
class DishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'eatery_id' => Eatery::factory(),
            'name' => fake()->randomElement([
                'Pork Adobo', 'Chicken Inasal', 'Grilled Bangus', 'Beef Bulalo',
                'Pancit Canton', 'Ginisang Monggo', 'Pinakbet', 'Pork Sisig',
                'Fried Tilapia', 'Buko Pandan', 'Banana Cue', 'Garlic Rice',
            ]),
            'price' => fake()->numberBetween(25, 250),
            'is_bestseller' => fake()->boolean(25),
        ];
    }
}
