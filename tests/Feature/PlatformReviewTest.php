<?php

namespace Tests\Feature;

use App\Models\PlatformReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_can_publish_and_update_one_platform_review(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'verified' => true]);

        $this->actingAs($user)->post(route('front.platform-reviews.store'), [
            'rating' => 5,
            'comment' => 'Une plateforme très utile pour trouver des prestataires.',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('front.platform-reviews.store'), [
            'rating' => 4,
            'comment' => 'Une plateforme utile avec une expérience agréable.',
        ])->assertRedirect();

        $this->assertDatabaseCount('platform_reviews', 1);
        $this->assertDatabaseHas('platform_reviews', ['user_id' => $user->id, 'rating' => 4]);
    }

    public function test_guest_cannot_publish_a_platform_review(): void
    {
        $this->post(route('front.platform-reviews.store'), [
            'rating' => 5,
            'comment' => 'Une expérience vraiment excellente sur Barasira.',
        ])->assertRedirect(route('login'));
    }

    public function test_public_page_only_displays_published_reviews(): void
    {
        $published = PlatformReview::query()->create([
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
            'comment' => 'Avis public visible sur la plateforme.',
            'is_published' => true,
        ]);
        PlatformReview::query()->create([
            'user_id' => User::factory()->create()->id,
            'rating' => 1,
            'comment' => 'Avis masqué qui ne doit pas apparaître.',
            'is_published' => false,
        ]);

        $this->get(route('front.platform-reviews.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('PlatformReviews/Index')
                ->has('reviews.data', 1)
                ->where('reviews.data.0.id', $published->id));
    }

    public function test_homepage_displays_at_most_three_published_reviews(): void
    {
        foreach (range(1, 4) as $index) {
            PlatformReview::query()->create([
                'user_id' => User::factory()->create()->id,
                'rating' => 4,
                'comment' => "Avis public numéro {$index} sur Barasira.",
                'is_published' => true,
            ]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('platformReviews', 3)
                ->where('platformReviewStats.count', 4)
                ->where('platformReviewStats.average', 4));
    }
}
