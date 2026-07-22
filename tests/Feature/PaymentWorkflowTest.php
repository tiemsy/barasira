<?php

namespace Tests\Feature;

use App\Models\Mission;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_allocates_ten_percent_to_barasira_and_ninety_percent_to_provider(): void
    {
        $mission = Mission::factory()->completed()->create(['price' => 10000]);
        $payment = Payment::factory()->create([
            'mission_id' => $mission->id,
            'amount' => 10000,
        ]);

        $this->assertSame('1000.00', $payment->fresh()->platform_fee);
        $this->assertSame('9000.00', $payment->fresh()->provider_amount);
    }

    public function test_client_can_open_payment_selection_for_an_in_progress_mission(): void
    {
        $client = User::factory()->client()->create();
        $mission = Mission::factory()->assigned()->create(['client_id' => $client->id]);

        $this->actingAs($client)
            ->get(route('payments.select', $mission))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Payments/SelectMethod')
                ->where('mission.id', $mission->id));
    }

    public function test_payment_cannot_be_started_for_a_mission_that_is_not_in_progress(): void
    {
        $client = User::factory()->client()->create();
        $mission = Mission::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client)
            ->get(route('payments.select', $mission))
            ->assertForbidden();

        Sanctum::actingAs($client);
        $this->postJson("/api/missions/{$mission->id}/payments", ['method' => 'orange_money'])
            ->assertForbidden();
    }

    public function test_provider_cannot_complete_the_mission_instead_of_the_client_payment_flow(): void
    {
        $provider = User::factory()->provider()->create();
        $mission = Mission::factory()->assigned($provider)->create();
        Sanctum::actingAs($provider);

        $this->patchJson("/api/missions/{$mission->id}", ['status' => 'completed'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseHas('missions', ['id' => $mission->id, 'status' => 'in_progress']);
    }

    public function test_confirmed_cinetpay_payment_completes_the_payment_and_mission(): void
    {
        $mission = Mission::factory()->assigned()->create(['price' => 25000]);
        $payment = Payment::factory()->create([
            'mission_id' => $mission->id,
            'provider' => 'cinetpay',
            'method' => 'orange_money',
            'status' => 'en_attente',
        ]);

        Http::fake([
            '*/payment/check' => Http::response([
                'data' => ['status' => 'ACCEPTED', 'amount' => 25000, 'currency' => 'XOF'],
            ]),
        ]);

        $this->postJson(route('payments.webhooks.cinetpay'), ['cpm_trans_id' => $payment->transaction_id])
            ->assertNoContent();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'effectue']);
        $this->assertDatabaseHas('missions', ['id' => $mission->id, 'status' => 'completed']);
        $this->assertNotNull($payment->fresh()->paid_at);
    }

    public function test_rejected_gateway_confirmation_does_not_complete_the_mission(): void
    {
        $mission = Mission::factory()->assigned()->create(['price' => 25000]);
        $payment = Payment::factory()->create([
            'mission_id' => $mission->id,
            'provider' => 'cinetpay',
            'status' => 'en_attente',
        ]);

        Http::fake([
            '*/payment/check' => Http::response([
                'data' => ['status' => 'REFUSED', 'amount' => 25000, 'currency' => 'XOF'],
            ]),
        ]);

        $this->postJson(route('payments.webhooks.cinetpay'), ['cpm_trans_id' => $payment->transaction_id])
            ->assertNoContent();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'en_attente']);
        $this->assertDatabaseHas('missions', ['id' => $mission->id, 'status' => 'in_progress']);
    }

    public function test_client_cannot_cancel_while_a_payment_is_pending_confirmation(): void
    {
        $client = User::factory()->client()->create();
        $mission = Mission::factory()->assigned()->create(['client_id' => $client->id]);
        Payment::factory()->create(['mission_id' => $mission->id, 'status' => 'en_attente']);
        Sanctum::actingAs($client);

        $this->patchJson("/api/missions/{$mission->id}", ['status' => 'cancelled'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseHas('missions', ['id' => $mission->id, 'status' => 'in_progress']);
    }
}
