<?php

namespace Tests\Feature;

use App\Models\Mission;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_show_routes_use_the_slug(): void
    {
        $service = Service::factory()->create(['name' => 'Plomberie urgence']);

        $this->get("/services/{$service->slug}")->assertOk();
        $this->getJson("/api/services/{$service->slug}")
            ->assertOk()
            ->assertJsonPath('slug', 'plomberie-urgence');
        $this->get("/services/{$service->id}")->assertNotFound();
    }

    public function test_mission_show_routes_use_the_slug(): void
    {
        $client = User::factory()->client()->create();
        $mission = Mission::factory()->create([
            'client_id' => $client->id,
            'title' => 'Réparer la toiture',
        ]);

        $this->actingAs($client)
            ->get("/missions/{$mission->slug}")
            ->assertOk();
        $this->actingAs($client, 'sanctum')
            ->getJson("/api/missions/{$mission->slug}")
            ->assertOk()
            ->assertJsonPath('mission.slug', 'reparer-la-toiture');
        $this->actingAs($client)
            ->get("/missions/{$mission->id}")
            ->assertNotFound();
    }

    public function test_duplicate_titles_receive_unique_slugs(): void
    {
        $first = Service::factory()->create(['name' => 'Peinture']);
        $second = Service::factory()->create(['name' => 'Peinture']);

        $this->assertSame('peinture', $first->slug);
        $this->assertSame('peinture-2', $second->slug);
    }
}
