<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

/**
 * Resume + CVthèque
 */
class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // utilisateur propriétaire
        'title', // titre du CV (ex : « CV Développeur Fullstack »)
        'summary', // résumé ou profil professionnel
        'visibility' //visibilité du CV (public / private)
    ];

    /**
     * L'utilisateur propriétaire du CV
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Expériences professionnelles liées au CV
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    /**
     * Formations liées au CV
     */
    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    /**
     * Certifications liées au CV
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    /**
     * Langues parlées ajoutées au CV
     */
    public function languages(): HasMany
    {
        return $this->hasMany(ResumeLanguage::class);
    }

    /**
     * Documents liés au CV
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Portfolio items liés au CV (optionnel)
     */
    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }
}
