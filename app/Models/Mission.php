<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mission extends Model
{
    use Filterable, HasFactory, HasUniqueSlug;

    protected $filtersClass = \App\Filters\MissionFilters::class;

    protected $fillable = [
        'client_id', // utilisateur client qui poste la mission
        'prestataire_id', // Prestataire lié à la mission
        'service_id', // service demandé
        'title', // Titre de la mission
        'description', // Détails de la mission
        'city', // Ville
        'skills', // Détails de la mission
        'questions', // Détails de la mission
        'address', // localisation
        'latitude', // localisation
        'longitude', // localisation
        'status', // workflow : pending, in_progress, completed, cancelled
        'price', // prix proposé
        'initial_hours', // durée minimale définie par le client
        'billable_hours', // durée finale validée avant paiement
        'date_start', // période de la mission
        'date_end', // période de la mission
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'initial_hours' => 'decimal:2',
        'billable_hours' => 'decimal:2',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'skills' => 'array',
        'questions' => 'array',
        'date_start' => 'datetime',
        'date_end' => 'datetime',
    ];

    protected function slugSource(): string
    {
        return $this->title;
    }

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

    public function acceptedApplication(): ?Application
    {
        return $this->applications()->where('status', 'acceptee')->first();
    }

    public function payableAmount(): float
    {
        $application = $this->acceptedApplication();

        if (! $application) {
            return (float) $this->price;
        }

        return $application->pricing_type === 'hourly'
            ? round((float) $application->hourly_rate * (float) $this->billable_hours, 2)
            : (float) $application->proposed_price;
    }

    /**
     * Paiements liés à cette mission
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MissionImage::class)->orderBy('sort_order');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(MissionInvitation::class);
    }

    public function unassignments(): HasMany
    {
        return $this->hasMany(MissionUnassignment::class);
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
