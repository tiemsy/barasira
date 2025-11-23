<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id', // lien vers le CV
        'name', // nom de la certification (ex : « Certificat en bureautique »)
        'issuer', // organisme (ex : Google, Orange Digital Center)
        'issue_date', // date d’obtention
        'expiration_date', // optionnel
        'credential_url' // lien de vérification
    ];

    /**
     * Une certification appartient à un CV
     */
    public function resume() : BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
