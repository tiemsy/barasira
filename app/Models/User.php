<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany};

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'bio',
        'avatar_url',
        'rating',
        'verified'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'verified' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class, 'client_id');
    }


    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }


    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'user_skills')
            ->withPivot(['price', 'experience_years']);
    }


    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }


    public function resume()
    {
        return $this->hasOne(Resume::class);
    }


    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }


    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'user_id');
    }
}
