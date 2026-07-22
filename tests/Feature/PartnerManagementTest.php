<?php

namespace Tests\Feature;

use App\Models\Partner;
use App\Models\PartnerPromotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PartnerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_published_partner_with_a_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/partners', [
            'company_name' => 'Mali Énergie', 'description' => 'Partenaire énergie.',
            'logo' => UploadedFile::fake()->image('logo.png'), 'website_url' => 'https://example.com',
            'company_email' => 'contact@example.com', 'company_phone' => '+22370000000',
            'address' => 'Bamako', 'contact_name' => 'Awa Traoré', 'contact_position' => 'Direction',
            'contact_email' => 'awa@example.com', 'contact_phone' => '+22371000000',
            'is_published' => '1', 'display_order' => 1,
            'promotion_amount' => 750000,
            'promotion_starts_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'promotion_ends_at' => now()->addWeek()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect('/admin/partners');
        $partner = Partner::query()->firstOrFail();
        $this->assertTrue($partner->is_published);
        $this->assertSame('750000.00', $partner->promotions()->firstOrFail()->paid_amount);
        Storage::disk('public')->assertExists($partner->logo_path);
    }

    public function test_public_pages_only_expose_published_partner_information(): void
    {
        Partner::query()->create($this->partnerData(['company_name' => 'Visible', 'is_published' => true]));
        Partner::query()->create($this->partnerData(['company_name' => 'Privé', 'is_published' => false]));

        $this->get('/partners')->assertOk()->assertSee('Visible')->assertDontSee('Privé')->assertDontSee('private-contact@example.com');
        $this->get('/')->assertOk()->assertSee('Visible')->assertDontSee('Privé')->assertDontSee('private-contact@example.com');
    }

    public function test_clients_cannot_manage_partners(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/admin/partners')->assertForbidden();
    }

    public function test_superadmin_can_manage_partners(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())->get('/admin/partners')->assertOk();
    }

    public function test_home_features_at_most_two_active_partners_ranked_by_amount_paid(): void
    {
        $low = Partner::query()->create($this->partnerData(['company_name' => 'Petit budget', 'is_published' => true]));
        $high = Partner::query()->create($this->partnerData(['company_name' => 'Grand budget', 'is_published' => true]));
        $middle = Partner::query()->create($this->partnerData(['company_name' => 'Budget moyen', 'is_published' => true]));
        $expired = Partner::query()->create($this->partnerData(['company_name' => 'Campagne terminée', 'is_published' => true]));

        foreach ([[$low, 100000], [$high, 900000], [$middle, 500000]] as [$partner, $amount]) {
            PartnerPromotion::query()->create([
                'partner_id' => $partner->id, 'paid_amount' => $amount,
                'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(),
            ]);
        }
        PartnerPromotion::query()->create([
            'partner_id' => $expired->id, 'paid_amount' => 2000000,
            'starts_at' => now()->subDays(3), 'ends_at' => now()->subDay(),
        ]);

        $this->get('/')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->has('featuredPartners', 2)
            ->where('featuredPartners.0.company_name', 'Grand budget')
            ->where('featuredPartners.1.company_name', 'Budget moyen')
            ->missing('featuredPartners.0.paid_amount'));
    }

    private function partnerData(array $overrides = []): array
    {
        return array_merge([
            'company_name' => 'Partenaire', 'contact_name' => 'Contact privé',
            'contact_email' => 'private-contact@example.com', 'is_published' => false, 'display_order' => 0,
        ], $overrides);
    }
}
