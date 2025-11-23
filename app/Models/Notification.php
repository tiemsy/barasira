<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // utilisateur destinataire
        'type', // type de notification : system / mission / message / etc.
        'title', // titre de la notification
        'message', // contenu
        'read' // booléen : true si lue, false sinon
    ];

    protected $casts = ['read' => 'boolean'];

    /**
     * L'utilisateur destinataire de la notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
