<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_user_has_3_game_limit(): void
    {
        $user = User::factory()->create(['plan' => 'free']);
        $this->assertEquals(3, $user->gameLimit());
    }

    public function test_starter_user_has_10_game_limit(): void
    {
        $user = User::factory()->create(['plan' => 'starter']);
        $this->assertEquals(10, $user->gameLimit());
    }

    public function test_pro_user_has_50_game_limit(): void
    {
        $user = User::factory()->create(['plan' => 'pro']);
        $this->assertEquals(50, $user->gameLimit());
    }

    public function test_enterprise_user_has_no_limit(): void
    {
        $user = User::factory()->create(['plan' => 'enterprise']);
        $this->assertNull($user->gameLimit());
        $this->assertTrue($user->canAddGame());
    }

    public function test_free_user_has_correct_plan_config(): void
    {
        $user = User::factory()->create(['plan' => 'free']);
        $config = $user->planConfig();

        $this->assertFalse($config['email_notifications']);
        $this->assertFalse($config['telegram_notifications']);
        $this->assertEquals(7, $config['price_history_days']);
        $this->assertFalse($config['api_access']);
    }

    public function test_enterprise_user_has_all_features(): void
    {
        $user = User::factory()->create(['plan' => 'enterprise']);
        $config = $user->planConfig();

        $this->assertTrue($config['email_notifications']);
        $this->assertTrue($config['telegram_notifications']);
        $this->assertNull($config['price_history_days']);
        $this->assertTrue($config['api_access']);
        $this->assertTrue($config['webhooks']);
    }

    public function test_is_subscribed_returns_false_for_free(): void
    {
        $user = User::factory()->create(['plan' => 'free']);
        $this->assertFalse($user->isSubscribed());
    }

    public function test_is_subscribed_returns_true_for_active_paid(): void
    {
        $user = User::factory()->create([
            'plan' => 'pro',
            'subscription_status' => 'active',
        ]);
        $this->assertTrue($user->isSubscribed());
    }

    public function test_admin_page_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_page_accessible_for_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_api_requires_enterprise_plan(): void
    {
        $user = User::factory()->create(['plan' => 'free']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/watchlist');
        $response->assertStatus(403);
    }

    public function test_api_accessible_for_enterprise(): void
    {
        $user = User::factory()->create(['plan' => 'enterprise']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/watchlist');
        $response->assertStatus(200);
    }
}
