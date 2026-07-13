<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use App\Models\Traits\Filterable;

class Mission extends Model
{
    use HasFactory, Filterable;

    protected $filtersClass = \App\Filters\MissionFilters::class;

    protected $fillable = [
        'client_id', // utilisateur client qui poste la mission
        'prestataire_id', // Prestataire lié à la mission
        'service_id', // service demandé
        'title', // Titre de la mission
        'description', // Détails de la mission
        'address', // localisation
        'latitude', // localisation
        'longitude', // localisation
        'status', // workflow : pending, in_progress, completed, cancelled
        'price', // prix proposé
        'date_start', // période de la mission
        'date_end' // période de la mission
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    /**
     * Client qui a publié la mission
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }


    /**
     * Prestataire lié à la mission
     */
    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prestataire_id');
    }

    /**
     * Service associé à la mission
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Applications reçues pour cette mission
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Paiements liés à cette mission
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Avis / reviews de la mission
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Messages échangés concernant cette mission
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Documents liés à cette mission
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Litiges associés à cette mission
     */
    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }
}
