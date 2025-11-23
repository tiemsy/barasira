<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',            // utilisateur auquel appartient la compétence
        'service_id',         // service associé à cette compétence
        'level',              // niveau de compétence (ex. : débutant, confirmé, expert)
        'years_experience',   // années d'expérience réelle
        'certificate',        // nom du certificat éventuel
        'certificate_file',   // chemin du fichier justificatif (PDF, image…)
        'description',        // description libre de la compétence
        'verified',           // booléen si la compétence a été validée par l'admin
    ];

    /**
     * L'utilisateur qui possède la compétence
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Le service auquel cette compétence est liée
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
