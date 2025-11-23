<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id', // identifiant du CV
        'tag', // nom du tag ou compétence
    ];

    /**
     * Le CV auquel le tag appartient
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
