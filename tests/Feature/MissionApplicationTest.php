<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Mission;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\ApplicationAcceptedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Tests\TestCase;

class MissionApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_see_all_available_missions_and_apply_without_being_assigned(): void
    {
        $provider = User::factory()->provider()->create();
        $mission = Mission::factory()->create();

        $this->actingAs($provider)
            ->getJson('/api/missions')
            ->assertOk()
            ->assertJsonPath('data.0.id', $mission->id);

        $this->actingAs($provider)
            ->postJson("/api/missions/{$mission->id}/applications", [
                'pricing_type' => 'hourly',
                'hourly_rate' => 5000,
            ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'en_attente');

        $this->assertDatabaseHas('applications', [
            'mission_id' => $mission->id,
            'worker_id' => $provider->id,
            'status' => 'en_attente',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $mission->client_id,
            'type' => 'mission_application',
            'read' => false,
        ]);
        $this->assertNull($mission->fresh()->prestataire_id);
        $this->assertSame('pending', $mission->fresh()->status);
    }

    public function test_provider_cannot_apply_twice_to_the_same_mission(): void
    {
        $provider = User::factory()->provider()->create();
        $mission = Mission::factory()->create();
        Application::query()->create([
            'mission_id' => $mission->id,
            'worker_id' => $provider->id,
            'status' => 'en_attente',
        ]);

        $this->actingAs($provider)
            ->postJson("/api/missions/{$mission->id}/applications", [
                'pricing_type' => 'global',
                'proposed_price' => 20000,
            ])
            ->assertUnprocessable();

        $this->assertSame(0, Notification::query()->count());
        $this->assertSame(1, Application::query()->count());
    }

    public function test_client_can_select_one_of_several_applicants_and_selected_provider_is_notified(): void
    {
        NotificationFacade::fake();
        $client = User::factory()->client()->create();
        $selectedProvider = User::factory()->provider()->create(['locale' => 'en']);
        $otherProvider = User::factory()->provider()->create();
        $mission = Mission::factory()->create(['client_id' => $client->id]);
        $selectedApplication = Application::query()->create([
            'mission_id' => $mission->id,
            'worker_id' => $selectedProvider->id,
            'status' => 'en_attente',
        ]);
        $otherApplication = Application::query()->create([
            'mission_id' => $mission->id,
            'worker_id' => $otherProvider->id,
            'status' => 'en_attente',
        ]);

        $this->actingAs($client)
            ->postJson("/api/missions/{$mission->id}/applications/{$selectedApplication->id}/accept")
            ->assertOk()
            ->assertJsonPath('data.prestataire_id', $selectedProvider->id)
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertSame('acceptee', $selectedApplication->fresh()->status);
        $this->assertSame('refusee', $otherApplication->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $selectedProvider->id,
            'type' => 'application_accepted',
            'title' => 'Application accepted',
            'message' => "Your application for “{$mission->title}” was accepted.",
            'read' => false,
        ]);
        NotificationFacade::assertSentTo($selectedProvider, ApplicationAcceptedNotification::class);
        NotificationFacade::assertNotSentTo($otherProvider, ApplicationAcceptedNotification::class);
    }

    public function test_provider_can_apply_to_multiple_missions_when_time_slots_do_not_overlap(): void
    {
        $provider = User::factory()->provider()->create();
        $firstMission = Mission::factory()->create([
            'date_start' => '2026-08-10 09:00:00',
            'date_end' => '2026-08-10 11:00:00',
        ]);
        $secondMission = Mission::factory()->create([
            'date_start' => '2026-08-10 12:00:00',
            'date_end' => '2026-08-10 14:00:00',
        ]);

        $payload = ['pricing_type' => 'hourly', 'hourly_rate' => 5000];
        $this->actingAs($provider)->postJson("/api/missions/{$firstMission->id}/applications", $payload)->assertCreated();
        $this->actingAs($provider)->postJson("/api/missions/{$secondMission->id}/applications", $payload)->assertCreated();

        $this->assertSame(2, Application::query()->where('worker_id', $provider->id)->count());
    }

    public function test_provider_cannot_apply_when_a_pending_application_overlaps_the_time_slot(): void
    {
        $provider = User::factory()->provider()->create();
        $firstMission = Mission::factory()->create([
            'date_start' => '2026-08-10 09:00:00',
            'date_end' => '2026-08-10 12:00:00',
        ]);
        $overlappingMission = Mission::factory()->create([
            'date_start' => '2026-08-10 11:00:00',
            'date_end' => '2026-08-10 13:00:00',
        ]);

        $payload = ['pricing_type' => 'global', 'proposed_price' => 25000];
        $this->actingAs($provider)->postJson("/api/missions/{$firstMission->id}/applications", $payload)->assertCreated();
        $this->actingAs($provider)
            ->postJson("/api/missions/{$overlappingMission->id}/applications", $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('mission');

        $this->assertSame(1, Application::query()->where('worker_id', $provider->id)->count());
    }
}
