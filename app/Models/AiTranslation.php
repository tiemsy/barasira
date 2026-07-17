<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiTranslation extends Model
{
    protected $fillable = [
        'translatable_type','translatable_id','field','source_locale','target_locale',
        'source_hash','original_text','translated_text','provider',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
