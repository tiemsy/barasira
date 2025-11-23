<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id', // référence du CV associé
        'company_name', // nom de l’entreprise
        'position', // Poste occupé
        'start_date', // période d’emploi
        'end_date', // période d’emploi
        'description' // détails sur le rôle et les missions
    ];

    /**
     * Une expérience professionnelle appartient à un CV
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
