<?php

namespace App\Models;

use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'verified',
        'email_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verified' => 'boolean',
        'rating' => 'decimal:2',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailCustom);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin'], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    // public function missions(): HasMany
    // {
    //     return $this->hasMany(Mission::class, 'client_id');
    // }

    public function missionsAsPrestataire()
    {
        return $this->hasMany(Mission::class, 'prestataire_id');
    }

    public function missionsAsClient()
    {
        return $this->hasMany(Mission::class, 'client_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'user_skills');
        // ->withPivot(['certificate', 'years_experience'])
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
        return $this->hasMany(Dispute::class, 'complainant_id');
    }
}
