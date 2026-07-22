<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_stores_first_name_and_last_name(): void
    {
        $this->postJson('/api/register', [
            'first_name' => '  Aminata  ',
            'last_name' => '  Traoré  ',
            'email' => 'aminata@example.com',
            'phone' => '+22370000002',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'client',
        ])->assertCreated();

        $this->assertDatabaseHas('users', [
            'first_name' => 'Aminata',
            'last_name' => 'Traoré',
            'email' => 'aminata@example.com',
        ]);
    }

    public function test_registration_requires_first_name_and_last_name(): void
    {
        $this->postJson('/api/register', [
            'email' => 'aminata@example.com',
            'phone' => '+22370000002',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'client',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['first_name', 'last_name']);
    }
}
