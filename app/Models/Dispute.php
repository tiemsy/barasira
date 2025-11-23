<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id', // mission liée au litige
        'complainant_id', // Auteur de la plainte
        'defendant_id', // Partie accusée (client ou prestataire)
        'reason', // Motif du litige(paiement, retard, malfaçon, etc.)
        'status', // workflow : open, in_review, resolved, rejected
        'resolution' // Note ou conclusion apportée par l’équipe support
    ];

    /**
     * La dispute concerne une mission.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * L'utilisateur qui a déposé la plainte.
     */
    public function complainant() : BelongsTo
    {
        return $this->belongsTo(User::class, 'complainant_id');
    }

    /**
     * L'utilisateur mis en cause dans la dispute.
     */
    public function defendant() : BelongsTo
    {
        return $this->belongsTo(User::class, 'defendant_id');
    }
}
