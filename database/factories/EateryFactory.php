<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Eatery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Eatery> */
class EateryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Kusina ni Mila', 'Balay Tinola', 'Agdao Sugba Corner', 'Lutong Toril',
            'Talyer Kainan', 'Buhangin Bulalo', 'Talomo Grill', 'Kanto Kinilaw',
            'Dabawenyo Lunch House', 'Calinan Garden Eatery', 'Nanay Belen Kitchen',
            'San Pedro Silogan', 'Matina Merienda House', 'Lola Nena Carinderia',
            'Bajada Batchoy', 'Tuna sa Kanto', 'Puan Panciteria', 'Sasa Sinugba',
            'Maruya at Saging', 'Kadayawan Kainan', 'Bangkal Inihaw', 'Tita Rosa Eatery',
            'Tugbok Tinapayan', 'Bolton Paksiwan',
        ]);

        return [
            'district_id' => District::query()->inRandomOrder()->firstOrFail()->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => fake()->randomElement(['Rizal St.', 'Quezon Blvd.', 'Mabini St.', 'Roxas Ave.', 'Bonifacio St.']).', Davao City',
            'price_range' => fake()->randomElement(['budget', 'mid', 'splurge']),
            'specialty' => fake()->randomElement(['Pork Adobo', 'Grilled Bangus', 'Beef Bulalo', 'Chicken Inasal', 'Pancit Canton', 'Kinilaw na Tuna']),
            'is_open' => true,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => ['is_open' => false]);
    }
}
