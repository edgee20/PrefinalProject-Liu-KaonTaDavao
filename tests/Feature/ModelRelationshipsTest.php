<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Eatery;
use App\Models\Feature;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_child_relationships_work_in_both_directions(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $eatery = $this->createEatery($district);
        $user = User::factory()->create();
        $dish = $eatery->dishes()->create(['name' => 'Tinolang Manok', 'price' => 85, 'is_bestseller' => true]);
        $permit = $eatery->permit()->create(['permit_no' => 'DVO-2026-0420', 'expires_on' => '2027-04-20']);
        $review = $eatery->reviews()->create(['user_id' => $user->id, 'rating' => 5, 'body' => 'A delicious meal worth coming back for.']);

        $this->assertTrue($district->eateries->contains($eatery));
        $this->assertTrue($eatery->district->is($district));
        $this->assertTrue($eatery->dishes->contains($dish));
        $this->assertTrue($dish->eatery->is($eatery));
        $this->assertTrue($eatery->permit->is($permit));
        $this->assertTrue($permit->eatery->is($eatery));
        $this->assertTrue($eatery->reviews->contains($review));
        $this->assertTrue($review->eatery->is($eatery));
        $this->assertTrue($user->reviews->contains($review));
        $this->assertTrue($review->user->is($user));
    }

    public function test_features_support_both_directions_and_pivot_operations(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $eatery = $this->createEatery($district);
        $budget = Feature::create(['name' => 'Budget Meal']);
        $halal = Feature::create(['name' => 'Halal']);

        $eatery->features()->attach($budget->id);
        $this->assertTrue($eatery->features()->first()->is($budget));
        $this->assertTrue($budget->eateries()->first()->is($eatery));

        $eatery->features()->sync([$halal->id]);
        $this->assertSame([$halal->id], $eatery->features()->pluck('features.id')->all());
        $this->assertSame(0, $budget->eateries()->count());

        $eatery->features()->detach();
        $this->assertSame(0, $eatery->features()->count());
        $this->assertDatabaseCount('features', 2);
    }

    public function test_district_reviews_are_retrieved_through_its_eateries(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $otherDistrict = District::create(['name' => 'Talomo']);
        $first = $this->createEatery($district, 'First Eatery');
        $second = $this->createEatery($district, 'Second Eatery');
        $other = $this->createEatery($otherDistrict, 'Other Eatery');
        $user = User::factory()->create();
        $attributes = ['user_id' => $user->id, 'rating' => 4, 'body' => 'A satisfying local lunch.'];
        $firstReview = $first->reviews()->create($attributes);
        $secondReview = $second->reviews()->create($attributes);
        $otherReview = $other->reviews()->create($attributes);

        $this->assertSame(2, $district->reviews()->count());
        $this->assertTrue($district->reviews->contains($firstReview));
        $this->assertTrue($district->reviews->contains($secondReview));
        $this->assertFalse($district->reviews->contains($otherReview));
        $this->assertSame(1, $otherDistrict->reviews()->count());
    }

    public function test_saved_attributes_have_the_required_casts(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $eatery = $this->createEatery($district);
        $dish = $eatery->dishes()->create(['name' => 'Tinolang Manok', 'price' => 85, 'is_bestseller' => true]);
        $permit = $eatery->permit()->create(['permit_no' => 'DVO-2026-0420', 'expires_on' => '2027-04-20']);

        $this->assertTrue($eatery->fresh()->is_open);
        $this->assertTrue($dish->fresh()->is_bestseller);
        $this->assertSame('85.00', $dish->fresh()->price);
        $this->assertInstanceOf(CarbonInterface::class, $permit->fresh()->expires_on);
        $this->assertSame('2027-04-20', $permit->fresh()->expires_on->toDateString());

        $eatery->update(['is_open' => false]);
        $dish->update(['is_bestseller' => false]);
        $this->assertFalse($eatery->fresh()->is_open);
        $this->assertFalse($dish->fresh()->is_bestseller);
    }

    public function test_eatery_model_binding_uses_slugs_while_other_models_use_ids(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $eatery = $this->createEatery($district, 'Carinderia ni Liu');

        $this->assertSame('slug', $eatery->getRouteKeyName());
        $this->assertSame('carinderia-ni-liu', $eatery->getRouteKey());
        $this->assertTrue((new Eatery)->resolveRouteBinding('carinderia-ni-liu')->is($eatery));
        $this->assertNull((new Eatery)->resolveRouteBinding('unknown-eatery'));
        $this->assertNull((new Eatery)->resolveRouteBinding((string) $eatery->id));
        $this->assertSame('id', $district->getRouteKeyName());
        $this->assertTrue((new District)->resolveRouteBinding($district->id)->is($district));
    }

    private function createEatery(District $district, string $name = 'Test Eatery'): Eatery
    {
        return $district->eateries()->create([
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => 'San Pedro St., Poblacion, Davao City',
            'price_range' => 'budget',
            'specialty' => 'Tinolang Manok',
            'is_open' => true,
        ]);
    }
}
