<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id', // utilisateur qui envoie le message
        'receiver_id', // utilisateur qui reçoit le message
        'mission_id',  // optionnel : lien vers la mission
        'message', // contenu du message
        'read', // booléen indiquant si le message a été lu (true ou false)
    ];

    protected $casts = ['read' => 'boolean'];

    /**
     * L'utilisateur qui envoie le message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * L'utilisateur qui reçoit le message
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
