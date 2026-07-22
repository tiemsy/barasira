<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_each_legal_document_is_publicly_available(): void
    {
        foreach (['cgu', 'cgv', 'confidentialite', 'cookies', 'moderation', 'kyc'] as $document) {
            $this->get("/legal/{$document}")->assertOk()->assertInertia(fn (Assert $page) => $page
                ->component('Legal/Show')
                ->where('documentKey', $document)
                ->has('document.title')
                ->has('document.sections'));
        }
    }

    public function test_unknown_legal_document_returns_not_found(): void
    {
        $this->get('/legal/inconnu')->assertNotFound();
    }
}
