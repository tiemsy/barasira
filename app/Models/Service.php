<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany};

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',    // catégorie du service (ex : plomberie, électricité…)
        'name',           // nom du service
        'description',    // description générale
        'icon',           // icône ou pictogramme (optionnel)
        'price_min',      // prix minimum suggéré
        'price_max',      // prix maximum suggéré
        'is_active',      // service visible ou désactivé
    ];

    /**
     * Catégorie du service
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class);
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
