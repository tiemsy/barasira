<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id', // mission concernée
        'reviewer_id', // utilisateur qui rédige l'avis
        'reviewed_id', // utilisateur qui reçoit l'avis
        'rating', // note : 1 à 5
        'comment', // commentaire
        'edit_count',
        'revised_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'edit_count' => 'integer',
        'revised_at' => 'datetime',
    ];

    /**
     * Mission associée à cet avis
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Utilisateur qui rédige l'avis
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Utilisateur qui reçoit l'avis
     */
    public function reviewed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }
}
