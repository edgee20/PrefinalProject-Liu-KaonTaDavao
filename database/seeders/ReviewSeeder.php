<?php

namespace Database\Seeders;

use App\Models\Eatery;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $personalEatery = Eatery::where('slug', 'carinderia-ni-liu')->firstOrFail();
        $tita = $users->firstWhere('email', 'tita.liu@kaonta.test');

        $personalEatery->reviews()->create([
            'user_id' => $tita->id,
            'rating' => 5,
            'body' => 'Lami kaayo! Balik-balikan ni Tita Liu.',
        ]);
        Review::factory()->count(2)->for($personalEatery)
            ->state(fn () => ['user_id' => $users->random()->id])->create();

        $openEateries = Eatery::where('is_open', true)
            ->where('id', '!=', $personalEatery->id)->orderBy('id')->get();

        foreach ($openEateries as $index => $eatery) {
            // Together with the personal eatery, these guarantee three rated eateries.
            $count = fake()->numberBetween($index < 2 ? 1 : 0, 6);
            Review::factory()->count($count)->for($eatery)
                ->state(fn () => ['user_id' => $users->random()->id])->create();
        }
    }
}
