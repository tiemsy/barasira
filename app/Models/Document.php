<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'label',
        'file_url',
        'original_name',
        'mime_type',
        'file_size',
        'status',
        'review_comment',
        'reviewed_by',
        'reviewed_at',
        'uploaded_at',
    ];

    protected $casts = ['uploaded_at' => 'datetime', 'reviewed_at' => 'datetime', 'file_size' => 'integer'];

    /**
     * Le document appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
