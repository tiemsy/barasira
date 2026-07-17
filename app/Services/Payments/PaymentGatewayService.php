<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaymentGatewayService
{
    public function initialize(Payment $payment, ?string $returnUrl = null, ?string $cancelUrl = null): string
    {
        return $payment->provider === 'paypal'
            ? $this->paypal($payment, $returnUrl, $cancelUrl)
            : $this->cinetPay($payment, $returnUrl);
    }

    public function verifyCinetPay(Payment $payment): bool
    {
        $response = Http::asJson()->post(config('services.cinetpay.base_url').'/payment/check', [
            'apikey' => config('services.cinetpay.api_key'), 'site_id' => config('services.cinetpay.site_id'),
            'transaction_id' => $payment->transaction_id,
        ])->throw()->json();

        $payment->update(['provider_data' => $response]);

        return data_get($response, 'data.status') === 'ACCEPTED'
            && (int) data_get($response, 'data.amount') === (int) $payment->amount
            && data_get($response, 'data.currency') === 'XOF';
    }

    public function capturePayPal(Payment $payment): bool
    {
        $token = $this->paypalToken();
        $response = Http::withToken($token)->withHeaders(['PayPal-Request-Id' => $payment->transaction_id])
            ->post(config('services.paypal.base_url')."/v2/checkout/orders/{$payment->transaction_id}/capture")->throw()->json();
        $payment->update(['provider_data' => $response]);

        $capturedAmount = data_get($response, 'purchase_units.0.payments.captures.0.amount');
        $expectedValue = number_format(
            $payment->amount / (float) config('services.paypal.xof_per_unit'),
            2,
            '.',
            ''
        );

        return data_get($response, 'status') === 'COMPLETED'
            && data_get($capturedAmount, 'currency_code') === config('services.paypal.currency')
            && data_get($capturedAmount, 'value') === $expectedValue;
    }

    private function cinetPay(Payment $payment, ?string $returnUrl = null): string
    {
        $config = config('services.cinetpay');
        if (! $config['api_key'] || ! $config['site_id']) {
            throw new RuntimeException('CinetPay n’est pas configuré.');
        }
        $user = $payment->payer;
        $channels = $payment->method === 'carte' ? 'CREDIT_CARD' : 'MOBILE_MONEY';
        $response = Http::asJson()->post($config['base_url'].'/payment', [
            'apikey' => $config['api_key'], 'site_id' => $config['site_id'], 'transaction_id' => $payment->transaction_id,
            'amount' => (int) $payment->amount, 'currency' => 'XOF', 'description' => 'Mission '.$payment->mission_id,
            'notify_url' => route('payments.webhooks.cinetpay'), 'return_url' => $returnUrl ?? route('payments.return', $payment),
            'channels' => $channels, 'metadata' => (string) $payment->id, 'customer_id' => (string) $user->id,
            'customer_name' => $user->last_name, 'customer_surname' => $user->first_name,
            'customer_email' => $user->email, 'customer_phone_number' => $user->phone, 'customer_country' => 'ML',
        ])->throw()->json();
        $url = data_get($response, 'data.payment_url');
        if (! $url) {
            throw new RuntimeException(data_get($response, 'description', 'Initialisation CinetPay impossible.'));
        }
        $payment->update(['payment_url' => $url, 'provider_data' => $response]);

        return $url;
    }

    private function paypal(Payment $payment, ?string $returnUrl = null, ?string $cancelUrl = null): string
    {
        $rate = (float) config('services.paypal.xof_per_unit');
        if ($rate <= 0) {
            throw new RuntimeException('Le taux de conversion PayPal n’est pas configuré.');
        }
        $response = Http::withToken($this->paypalToken())->withHeaders(['PayPal-Request-Id' => $payment->transaction_id])
            ->post(config('services.paypal.base_url').'/v2/checkout/orders', [
                'intent' => 'CAPTURE', 'purchase_units' => [[
                    'reference_id' => (string) $payment->id,
                    'amount' => ['currency_code' => config('services.paypal.currency'), 'value' => number_format($payment->amount / $rate, 2, '.', '')],
                ]],
                'payment_source' => ['paypal' => ['experience_context' => [
                    'return_url' => $returnUrl ?? route('payments.paypal.capture', $payment),
                    'cancel_url' => $cancelUrl ?? route('payments.return', $payment),
                ]]],
            ])->throw()->json();
        $orderId = data_get($response, 'id');
        $url = collect(data_get($response, 'links', []))->firstWhere('rel', 'payer-action')['href'] ?? null;
        if (! $orderId || ! $url) {
            throw new RuntimeException('Initialisation PayPal impossible.');
        }
        $payment->update(['transaction_id' => $orderId, 'payment_url' => $url, 'provider_data' => $response]);

        return $url;
    }

    private function paypalToken(): string
    {
        $config = config('services.paypal');
        if (! $config['client_id'] || ! $config['client_secret']) {
            throw new RuntimeException('PayPal n’est pas configuré.');
        }

        return Http::asForm()->withBasicAuth($config['client_id'], $config['client_secret'])
            ->post($config['base_url'].'/v1/oauth2/token', ['grant_type' => 'client_credentials'])->throw()->json('access_token');
    }
}
