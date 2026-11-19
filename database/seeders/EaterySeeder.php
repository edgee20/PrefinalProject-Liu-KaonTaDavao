<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\District;
use App\Models\Eatery;
use App\Models\Feature;
use App\Models\Permit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EaterySeeder extends Seeder
{
    public function run(): void
    {
        $personalEatery = Eatery::create([
            'district_id' => District::where('name', 'Poblacion')->firstOrFail()->id,
            'name' => 'Carinderia ni Liu',
            'slug' => Str::slug('Carinderia ni Liu'),
            'address' => 'San Pedro St., Poblacion, Davao City',
            'price_range' => 'budget',
            'specialty' => 'Tinolang Manok',
            'is_open' => true,
        ]);

        $personalEatery->features()->sync(
            Feature::whereIn('name', ['Suki ni Liu', 'Budget Meal'])->pluck('id')
        );
        $personalEatery->permit()->create([
            'permit_no' => 'DVO-2026-0420',
            'expires_on' => '2027-04-20',
        ]);
        $personalEatery->dishes()->createMany([
            ['name' => 'Tinolang Manok', 'price' => 85.00, 'is_bestseller' => true],
            ['name' => 'Kinilaw na Tuna', 'price' => 120.00, 'is_bestseller' => false],
            ['name' => 'Durian Shake', 'price' => 75.00, 'is_bestseller' => false],
        ]);

        $eateries = Eatery::factory()->count(11)->create()
            ->concat(Eatery::factory()->count(3)->closed()->create());
        $features = Feature::all();

        foreach ($eateries as $index => $eatery) {
            // One expired permit is guaranteed, even when the random data changes.
            Permit::factory()->for($eatery)->create(
                $index === 0 ? ['expires_on' => today()->subDay()] : []
            );
            Dish::factory()->count(fake()->numberBetween(2, 5))->for($eatery)->create();
            $eatery->features()->attach($features->random(fake()->numberBetween(1, 3))->modelKeys());
        }
    }
}
