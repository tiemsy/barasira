<?php

namespace Tests\Feature;

use App\Models\Mission;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReviewAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('authorizedReviewerRoles')]
    public function test_authorized_roles_can_rate_a_provider(string $role): void
    {
        $reviewer = User::factory()->{$role}()->create();
        $provider = User::factory()->provider()->create();
        $mission = Mission::factory()->completed($provider)->create([
            'client_id' => $role === 'client'
                ? $reviewer->id
                : User::factory()->client()->create()->id,
        ]);

        $this->actingAs($reviewer)
            ->postJson('/api/reviews', [
                'mission_id' => $mission->id,
                'rating' => 4,
                'comment' => 'Très bon travail.',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('reviews', [
            'mission_id' => $mission->id,
            'reviewer_id' => $reviewer->id,
            'reviewed_id' => $provider->id,
            'rating' => 4,
        ]);
    }

    public static function authorizedReviewerRoles(): array
    {
        return [
            'client' => ['client'],
            'admin' => ['admin'],
            'superadmin' => ['superAdmin'],
        ];
    }

    public function test_provider_cannot_rate_another_provider(): void
    {
        $reviewer = User::factory()->provider()->create();
        $provider = User::factory()->provider()->create();
        $mission = Mission::factory()->completed($provider)->create();

        $this->actingAs($reviewer)
            ->postJson('/api/reviews', [
                'mission_id' => $mission->id,
                'rating' => 5,
            ])
            ->assertForbidden();

        $this->assertSame(0, Review::query()->count());
    }
}
