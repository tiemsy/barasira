<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_contains_search_metadata_and_structured_data(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('<meta inertia="description" name="description"', false)
            ->assertSee('<link inertia="canonical" rel="canonical"', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('index,follow,max-image-preview:large', false);
    }

    public function test_private_page_is_not_indexable(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('noindex,nofollow', false);
    }

    public function test_sitemap_lists_active_services_only(): void
    {
        $active = Service::factory()->create(['name' => 'Plomberie Bamako', 'is_active' => true]);
        $inactive = Service::factory()->create(['name' => 'Ancien service', 'is_active' => false]);

        $this->get(route('seo.sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('front.services.show', $active), false)
            ->assertDontSee(route('front.services.show', $inactive), false);
    }
}
