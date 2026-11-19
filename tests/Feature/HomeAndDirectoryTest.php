<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Eatery;
use App\Models\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomeAndDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_home_counts_and_top_three_are_based_on_database_reviews(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $user = User::factory()->create();
        $rated = [];

        foreach (['Five Star' => 5, 'Four Star' => 4, 'Three Star' => 3, 'Two Star' => 2] as $name => $rating) {
            $eatery = $this->createEatery($district, $name);
            $eatery->reviews()->create(['user_id' => $user->id, 'rating' => $rating, 'body' => 'A satisfying local meal.']);
            $rated[] = $eatery->id;
        }
        $this->createEatery($district, 'Unrated Eatery');

        $this->get(route('home'))->assertOk()
            ->assertViewHas('eateryCount', 5)
            ->assertViewHas('districtCount', 1)
            ->assertViewHas('reviewCount', 4)
            ->assertViewHas('topRated', fn ($eateries) => $eateries->modelKeys() === array_slice($rated, 0, 3))
            ->assertSee('5.0')->assertDontSee('Unrated Eatery');
    }

    public function test_name_or_specialty_search_is_grouped_with_district_and_feature_filters(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $otherDistrict = District::create(['name' => 'Talomo']);
        $budget = Feature::create(['name' => 'Budget Meal']);
        $halal = Feature::create(['name' => 'Halal']);
        $match = $this->createEatery($district, 'Target Eatery', 'Tinolang Manok');
        $match->features()->attach($budget);
        $wrongDistrict = $this->createEatery($otherDistrict, 'Tinola Elsewhere');
        $wrongDistrict->features()->attach($budget);
        $wrongFeature = $this->createEatery($district, 'Tinola Corner');
        $wrongFeature->features()->attach($halal);

        $this->get(route('eateries.index', ['q' => 'Tinola', 'district' => $district->id, 'feature' => 'Budget Meal']))
            ->assertOk()
            ->assertViewHas('eateries', fn ($eateries) => $eateries->pluck('id')->all() === [$match->id])
            ->assertSee('value="Tinola"', false)
            ->assertSee('value="'.$district->id.'" selected', false)
            ->assertSee('value="Budget Meal" selected', false)
            ->assertDontSee('Tinola Elsewhere')->assertDontSee('Tinola Corner');
    }

    public function test_directory_is_alphabetical_six_per_page_and_retains_all_filters(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $feature = Feature::create(['name' => 'Budget Meal']);
        for ($number = 8; $number >= 1; $number--) {
            $eatery = $this->createEatery($district, 'Eatery '.str_pad($number, 2, '0', STR_PAD_LEFT));
            $eatery->features()->attach($feature);
        }
        $eatery->update(['is_open' => false]);
        $filters = ['q' => 'Eatery', 'district' => $district->id, 'feature' => 'Budget Meal'];
        $response = $this->get(route('eateries.index', $filters))->assertOk();
        $eateries = $response->viewData('eateries');

        $this->assertSame(6, $eateries->count());
        $this->assertSame(8, $eateries->total());
        $this->assertSame(['Eatery 01', 'Eatery 02', 'Eatery 03', 'Eatery 04', 'Eatery 05', 'Eatery 06'], $eateries->pluck('name')->all());
        parse_str(parse_url($eateries->url(2), PHP_URL_QUERY), $nextPage);
        $this->assertEquals($filters + ['page' => 2], $nextPage);
        $response->assertSee('No reviews yet')->assertSee('Closed');
        $response->assertSee(route('districts.show', $district));
        $response->assertSee(route('eateries.index', ['feature' => $feature->name]));

        $this->get(route('eateries.index', $filters + ['page' => 2]))->assertOk()
            ->assertViewHas('eateries', fn ($page) => $page->pluck('name')->all() === ['Eatery 07', 'Eatery 08']);
    }

    public function test_directory_displays_the_exact_empty_state(): void
    {
        $this->get(route('eateries.index', ['q' => 'No match']))->assertOk()
            ->assertSee('No eateries match your filters.');
    }

    private function createEatery(District $district, string $name, string $specialty = 'Pork Adobo'): Eatery
    {
        return $district->eateries()->create([
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => 'Davao City',
            'price_range' => 'budget',
            'specialty' => $specialty,
            'is_open' => true,
        ]);
    }
}
