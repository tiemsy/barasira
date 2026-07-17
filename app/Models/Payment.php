<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id', // mission concernée
        'payer_id', // utilisateur qui effectue le paiement
        'receiver_id', // utilisateur qui reçoit le paiement
        'amount', // montant payé
        'status', // pending, completed, failed, refunded
        'method', // méthode de paiement (card, mobile_money, cash)
        'provider',
        'transaction_id',
        'payment_url',
        'provider_data',
        'paid_at',
    ];

    protected $casts = ['amount' => 'decimal:2', 'provider_data' => 'array', 'paid_at' => 'datetime'];

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
