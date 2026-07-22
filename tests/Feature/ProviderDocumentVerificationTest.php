<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProviderDocumentVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_upload_a_private_identity_document(): void
    {
        Storage::fake('local');
        $provider = User::factory()->provider()->create();

        $this->actingAs($provider)->post('/profile/documents', [
            'document_type' => 'identity',
            'file' => UploadedFile::fake()->create('identite.pdf', 200, 'application/pdf'),
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $document = Document::query()->firstOrFail();
        $this->assertSame('en_attente', $document->status);
        $this->assertSame($provider->id, $document->user_id);
        Storage::disk('local')->assertExists($document->file_url);
    }

    public function test_client_cannot_upload_provider_documents(): void
    {
        Storage::fake('local');

        $this->actingAs(User::factory()->client()->create())->post('/profile/documents', [
            'document_type' => 'identity',
            'file' => UploadedFile::fake()->create('identite.pdf', 100, 'application/pdf'),
        ])->assertForbidden();
    }

    public function test_admin_can_approve_or_reject_a_provider_document(): void
    {
        $provider = User::factory()->provider()->create();
        $admin = User::factory()->admin()->create();
        $document = Document::query()->create([
            'user_id' => $provider->id, 'document_type' => 'diploma', 'file_url' => 'private/diplome.pdf',
            'original_name' => 'diplome.pdf', 'status' => 'en_attente', 'uploaded_at' => now(),
        ]);

        $this->actingAs($admin)->patch("/admin/documents/{$document->id}/review", [
            'status' => 'valide',
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $document->refresh();
        $this->assertSame('valide', $document->status);
        $this->assertSame($admin->id, $document->reviewed_by);
        $this->assertNotNull($document->reviewed_at);
        $this->assertNull($provider->fresh()->identity_verified_at, 'A diploma must not verify the provider identity.');
    }

    public function test_validating_an_identity_document_awards_the_verified_profile_badge(): void
    {
        $provider = User::factory()->provider()->create();
        $document = Document::query()->create([
            'user_id' => $provider->id, 'document_type' => 'identity', 'file_url' => 'private/identite.pdf',
            'original_name' => 'identite.pdf', 'status' => 'en_attente', 'uploaded_at' => now(),
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->patch("/admin/documents/{$document->id}/review", ['status' => 'valide'])
            ->assertSessionHasNoErrors();

        $this->assertNotNull($provider->fresh()->identity_verified_at);
    }

    public function test_rejecting_the_last_valid_identity_removes_the_verified_profile_badge(): void
    {
        $provider = User::factory()->provider()->create();
        $document = Document::query()->create([
            'user_id' => $provider->id, 'document_type' => 'identity', 'file_url' => 'private/identite.pdf',
            'original_name' => 'identite.pdf', 'status' => 'valide', 'reviewed_at' => now(), 'uploaded_at' => now(),
        ]);
        $provider->syncIdentityVerification();
        $this->assertNotNull($provider->fresh()->identity_verified_at);

        $this->actingAs(User::factory()->superAdmin()->create())
            ->patch("/admin/documents/{$document->id}/review", ['status' => 'rejete', 'review_comment' => 'Document illisible.'])
            ->assertSessionHasNoErrors();

        $this->assertNull($provider->fresh()->identity_verified_at);
    }

    public function test_admin_can_filter_documents_by_the_providers_full_name(): void
    {
        $awa = User::factory()->provider()->create(['first_name' => 'Awa', 'last_name' => 'Traoré']);
        $binta = User::factory()->provider()->create(['first_name' => 'Binta', 'last_name' => 'Coulibaly']);

        foreach ([[$awa, 'awa.pdf'], [$binta, 'binta.pdf']] as [$provider, $filename]) {
            Document::query()->create([
                'user_id' => $provider->id, 'document_type' => 'identity', 'file_url' => "private/{$filename}",
                'original_name' => $filename, 'status' => 'en_attente', 'uploaded_at' => now(),
            ]);
        }

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/documents?provider=Awa%20Traoré')
            ->assertOk()
            ->assertSee('awa.pdf')
            ->assertDontSee('binta.pdf');
    }

    public function test_superadmin_can_combine_provider_name_and_service_category_filters(): void
    {
        $plumbing = ServiceCategory::factory()->create(['name' => 'Plomberie']);
        $electricity = ServiceCategory::factory()->create(['name' => 'Électricité']);
        $awa = User::factory()->provider()->create(['first_name' => 'Awa', 'last_name' => 'Traoré']);
        $binta = User::factory()->provider()->create(['first_name' => 'Binta', 'last_name' => 'Traoré']);
        Service::factory()->create(['user_id' => $awa->id, 'service_category_id' => $plumbing->id]);
        Service::factory()->create(['user_id' => $binta->id, 'service_category_id' => $electricity->id]);

        foreach ([[$awa, 'awa-plomberie.pdf'], [$binta, 'binta-electricite.pdf']] as [$provider, $filename]) {
            Document::query()->create([
                'user_id' => $provider->id, 'document_type' => 'certification', 'file_url' => "private/{$filename}",
                'original_name' => $filename, 'status' => 'en_attente', 'uploaded_at' => now(),
            ]);
        }

        $this->actingAs(User::factory()->superAdmin()->create())
            ->get("/admin/documents?provider=Traoré&service_category={$plumbing->id}")
            ->assertOk()
            ->assertSee('awa-plomberie.pdf')
            ->assertDontSee('binta-electricite.pdf');
    }

    public function test_rejection_requires_an_explanation(): void
    {
        $provider = User::factory()->provider()->create();
        $document = Document::query()->create([
            'user_id' => $provider->id, 'document_type' => 'certification', 'file_url' => 'private/certification.pdf',
            'status' => 'en_attente', 'uploaded_at' => now(),
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->patch("/admin/documents/{$document->id}/review", ['status' => 'rejete'])
            ->assertSessionHasErrors('review_comment');
    }

    public function test_provider_cannot_download_another_providers_document(): void
    {
        $owner = User::factory()->provider()->create();
        $other = User::factory()->provider()->create();
        $document = Document::query()->create([
            'user_id' => $owner->id, 'document_type' => 'identity', 'file_url' => 'private/identite.pdf',
            'status' => 'en_attente', 'uploaded_at' => now(),
        ]);

        $this->actingAs($other)->get("/profile/documents/{$document->id}")->assertForbidden();
    }

    public function test_admin_can_move_a_validated_identity_document_back_to_pending(): void
    {
        $provider = User::factory()->provider()->create();
        $document = Document::query()->create([
            'user_id' => $provider->id, 'document_type' => 'identity', 'file_url' => 'provider-documents/identity.pdf',
            'status' => 'valide', 'reviewed_by' => User::factory()->admin()->create()->id,
            'reviewed_at' => now(), 'uploaded_at' => now(),
        ]);
        $provider->syncIdentityVerification();

        $this->actingAs(User::factory()->admin()->create())
            ->patch("/admin/documents/{$document->id}/review", ['status' => 'en_attente'])
            ->assertSessionHasNoErrors();

        $document->refresh();
        $this->assertSame('en_attente', $document->status);
        $this->assertNull($document->reviewed_by);
        $this->assertNull($document->reviewed_at);
        $this->assertNull($provider->fresh()->identity_verified_at);
    }

    public function test_admin_can_delete_a_validated_document_and_its_file(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('provider-documents/identity.pdf', 'identity');
        $provider = User::factory()->provider()->create();
        $document = Document::query()->create([
            'user_id' => $provider->id, 'document_type' => 'identity', 'file_url' => 'provider-documents/identity.pdf',
            'status' => 'valide', 'reviewed_at' => now(), 'uploaded_at' => now(),
        ]);
        $provider->syncIdentityVerification();

        $this->actingAs(User::factory()->admin()->create())
            ->delete("/admin/documents/{$document->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing('provider-documents/identity.pdf');
        $this->assertNull($provider->fresh()->identity_verified_at);
    }
}
