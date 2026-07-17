<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_category_id', // catégorie du service (ex : plomberie, électricité…)
        'city_id',
        'municipality_id',
        'name',           // nom du service
        'description',    // description générale
        'icon',           // icône ou pictogramme (optionnel)
        'price_min',      // prix minimum suggéré
        'price_max',      // prix maximum suggéré
        'is_active',      // service visible ou désactivé
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price_min' => 'decimal:2',
        'price_max' => 'decimal:2',
    ];

    public function scopeActiveForProvider(Builder $query, User|int $provider): Builder
    {
        $providerId = $provider instanceof User ? $provider->id : $provider;

        return $query->where('user_id', $providerId)->where('is_active', true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    /**
     * Catégorie du service
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    /**
     * Missions liées à ce service
     */
    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    /**
     * Prestataires proposant ce service (relation pivot)
     */
    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_service')
            ->withTimestamps();
    }

    /**
     * Documents liés à ce service (plans, fiches, guides…)
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Compétences utilisateur spécifiques à ce service (pivot user_skills)
     *
     * Exemple : niveau, années d'expérience, certificats…
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_skills');
    }

    /**
     * Éléments de portfolio associés à ce service (si un user associe un travail à ce service)
     */
    public function portfolioItems()
    {
        return $this->hasMany(PortfolioItem::class);
    }

    /**
     * Compétences utilisateur spécifiques à ce service (pivot user_skills)
     *
     * Exemple : niveau, années d'expérience, certificats…
     */
    public function userSkills()
    {
        return $this->hasMany(UserSkill::class);
    }
}
