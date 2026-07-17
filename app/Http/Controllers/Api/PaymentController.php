<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Payment;
use App\Services\Payments\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentGatewayService $gateway) {}

    public function store(Request $request, Mission $mission): JsonResponse
    {
        abort_unless(
            $mission->client_id === $request->user()->id
            && $mission->status === 'in_progress'
            && $mission->prestataire_id
            && $mission->price > 0,
            403
        );

        $data = $request->validate(['method' => ['required', 'in:orange_money,moov_money,carte,paypal']]);
        if ($data['method'] !== 'paypal' && ((int) $mission->price % 5 !== 0 || (float) $mission->price !== (float) (int) $mission->price)) {
            return response()->json(['message' => 'Le montant doit être un nombre entier multiple de 5 FCFA.'], 422);
        }
        if ($mission->payments()->where('status', 'effectue')->exists()) {
            return response()->json(['message' => 'Cette mission est déjà payée.'], 409);
        }
        $pendingPayment = $mission->payments()
            ->where('payer_id', $request->user()->id)
            ->where('method', $data['method'])
            ->where('status', 'en_attente')
            ->whereNotNull('payment_url')
            ->latest()
            ->first();
        if ($pendingPayment) {
            return response()->json([
                'payment' => $this->payload($pendingPayment),
                'payment_url' => $pendingPayment->payment_url,
            ]);
        }

        $payment = Payment::create([
            'mission_id' => $mission->id,
            'payer_id' => $request->user()->id,
            'receiver_id' => $mission->prestataire_id,
            'amount' => $mission->price,
            'status' => 'en_attente',
            'method' => $data['method'],
            'provider' => $data['method'] === 'paypal' ? 'paypal' : 'cinetpay',
            'transaction_id' => Str::upper(Str::random(20)),
        ]);

        try {
            $paymentUrl = $this->gateway->initialize(
                $payment,
                route('api.payments.mobile.return', $payment),
                route('api.payments.mobile.cancel', $payment),
            );
        } catch (Throwable $exception) {
            report($exception);
            $payment->update(['status' => 'echoue']);

            return response()->json(['message' => $exception->getMessage()], 502);
        }

        return response()->json(['payment' => $this->payload($payment->fresh()), 'payment_url' => $paymentUrl], 201);
    }

    public function show(Request $request, Payment $payment): JsonResponse
    {
        abort_unless($payment->payer_id === $request->user()->id, 403);

        return response()->json(['payment' => $this->payload($payment->fresh())]);
    }

    public function returned(Payment $payment): RedirectResponse
    {
        if ($payment->provider === 'paypal' && $payment->status === 'en_attente' && $this->gateway->capturePayPal($payment)) {
            $payment->markAsPaid();
        }

        return redirect()->away("barasira://payments/{$payment->id}");
    }

    public function cancelled(Payment $payment): RedirectResponse
    {
        if ($payment->status === 'en_attente') {
            $payment->update(['status' => 'annule']);
        }

        return redirect()->away("barasira://payments/{$payment->id}");
    }

    private function payload(Payment $payment): array
    {
        return $payment->only('id', 'mission_id', 'amount', 'status', 'method', 'provider', 'paid_at');
    }
}
