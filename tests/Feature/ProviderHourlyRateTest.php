<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderHourlyRateTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_update_hourly_rate_through_api(): void
    {
        $provider = User::factory()->provider()->create();

        $this->actingAs($provider)
            ->patchJson("/api/users/{$provider->id}", [
                'first_name' => $provider->first_name,
                'last_name' => $provider->last_name,
                'email' => $provider->email,
                'phone' => $provider->phone,
                'bio' => $provider->bio,
                'hourly_rate' => 7500,
            ])
            ->assertOk()
            ->assertJsonPath('user.hourly_rate', '7500.00');

        $this->assertSame('7500.00', $provider->fresh()->hourly_rate);
    }

    public function test_hourly_rate_is_required_when_provider_updates_profile(): void
    {
        $provider = User::factory()->provider()->create();

        $this->actingAs($provider)
            ->patchJson("/api/users/{$provider->id}", [
                'first_name' => $provider->first_name,
                'last_name' => $provider->last_name,
                'email' => $provider->email,
            ])
            ->assertJsonValidationErrors('hourly_rate');
    }
}
