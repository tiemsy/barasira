<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id', // utilisateur propriétaire
        'title', // titre du projet
        'description', // description du projet
        'file_url' // chemin vers l'image ou média
    ];

    /**
     * L'utilisateur propriétaire de l'élément de portfolio
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
