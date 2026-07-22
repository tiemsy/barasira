<?php

namespace Tests\Feature;

use Database\Seeders\PlatformReviewSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformReviewSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_review_seeder_is_complete_and_idempotent(): void
    {
        $this->seed([UserSeeder::class, PlatformReviewSeeder::class]);
        $this->seed(PlatformReviewSeeder::class);

        $this->assertDatabaseCount('platform_reviews', 6);
        $this->assertDatabaseHas('platform_reviews', [
            'rating' => 5,
            'is_published' => true,
        ]);
    }
}
