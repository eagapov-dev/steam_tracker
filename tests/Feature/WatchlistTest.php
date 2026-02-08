<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use App\Models\Watchlist;
use App\Services\SteamApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WatchlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_watchlist(): void
    {
        $response = $this->get('/watchlist');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_watchlist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/watchlist');
        $response->assertStatus(200);
    }

    public function test_user_can_add_game_to_watchlist(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        $game = Game::create([
            'steam_app_id' => 730,
            'name' => 'Counter-Strike 2',
            'is_free' => true,
            'current_price' => 0,
            'current_discount_percent' => 0,
        ]);

        $mock = Mockery::mock(SteamApiService::class);
        $mock->shouldReceive('syncGameData')->andReturn($game);
        $this->app->instance(SteamApiService::class, $mock);

        $response = $this->actingAs($user)->post('/watchlist/730');
        $response->assertRedirect();

        $this->assertDatabaseHas('watchlist', [
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);
    }

    public function test_free_user_cannot_exceed_game_limit(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        // Add 3 games (free limit)
        for ($i = 1; $i <= 3; $i++) {
            $game = Game::create([
                'steam_app_id' => $i,
                'name' => "Game {$i}",
                'is_free' => false,
                'current_price' => 9.99,
                'current_discount_percent' => 0,
            ]);

            Watchlist::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
            ]);
        }

        // Try adding 4th game
        $game4 = Game::create([
            'steam_app_id' => 4,
            'name' => 'Game 4',
            'is_free' => false,
            'current_price' => 19.99,
            'current_discount_percent' => 0,
        ]);

        $mock = Mockery::mock(SteamApiService::class);
        $mock->shouldReceive('syncGameData')->andReturn($game4);
        $this->app->instance(SteamApiService::class, $mock);

        $response = $this->actingAs($user)->post('/watchlist/4');
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('watchlist', [
            'user_id' => $user->id,
            'game_id' => $game4->id,
        ]);
    }

    public function test_user_can_remove_game_from_watchlist(): void
    {
        $user = User::factory()->create();
        $game = Game::create([
            'steam_app_id' => 730,
            'name' => 'Counter-Strike 2',
            'is_free' => true,
            'current_price' => 0,
            'current_discount_percent' => 0,
        ]);

        Watchlist::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($user)->delete('/watchlist/730');
        $response->assertRedirect();

        $this->assertDatabaseMissing('watchlist', [
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);
    }

    public function test_user_can_update_target_price(): void
    {
        $user = User::factory()->create();
        $game = Game::create([
            'steam_app_id' => 440,
            'name' => 'Team Fortress 2',
            'is_free' => true,
            'current_price' => 0,
            'current_discount_percent' => 0,
        ]);

        Watchlist::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $response = $this->actingAs($user)->patch('/watchlist/440', [
            'target_price' => 4.99,
            'notify_any_discount' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('watchlist', [
            'user_id' => $user->id,
            'game_id' => $game->id,
            'target_price' => 4.99,
            'notify_any_discount' => false,
        ]);
    }
}
