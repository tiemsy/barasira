<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'mission_id', // Référence de la mission cible
        'worker_id', // Prestataire qui postule
        'message', // Message ou explication du prestataire
        'proposed_price', // Prix proposé par le prestataire
        'status' // État de la candidature : pending, accepted, rejected, cancelled
    ];

    /**
     * Une candidature appartient à un utilisateur (prestataire)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Une candidature appartient à une mission
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
