<?php

namespace Database\Factories;

use App\Models\Eatery;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Review> */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        $reviewedAt = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'eatery_id' => Eatery::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'body' => fake()->randomElement([
                'Lami ang pagkaon ug sulit para sa presyo.',
                'The grilled fish was fresh and the serving was generous.',
                'A comforting lunch after a busy morning in Davao.',
                'Friendly service and a good selection of home-cooked dishes.',
                'The food was good, though our order took a little while.',
                'A simple place for an affordable meal with friends.',
                'I enjoyed the soup and would order it again.',
                'The meal was okay, but I would prefer it served warmer.',
            ]),
            'created_at' => $reviewedAt,
            'updated_at' => $reviewedAt,
        ];
    }
}
