<?php

namespace App\Models;

use App\Models\Traits\HasUniqueSlug;
use App\Notifications\VerifyEmailCustom;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasLocalePreference, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasUniqueSlug, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'locale',
        'password',
        'phone',
        'role',
        'bio',
        'avatar_url',
        'rating',
        'hourly_rate',
        'verified',
        'email_verified_at',
        'identity_verified_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'identity_verified_at' => 'datetime',
        'verified' => 'boolean',
        'rating' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
    ];

    protected function slugSource(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

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

    public function routeNotificationForWhatsApp(): ?string
    {
        return $this->phone;
    }

    public function preferredLocale(): string
    {
        return in_array($this->locale, ['fr', 'en', 'bm'], true) ? $this->locale : 'fr';
    }

    public function syncIdentityVerification(): void
    {
        $verifiedAt = $this->documents()
            ->where('document_type', 'identity')
            ->where('status', 'valide')
            ->max('reviewed_at');

        $this->forceFill(['identity_verified_at' => $verifiedAt])->save();
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

    public function providerServices(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_id');
    }

    public function missionInvitations(): HasMany
    {
        return $this->hasMany(MissionInvitation::class, 'provider_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'worker_id');
    }

    public function clientComments(): HasMany
    {
        return $this->hasMany(ClientComment::class, 'client_id');
    }

    public function commentsAboutClients(): HasMany
    {
        return $this->hasMany(ClientComment::class, 'commenter_id');
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
