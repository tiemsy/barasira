<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

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
        'transaction_id' // éférence externe de transaction (optionnel)
    ];

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
}
