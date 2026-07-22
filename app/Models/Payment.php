<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    public const PLATFORM_FEE_RATE = 0.10;

    protected $fillable = [
        'mission_id', // mission concernée
        'payer_id', // utilisateur qui effectue le paiement
        'receiver_id', // utilisateur qui reçoit le paiement
        'amount', // montant payé
        'platform_fee', // commission Barasira (10 %)
        'provider_amount', // montant attribué au prestataire (90 %)
        'status', // pending, completed, failed, refunded
        'method', // méthode de paiement (card, mobile_money, cash)
        'provider',
        'transaction_id',
        'payment_url',
        'provider_data',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'provider_amount' => 'decimal:2',
        'provider_data' => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Payment $payment): void {
            if ($payment->isDirty('amount') || $payment->platform_fee === null || $payment->provider_amount === null) {
                $amount = round((float) $payment->amount, 2);
                $fee = round($amount * self::PLATFORM_FEE_RATE, 2);
                $payment->platform_fee = $fee;
                $payment->provider_amount = round($amount - $fee, 2);
            }
        });
    }

    /**
     * La mission associée au paiement
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Utilisateur qui paie
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    /**
     * Utilisateur qui reçoit le paiement
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Confirme atomiquement le paiement et clôt la mission validée par le client.
     */
    public function markAsPaid(): bool
    {
        return DB::transaction(function (): bool {
            $payment = self::query()->lockForUpdate()->findOrFail($this->getKey());

            if ($payment->status === 'effectue') {
                return true;
            }

            if ($payment->status !== 'en_attente') {
                return false;
            }

            $mission = $payment->mission()->lockForUpdate()->first();
            if (! $mission || ! in_array($mission->status, ['in_progress', 'completed'], true)) {
                return false;
            }

            $payment->update(['status' => 'effectue', 'paid_at' => now()]);

            if ($mission->status === 'in_progress') {
                $mission->update(['status' => 'completed']);
            }

            $this->setRawAttributes($payment->fresh()->getAttributes(), true);

            return true;
        });
    }
}
