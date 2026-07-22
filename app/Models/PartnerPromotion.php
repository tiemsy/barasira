<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerPromotion extends Model
{
    protected $fillable = ['partner_id', 'paid_amount', 'starts_at', 'ends_at', 'created_by'];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function scopeActive($query, $date = null)
    {
        $date ??= now();

        return $query->where('starts_at', '<=', $date)->where('ends_at', '>=', $date);
    }
}
