<?php

namespace Tests\Feature;

use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissionHoursTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_increase_hours_before_payment(): void
    {
        $client = User::factory()->client()->create();
        $provider = User::factory()->provider()->create();
        $mission = Mission::factory()->assigned($provider)->create([
            'client_id' => $client->id,
            'initial_hours' => 2,
            'billable_hours' => 2,
        ]);

        $this->actingAs($client)
            ->patch("/missions/{$mission->id}/hours", ['billable_hours' => 3.5])
            ->assertSessionHasNoErrors();

        $this->assertSame('2.00', $mission->fresh()->initial_hours);
        $this->assertSame('3.50', $mission->fresh()->billable_hours);
    }

    public function test_client_cannot_decrease_hours_below_current_or_initial_duration(): void
    {
        $client = User::factory()->client()->create();
        $provider = User::factory()->provider()->create();
        $mission = Mission::factory()->assigned($provider)->create([
            'client_id' => $client->id,
            'initial_hours' => 2,
            'billable_hours' => 3,
        ]);

        $this->actingAs($client)
            ->patch("/missions/{$mission->id}/hours", ['billable_hours' => 2.5])
            ->assertSessionHasErrors('billable_hours');

        $this->assertSame('3.00', $mission->fresh()->billable_hours);
    }
}
