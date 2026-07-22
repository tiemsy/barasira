<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionUnassignment extends Model
{
    protected $fillable = ['mission_id', 'client_id', 'provider_id', 'reason', 'details'];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
