<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Eatery;
use App\Models\Feature;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EateryManagementTest extends TestCase
{
    use RefreshDatabase;

    private District $district;

    private Feature $feature;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->district = District::create(['name' => 'Poblacion']);
        $this->feature = Feature::create(['name' => 'Budget Meal']);
        $this->user = User::factory()->create(['name' => 'Tita Liu']);
    }

    private function validFields(array $overrides = []): array
    {
        return array_replace([
            'name' => 'New Local Eatery',
            'district_id' => $this->district->id,
            'address' => 'San Pedro St., Poblacion, Davao City',
            'price_range' => 'budget',
            'specialty' => 'Tinolang Manok',
            'is_open' => '1',
            'features' => [$this->feature->id],
            'permit_no' => 'DVO-2026-1234',
            'expires_on' => today()->addYear()->toDateString(),
        ], $overrides);
    }

    private function createEatery(): Eatery
    {
        $fields = $this->validFields();
        $eatery = Eatery::create([
            'name' => $fields['name'], 'slug' => 'new-local-eatery',
            'district_id' => $fields['district_id'], 'address' => $fields['address'],
            'price_range' => 'budget', 'specialty' => $fields['specialty'], 'is_open' => true,
        ]);
        $eatery->permit()->create(['permit_no' => $fields['permit_no'], 'expires_on' => $fields['expires_on']]);
        $eatery->features()->attach($this->feature);

        return $eatery;
    }

    public function test_eatery_creation_uses_validated_data_and_relationships(): void
    {
        $fields = $this->validFields();
        unset($fields['is_open']);
        $response = $this->post(route('eateries.store'), $fields + ['slug' => 'untrusted-slug', 'id' => 999]);
        $eatery = Eatery::firstOrFail();

        $response->assertRedirect(route('eateries.show', $eatery))->assertSessionHas('status', 'Eatery added.');
        $this->assertSame('new-local-eatery', $eatery->slug);
        $this->assertFalse($eatery->is_open);
        $this->assertSame('DVO-2026-1234', $eatery->permit->permit_no);
        $this->assertSame([$this->feature->id], $eatery->features->modelKeys());
        $this->assertNotSame(999, $eatery->id);
    }

    public function test_create_and_update_show_the_exact_custom_validation_messages(): void
    {
        $eatery = $this->createEatery();
        $invalid = $this->validFields(['permit_no' => 'ABC-1', 'expires_on' => today()->subDay()->toDateString()]);
        unset($invalid['features']);
        $expected = [
            'name' => 'Kaon Ta Liu already lists an eatery with this name.',
            'features' => 'Pick at least one feature.',
            'permit_no' => 'Permit number must look like DVO-2026-0415.',
            'expires_on' => 'The permit must not be expired.',
        ];
        $this->from(route('eateries.create'))->post(route('eateries.store'), $invalid)
            ->assertRedirect(route('eateries.create'))->assertSessionHasErrors($expected);
        // Persist the session after TestResponse restarts it for its assertions.
        session()->reflash();
        session()->save();
        $this->get(route('eateries.create'))->assertOk()->assertSee('Pick at least one feature.')->assertSee('value="ABC-1"', false);

        $other = Eatery::create(['district_id' => $this->district->id, 'name' => 'Other Eatery', 'slug' => 'other-eatery', 'address' => 'Davao', 'price_range' => 'budget']);
        $other->permit()->create(['permit_no' => 'DVO-2026-5678', 'expires_on' => today()->addYear()]);
        $this->put(route('eateries.update', $other), $invalid)->assertSessionHasErrors($expected);
    }

    public function test_all_other_eatery_rules_reject_invalid_input(): void
    {
        $this->post(route('eateries.store'), $this->validFields([
            'name' => str_repeat('n', 101), 'district_id' => 999, 'address' => str_repeat('a', 256),
            'price_range' => 'cheap', 'specialty' => str_repeat('s', 101), 'is_open' => 'maybe',
            'features' => [999], 'expires_on' => 'not-a-date',
        ]))->assertSessionHasErrors(['name', 'district_id', 'address', 'price_range', 'specialty', 'is_open', 'features.0', 'expires_on']);
        $this->post(route('eateries.store'), [])->assertSessionHasErrors(['name', 'district_id', 'address', 'price_range', 'features', 'permit_no', 'expires_on']);
        $this->assertDatabaseCount('eateries', 0);
    }

    public function test_unchanged_update_slug_changes_features_and_unchecked_open_work(): void
    {
        $eatery = $this->createEatery();
        $this->put(route('eateries.update', $eatery), $this->validFields())
            ->assertRedirect(route('eateries.show', $eatery))->assertSessionHas('status', 'Eatery updated.');
        $feature = Feature::create(['name' => 'Halal']);
        $fields = $this->validFields(['name' => 'Renamed Eatery', 'permit_no' => 'DVO-2026-4321', 'features' => [$feature->id]]);
        unset($fields['is_open']);
        $this->put(route('eateries.update', $eatery), $fields)->assertRedirect(route('eateries.show', ['eatery' => 'renamed-eatery']));
        $eatery->refresh();
        $this->assertSame('renamed-eatery', $eatery->slug);
        $this->assertFalse($eatery->is_open);
        $this->assertSame('DVO-2026-4321', $eatery->permit->permit_no);
        $this->assertSame([$feature->id], $eatery->features->modelKeys());
        $this->get(route('eateries.show', ['eatery' => 'new-local-eatery']))->assertNotFound();
    }

    public function test_edit_keeps_unchecked_features_after_a_failed_submit(): void
    {
        $eatery = $this->createEatery();
        $fields = $this->validFields(['is_open' => '0']);
        unset($fields['features']);
        $this->from(route('eateries.edit', $eatery))->put(route('eateries.update', $eatery), $fields)->assertSessionHasErrors('features');
        $response = $this->get(route('eateries.edit', $eatery))->assertOk();
        $this->assertDoesNotMatchRegularExpression('/name="features\[\]"[^>]*checked/', $response->getContent());
        $this->assertDoesNotMatchRegularExpression('/id="is_open"[^>]*checked/', $response->getContent());
    }

    public function test_duplicate_permits_and_slug_collisions_are_validation_errors(): void
    {
        $this->createEatery();
        $this->post(route('eateries.store'), $this->validFields(['name' => 'Different Name']))->assertSessionHasErrors('permit_no');
        $this->post(route('eateries.store'), $this->validFields(['name' => 'New-Local-Eatery', 'permit_no' => 'DVO-2026-2222']))->assertSessionHasErrors('name');
        $this->post(route('eateries.store'), $this->validFields(['name' => 'Create', 'permit_no' => 'DVO-2026-2222']))->assertSessionHasErrors('name');
        $this->assertDatabaseCount('eateries', 1);
    }

    public function test_deletion_removes_children_and_pivot_but_preserves_district_and_features(): void
    {
        $eatery = $this->createEatery();
        $eatery->dishes()->create(['name' => 'Tinolang Manok', 'price' => 85]);
        $eatery->reviews()->create(['user_id' => $this->user->id, 'rating' => 5, 'body' => 'A delicious meal worth sharing.']);
        $this->delete(route('eateries.destroy', $eatery))->assertRedirect(route('eateries.index'))->assertSessionHas('status', 'Eatery deleted.');
        foreach (['eateries', 'permits', 'dishes', 'reviews', 'eatery_feature'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
        $this->assertDatabaseCount('districts', 1);
        $this->assertDatabaseCount('features', 1);
    }

    public function test_review_creation_deletion_and_exact_validation_messages(): void
    {
        $eatery = $this->createEatery();
        $this->from(route('eateries.show', $eatery))->post(route('eateries.reviews.store', $eatery), ['rating' => 6, 'body' => 'short'])
            ->assertSessionHasErrors([
                'user_id' => 'Choose who is writing this review.',
                'rating' => 'Ratings go from 1 to 5 stars only.',
                'body' => 'Kaon Ta Liu: reviews need at least 10 characters.',
            ]);
        $this->post(route('eateries.reviews.store', $eatery), ['user_id' => 999, 'rating' => 'four', 'body' => str_repeat('x', 1001)])
            ->assertSessionHasErrors(['user_id', 'rating', 'body']);
        $this->post(route('eateries.reviews.store', $eatery), ['user_id' => $this->user->id, 'rating' => 5, 'body' => 'Lami kaayo! Balik-balikan ni Tita Liu.'])
            ->assertRedirect(route('eateries.show', $eatery))->assertSessionHas('status', 'Salamat! Review posted.');
        $review = Review::firstOrFail();
        $this->assertSame($eatery->id, $review->eatery_id);
        $this->assertEquals(5, $eatery->reviews()->avg('rating'));
        $this->delete(route('reviews.destroy', $review))->assertRedirect(route('eateries.show', $eatery))->assertSessionHas('status', 'Review deleted.');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_closed_eatery_hides_review_form_and_rejects_crafted_posts(): void
    {
        $eatery = $this->createEatery();
        $eatery->update(['is_open' => false]);
        $this->get(route('eateries.show', $eatery))->assertOk()->assertSee('This eatery is closed. Reviews are paused.')
            ->assertDontSee('name="user_id"', false);
        $this->post(route('eateries.reviews.store', $eatery), ['user_id' => $this->user->id, 'rating' => 5, 'body' => 'This should not be saved.'])->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_detail_district_and_reviewer_pages_render_without_lazy_loading(): void
    {
        $eatery = $this->createEatery();
        $eatery->permit()->update(['expires_on' => today()->subDay()]);
        $eatery->dishes()->create(['name' => 'Tinolang Manok', 'price' => 85, 'is_bestseller' => true]);
        $eatery->reviews()->create(['user_id' => $this->user->id, 'rating' => 3, 'body' => 'An older review of lunch.', 'created_at' => now()->subDay()]);
        $eatery->reviews()->create(['user_id' => $this->user->id, 'rating' => 5, 'body' => 'A newer review of lunch.']);
        Model::preventLazyLoading();
        try {
            $this->get(route('home'))->assertOk();
            $this->get(route('eateries.index'))->assertOk();
            $this->get(route('eateries.show', $eatery))->assertOk()->assertSee('Permit expired')->assertSee('₱85.00')
                ->assertSee('Bestseller')->assertSeeInOrder(['A newer review of lunch.', 'An older review of lunch.']);
            $this->get(route('districts.index'))->assertOk()->assertSee('Poblacion');
            $this->get(route('districts.show', $this->district))->assertOk()->assertSee('2 reviews in Poblacion');
            $this->get(route('reviewers.show', $this->user))->assertOk()->assertSee('2 reviews written')
                ->assertSeeInOrder(['A newer review of lunch.', 'An older review of lunch.']);
            $this->get(route('eateries.create'))->assertOk();
            $this->get(route('eateries.edit', $eatery))->assertOk()->assertSee('DVO-2026-1234');
        } finally {
            Model::preventLazyLoading(false);
        }
        $eatery->permit()->update(['expires_on' => today()]);
        $this->get(route('eateries.show', $eatery))->assertOk()->assertDontSee('Permit expired');
    }
}
