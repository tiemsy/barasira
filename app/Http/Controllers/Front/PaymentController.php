<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Payment;
use App\Services\MissionImageService;
use App\Services\Payments\PaymentGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayService $gateway,
        private readonly MissionImageService $missionImages,
    ) {}

    public function show(Request $request, Mission $mission): Response
    {
        $this->authorizePayment($request, $mission);

        return Inertia::render('Payments/SelectMethod', ['mission' => $mission->only('id', 'title', 'price', 'status'), 'payments' => $mission->payments()->latest()->get()]);
    }

    public function store(Request $request, Mission $mission): RedirectResponse
    {
        $this->authorizePayment($request, $mission);
        $data = $request->validate([
            'method' => ['required', 'in:orange_money,moov_money,carte,paypal'],
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'images.required' => __('missions.images.required'),
            'images.array' => __('missions.images.required'),
            'images.min' => __('missions.images.count'),
            'images.max' => __('missions.images.count'),
            'images.*.required' => __('missions.images.required'),
            'images.*.image' => __('missions.images.invalid'),
            'images.*.mimes' => __('missions.images.format'),
            'images.*.max' => __('missions.images.size'),
        ]);
        if ($data['method'] !== 'paypal' && ((int) $mission->price % 5 !== 0 || (float) $mission->price !== (float) (int) $mission->price)) {
            return back()->with('error', __('messages.payment_multiple_of_five_cinetpay'));
        }
        if ($mission->payments()->where('status', 'effectue')->exists()) {
            return back()->with('error', __('messages.mission_already_paid'));
        }
        $this->missionImages->replace($mission, $data['images']);

        $pendingPayment = $mission->payments()
            ->where('payer_id', $request->user()->id)
            ->where('method', $data['method'])
            ->where('status', 'en_attente')
            ->whereNotNull('payment_url')
            ->latest()
            ->first();
        if ($pendingPayment) {
            return redirect()->away($pendingPayment->payment_url);
        }
        $provider = $data['method'] === 'paypal' ? 'paypal' : 'cinetpay';
        $payment = Payment::create(['mission_id' => $mission->id, 'payer_id' => $request->user()->id, 'receiver_id' => $mission->prestataire_id,
            'amount' => $mission->price, 'status' => 'en_attente', 'method' => $data['method'], 'provider' => $provider, 'transaction_id' => Str::upper(Str::random(20))]);
        try {
            return redirect()->away($this->gateway->initialize($payment));
        } catch (Throwable $e) {
            report($e);
            $payment->update(['status' => 'echoue']);

            return back()->with('error', $e->getMessage());
        }
    }

    public function returned(Request $request, Payment $payment): Response
    {
        abort_unless($payment->payer_id === $request->user()->id, 403);

        return Inertia::render('Payments/Confirm', ['payment' => $payment->only('id', 'status', 'amount', 'method')]);
    }

    public function capture(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->payer_id === $request->user()->id && $payment->provider === 'paypal', 403);

        if ($payment->status === 'en_attente' && $this->gateway->capturePayPal($payment)) {
            $payment->markAsPaid();
        }

        return redirect()->route('payments.return', $payment);
    }

    public function cinetPay(Request $request)
    {
        if ($request->isMethod('get')) {
            return response()->noContent();
        }

        $payment = Payment::where('transaction_id', $request->input('cpm_trans_id'))->firstOrFail();
        if ($payment->status === 'en_attente' && $this->gateway->verifyCinetPay($payment)) {
            $payment->markAsPaid();
        }

        return response()->noContent();
    }

    private function authorizePayment(Request $request, Mission $mission): void
    {
        abort_unless(
            $mission->client_id === $request->user()->id
            && $mission->status === 'in_progress'
            && $mission->prestataire_id
            && $mission->price > 0,
            403
        );
    }
}
