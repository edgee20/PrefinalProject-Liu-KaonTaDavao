<?php

namespace Tests\Feature;

use App\Models\District;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutesAndLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_application_route_names_methods_uris_and_controllers_match_the_handout(): void
    {
        $expected = [
            'home' => ['GET|HEAD', '/', '__invoke'],
            'eateries.index' => ['GET|HEAD', 'eateries', 'index'],
            'eateries.create' => ['GET|HEAD', 'eateries/create', 'create'],
            'eateries.store' => ['POST', 'eateries', 'store'],
            'eateries.show' => ['GET|HEAD', 'eateries/{eatery}', 'show'],
            'eateries.edit' => ['GET|HEAD', 'eateries/{eatery}/edit', 'edit'],
            'eateries.update' => ['PUT|PATCH', 'eateries/{eatery}', 'update'],
            'eateries.destroy' => ['DELETE', 'eateries/{eatery}', 'destroy'],
            'eateries.reviews.store' => ['POST', 'eateries/{eatery}/reviews', 'store'],
            'reviews.destroy' => ['DELETE', 'reviews/{review}', 'destroy'],
            'districts.index' => ['GET|HEAD', 'districts', 'index'],
            'districts.show' => ['GET|HEAD', 'districts/{district}', 'show'],
            'reviewers.show' => ['GET|HEAD', 'reviewers/{user}', 'show'],
        ];

        Artisan::call('route:list', ['--except-vendor' => true, '--json' => true]);
        $listed = json_decode(Artisan::output(), true, flags: JSON_THROW_ON_ERROR);
        $this->assertCount(13, $listed);
        $this->assertEqualsCanonicalizing(array_keys($expected), array_column($listed, 'name'));

        foreach ($expected as $name => [$methods, $uri, $action]) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route, $name);
            $this->assertSame($methods, implode('|', $route->methods()), $name);
            $this->assertSame($uri, $route->uri(), $name);
            if ($name === 'home') {
                $this->assertSame('App\\Http\\Controllers\\HomeController', $route->getActionName());
                $this->assertTrue(method_exists($route->getActionName(), '__invoke'));
            } else {
                $this->assertSame($action, $route->getActionMethod(), $name);
            }
            $this->assertStringStartsWith('App\\Http\\Controllers\\', $route->getActionName(), $name);
        }
    }

    public function test_navigation_pages_share_the_title_and_named_links(): void
    {
        foreach (['home', 'eateries.index', 'districts.index', 'eateries.create'] as $name) {
            $response = $this->get(route($name));
            $response->assertOk();
            $response->assertSee('<title>'.e("Kaon Ta! Davao – Liu's Food Guide").'</title>', false);
            $response->assertSee('id="main-content"', false);

            foreach (['home', 'eateries.index', 'districts.index', 'eateries.create'] as $link) {
                $response->assertSee('href="'.route($link).'"', false);
            }
        }
    }

    public function test_flash_messages_are_displayed_and_escaped(): void
    {
        $message = '<strong>Saved</strong>';
        $response = $this->withSession(['status' => $message])->get(route('home'));

        $response->assertOk();
        $response->assertSee('role="status"', false);
        $response->assertSee($message);
        $response->assertDontSee($message, false);
    }

    public function test_eatery_urls_bind_by_slug_and_unknown_slugs_return_404(): void
    {
        $district = District::create(['name' => 'Poblacion']);
        $eatery = $district->eateries()->create([
            'name' => 'Carinderia ni Liu',
            'slug' => 'carinderia-ni-liu',
            'address' => 'San Pedro St., Poblacion, Davao City',
            'price_range' => 'budget',
        ]);

        $this->get(route('eateries.show', $eatery))->assertOk()->assertSee('Carinderia ni Liu');
        $this->get(route('eateries.edit', $eatery))->assertOk();
        $this->get(route('eateries.show', ['eatery' => $eatery->id]))->assertNotFound();
        $this->get(route('eateries.show', ['eatery' => 'unknown-eatery']))->assertNotFound();
        $this->post(route('eateries.reviews.store', ['eatery' => 'unknown-eatery']))->assertNotFound();
        $this->assertDatabaseCount('reviews', 0);
    }
}
