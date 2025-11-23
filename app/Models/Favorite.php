<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', //l’utilisateur qui ajoute un autre utilisateur en favori
        'favorite_user_id' // l’utilisateur favori
    ];

    /**
     * L'utilisateur qui a ajouté le favori
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * L'utilisateur qui est marqué comme favori
     */
    public function favorite(): BelongsTo
    {
        return $this->belongsTo(User::class, 'favorite_user_id');
    }
}
