<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id', // lien vers le CV
        'school_name', // nom de l’établissement
        'degree', // type de diplôme (Licence, Master, BTS…)
        'field', // domaine d’étude
        'start_year', // durée de la formation
        'end_year', // durée de la formation
        'city', // géolocalisation
        'country' // géolocalisation
    ];

    /**
     * Relation : une formation appartient à un CV
     * Un CV peut avoir plusieurs formations
     */
    public function resume() : BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
