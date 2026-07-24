<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientComment extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'commenter_id', 'comment'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function commenter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commenter_id');
    }
}
