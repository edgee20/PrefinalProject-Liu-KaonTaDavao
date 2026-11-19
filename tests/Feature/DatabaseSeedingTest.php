<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Eatery;
use App\Models\Feature;
use App\Models\Permit;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        fake()->unique(true);
        $this->seed();
    }

    public function test_required_totals_districts_features_and_users_are_exact(): void
    {
        $this->assertDatabaseCount('districts', 6);
        $this->assertDatabaseCount('features', 6);
        $this->assertDatabaseCount('users', 12);
        $this->assertDatabaseCount('eateries', 15);
        $this->assertDatabaseCount('permits', 15);
        $this->assertSame(3, Eatery::where('is_open', false)->count());
        $this->assertEqualsCanonicalizing(
            ['Poblacion', 'Talomo', 'Agdao', 'Buhangin', 'Toril', 'Calinan'],
            District::pluck('name')->all()
        );
        $this->assertEqualsCanonicalizing(
            ['Suki ni Liu', 'Budget Meal', 'Halal', 'Open 24 Hours', 'Aircon', 'lutong-bahay'],
            Feature::pluck('name')->all()
        );

        foreach (['liu@kaonta.test' => 'Ej Liu', 'tita.liu@kaonta.test' => 'Tita Liu'] as $email => $name) {
            $user = User::where('email', $email)->firstOrFail();
            $this->assertSame($name, $user->name);
            $this->assertNotSame('kaonta2026', $user->password);
            $this->assertTrue(Hash::check('kaonta2026', $user->password));
        }
    }

    public function test_personalized_eatery_permit_dishes_features_and_review_are_exact(): void
    {
        $eatery = Eatery::with(['district', 'permit', 'dishes', 'features', 'reviews.user'])
            ->where('slug', 'carinderia-ni-liu')->firstOrFail();

        $this->assertSame('Carinderia ni Liu', $eatery->name);
        $this->assertSame('Poblacion', $eatery->district->name);
        $this->assertSame('San Pedro St., Poblacion, Davao City', $eatery->address);
        $this->assertSame('budget', $eatery->price_range);
        $this->assertSame('Tinolang Manok', $eatery->specialty);
        $this->assertTrue($eatery->is_open);
        $this->assertSame('DVO-2026-0420', $eatery->permit->permit_no);
        $this->assertSame('2027-04-20', $eatery->permit->expires_on->toDateString());
        $this->assertEqualsCanonicalizing(['Suki ni Liu', 'Budget Meal'], $eatery->features->pluck('name')->all());
        $this->assertCount(3, $eatery->dishes);

        foreach ([
            'Tinolang Manok' => ['85.00', true],
            'Kinilaw na Tuna' => ['120.00', false],
            'Durian Shake' => ['75.00', false],
        ] as $name => [$price, $bestseller]) {
            $dish = $eatery->dishes->firstWhere('name', $name);
            $this->assertNotNull($dish);
            $this->assertSame($price, $dish->price);
            $this->assertSame($bestseller, $dish->is_bestseller);
        }

        $this->assertGreaterThanOrEqual(3, $eatery->reviews->count());
        $titaReview = $eatery->reviews->firstWhere('body', 'Lami kaayo! Balik-balikan ni Tita Liu.');
        $this->assertNotNull($titaReview);
        $this->assertSame(5, $titaReview->rating);
        $this->assertSame('Tita Liu', $titaReview->user->name);
        $this->assertSame('tita.liu@kaonta.test', $titaReview->user->email);
    }

    public function test_factory_eateries_have_the_required_related_records_and_bounds(): void
    {
        $eateries = Eatery::with('permit')->withCount(['dishes', 'features', 'reviews'])
            ->where('slug', '!=', 'carinderia-ni-liu')->get();

        $this->assertCount(14, $eateries);

        foreach ($eateries as $eatery) {
            $this->assertNotNull($eatery->permit);
            $this->assertMatchesRegularExpression('/^DVO-\d{4}-\d{4,5}$/', $eatery->permit->permit_no);
            $this->assertGreaterThanOrEqual(2, $eatery->dishes_count);
            $this->assertLessThanOrEqual(5, $eatery->dishes_count);
            $this->assertGreaterThanOrEqual(1, $eatery->features_count);
            $this->assertLessThanOrEqual(3, $eatery->features_count);
            $this->assertLessThanOrEqual(6, $eatery->reviews_count);

            if (! $eatery->is_open) {
                $this->assertSame(0, $eatery->reviews_count);
            }
        }

        $this->assertGreaterThanOrEqual(1, Permit::whereDate('expires_on', '<', today())->count());
        $this->assertGreaterThanOrEqual(3, Eatery::has('reviews')->count());
        $this->assertSame(0, Review::whereNotBetween('rating', [1, 5])->count());
    }

    public function test_closed_factory_state_and_default_open_state_work(): void
    {
        $this->assertTrue(Eatery::factory()->make()->is_open);
        $this->assertFalse(Eatery::factory()->closed()->make()->is_open);
    }
}
