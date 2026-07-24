<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ClientComment;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ClientProfileCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_view_client_profile_and_publish_one_public_comment(): void
    {
        $provider = User::factory()->provider()->create();
        $client = User::factory()->client()->create();
        $mission = Mission::factory()->create(['client_id' => $client->id]);

        $this->actingAs($provider)
            ->get("/clients/{$client->slug}/profile?mission={$mission->slug}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/ClientShow')
                ->where('client.id', $client->id)
                ->where('backMissionUrl', route('front.missions.show', ['mission' => $mission->slug]))
                ->has('comments', 0));

        $this->post("/clients/{$client->slug}/comments", [
            'comment' => 'Client ponctuel, disponible et très clair dans ses explications.',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('client_comments', [
            'client_id' => $client->id,
            'commenter_id' => $provider->id,
        ]);
    }

    public function test_provider_cannot_open_client_profile_without_the_source_mission(): void
    {
        $provider = User::factory()->provider()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($provider)
            ->get("/clients/{$client->slug}/profile")
            ->assertForbidden();
    }

    public function test_provider_who_applied_can_view_the_client_profile_after_mission_assignment(): void
    {
        $provider = User::factory()->provider()->create();
        $selectedProvider = User::factory()->provider()->create();
        $client = User::factory()->client()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'prestataire_id' => $selectedProvider->id,
            'status' => 'in_progress',
        ]);
        Application::query()->create([
            'mission_id' => $mission->id,
            'worker_id' => $provider->id,
            'proposed_price' => 50000,
            'pricing_type' => 'global',
            'status' => 'refusee',
        ]);

        $this->actingAs($provider)
            ->get("/clients/{$client->slug}/profile?mission={$mission->slug}")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Profile/ClientShow')
                ->where('client.id', $client->id));
    }

    public function test_provider_cannot_modify_client_profile_information(): void
    {
        $provider = User::factory()->provider()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($provider)
            ->patchJson("/api/users/{$client->id}", [
                'first_name' => 'Nom modifié',
                'last_name' => $client->last_name,
                'email' => $client->email,
            ])
            ->assertForbidden();

        $this->assertNotSame('Nom modifié', $client->fresh()->first_name);
    }

    public function test_client_comments_are_visible_to_admin_but_not_to_clients(): void
    {
        $provider = User::factory()->provider()->create();
        $client = User::factory()->client()->create();
        $otherClient = User::factory()->client()->create();
        $admin = User::factory()->admin()->create();
        ClientComment::query()->create([
            'client_id' => $client->id,
            'commenter_id' => $provider->id,
            'comment' => 'Communication professionnelle et paiement effectué rapidement.',
        ]);

        $this->actingAs($admin)
            ->get("/clients/{$client->slug}/profile")
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->has('comments', 1));

        $this->actingAs($otherClient)
            ->get("/clients/{$client->slug}/profile")
            ->assertForbidden();
    }
}
