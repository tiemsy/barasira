<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeLanguage extends Model
{
    use HasFactory;

    protected $table = 'resume_languages';

    protected $fillable = [
        'resume_id', // lien vers le CV
        'language', // nom de la langue : Français, Bambara, Anglais, etc. (texte libre ou future table séparée)
        'level', // niveau : débutant / intermédiaire / avancé / natif / A1–C2
    ];

    /**
     * Le CV associé
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
