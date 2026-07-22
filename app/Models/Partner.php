<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name', 'description', 'logo_path', 'website_url', 'company_email',
        'company_phone', 'address', 'contact_name', 'contact_position', 'contact_email',
        'contact_phone', 'is_published', 'display_order', 'created_by', 'updated_by',
    ];

    protected $casts = ['is_published' => 'boolean', 'display_order' => 'integer'];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('display_order')->orderBy('company_name');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(PartnerPromotion::class);
    }
}
