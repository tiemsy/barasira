<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminDocumentationTest extends TestCase
{
    public function test_admin_and_superadmin_can_view_documentation_catalog(): void
    {
        foreach (['admin', 'superadmin'] as $role) {
            $user = new User([
                'first_name' => 'Documentation',
                'last_name' => 'Admin',
                'email' => "{$role}@barasira.test",
                'locale' => 'fr',
                'role' => $role,
            ]);
            $user->id = $role === 'admin' ? 1 : 2;

            $this->actingAs($user)
                ->get('/admin/documentation')
                ->assertOk()
                ->assertInertia(fn ($page) => $page
                    ->component('Admin/Documentation/Index')
                    ->has('documents', 6)
                    ->where('documents.0.key', 'technical-specifications')
                    ->where('documents.0.available', true)
                    ->has('swaggerUrl'));
        }
    }

    public function test_client_cannot_access_admin_documentation(): void
    {
        $client = new User([
            'first_name' => 'Documentation',
            'last_name' => 'Client',
            'email' => 'client@barasira.test',
            'locale' => 'fr',
            'role' => 'client',
        ]);
        $client->id = 3;

        $this->actingAs($client)->get('/admin/documentation')->assertForbidden();
        $this->actingAs($client)->get('/admin/documentation/business-model')->assertForbidden();
    }

    public function test_admin_can_view_and_download_only_whitelisted_documentation(): void
    {
        $admin = new User([
            'first_name' => 'Documentation',
            'last_name' => 'Admin',
            'email' => 'admin@barasira.test',
            'locale' => 'fr',
            'role' => 'admin',
        ]);
        $admin->id = 4;

        $this->actingAs($admin)
            ->get('/admin/documentation/business-model')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($admin)
            ->get('/admin/documentation/openapi-yaml?download=1')
            ->assertOk()
            ->assertDownload('api-docs.yaml');

        $this->actingAs($admin)
            ->get('/admin/documentation/composer-json')
            ->assertNotFound();
    }
}
