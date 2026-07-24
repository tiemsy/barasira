<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_impersonate_a_user_and_return_to_their_account(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($superAdmin)
            ->post("/admin/users/{$client->id}/impersonate")
            ->assertRedirect(route('client.dashboard'))
            ->assertSessionHas('impersonator.id', $superAdmin->id)
            ->assertSessionHas('impersonator.target_id', $client->id)
            ->assertSessionHas('impersonator.role', 'superadmin')
            ->assertSessionHas('impersonator.active', true);

        $this->assertAuthenticatedAs($client);

        $this->post('/impersonation/stop')
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionMissing('impersonator');

        $this->assertAuthenticatedAs($superAdmin);
    }

    public function test_non_superadmin_cannot_impersonate_another_user(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($admin)
            ->post("/admin/users/{$client->id}/impersonate")
            ->assertForbidden();

        $this->assertAuthenticatedAs($admin);
    }

    public function test_logout_clears_active_impersonation(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($superAdmin)->post("/admin/users/{$client->id}/impersonate");

        $this->post('/logout')
            ->assertRedirect('/')
            ->assertSessionMissing('impersonator');

        $this->assertGuest();
    }

    public function test_stale_impersonation_session_does_not_block_superadmin_from_changing_account(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($superAdmin)
            ->withSession([
                'impersonator' => [
                    'id' => $superAdmin->id,
                    'name' => $superAdmin->first_name,
                ],
            ])
            ->post("/admin/users/{$client->id}/impersonate")
            ->assertRedirect(route('client.dashboard'))
            ->assertSessionHas('impersonator.target_id', $client->id)
            ->assertSessionHas('impersonator.active', true);

        $this->assertAuthenticatedAs($client);
    }
}
