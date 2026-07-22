<?php

namespace Tests\Feature;

use App\Models\Mission;
use App\Models\Partner;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_each_resource_for_excel(): void
    {
        $admin = User::factory()->admin()->create();
        Service::factory()->create();
        Mission::factory()->create();
        Partner::query()->create(['company_name' => 'Partenaire exporté', 'contact_name' => 'Contact']);

        foreach (['users', 'services', 'missions', 'partners'] as $resource) {
            $response = $this->actingAs($admin)->get("/admin/exports/{$resource}");

            $response->assertOk();
            $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
            $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));
        }
    }

    public function test_partner_export_uses_the_active_filters(): void
    {
        $admin = User::factory()->admin()->create();
        Partner::query()->create(['company_name' => 'Visible Excel', 'contact_name' => 'Awa', 'is_published' => true]);
        Partner::query()->create(['company_name' => 'Masqué Excel', 'contact_name' => 'Binta', 'is_published' => false]);

        $content = $this->actingAs($admin)
            ->get('/admin/exports/partners?search=Visible&status=published')
            ->streamedContent();

        $this->assertStringContainsString('Visible Excel', $content);
        $this->assertStringNotContainsString('Masqué Excel', $content);
    }

    public function test_superadmin_can_export_but_a_client_cannot(): void
    {
        $this->actingAs(User::factory()->superAdmin()->create())->get('/admin/exports/users')->assertOk();
        $this->actingAs(User::factory()->client()->create())->get('/admin/exports/users')->assertForbidden();
    }
}
