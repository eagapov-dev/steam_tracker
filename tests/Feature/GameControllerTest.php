<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Services\SteamApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_loads(): void
    {
        $response = $this->get('/games/search');
        $response->assertStatus(200);
    }

    public function test_search_with_query_calls_steam_api(): void
    {
        $mock = Mockery::mock(SteamApiService::class);
        $mock->shouldReceive('searchGames')
            ->with('portal')
            ->once()
            ->andReturn([
                [
                    'steam_app_id' => 400,
                    'name' => 'Portal',
                    'header_image_url' => 'https://example.com/portal.jpg',
                    'price' => 9.99,
                    'is_free' => false,
                ],
            ]);

        $this->app->instance(SteamApiService::class, $mock);

        $response = $this->get('/games/search?q=portal');
        $response->assertStatus(200);
        $response->assertSee('Portal');
    }

    public function test_game_details_page_with_existing_game(): void
    {
        $game = Game::create([
            'steam_app_id' => 730,
            'name' => 'Counter-Strike 2',
            'is_free' => true,
            'current_price' => 0,
            'current_discount_percent' => 0,
        ]);

        $mock = Mockery::mock(SteamApiService::class);
        $mock->shouldReceive('getAppDetails')
            ->with(730)
            ->andReturn([
                'steam_app_id' => 730,
                'name' => 'Counter-Strike 2',
                'header_image_url' => null,
                'is_free' => true,
                'price' => null,
                'discount_percent' => 0,
                'short_description' => 'A free FPS game.',
                'genres' => ['Action', 'FPS'],
                'release_date' => '2023',
                'developers' => ['Valve'],
                'publishers' => ['Valve'],
            ]);

        $this->app->instance(SteamApiService::class, $mock);

        $response = $this->get('/games/730');
        $response->assertStatus(200);
        $response->assertSee('Counter-Strike 2');
    }

    public function test_pricing_page_loads(): void
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);
        $response->assertSee('Free');
        $response->assertSee('Starter');
        $response->assertSee('Pro');
        $response->assertSee('Enterprise');
    }

    public function test_dashboard_requires_auth(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
