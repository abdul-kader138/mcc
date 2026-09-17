<?php

namespace App\Models;

use App\Notifications\Auth\ResetPassword;
use App\Notifications\Auth\VerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['first_name', 'last_name', 'email', 'phone', 'marketing_opt_in', 'password', 'avatar', 'google_id', 'email_verified_at', 'locale'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasName, MustVerifyEmail
{
    public const SUPPORTED_LOCALES = ['en', 'fr'];

    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    use \Illuminate\Auth\MustVerifyEmail;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty()->dontLogEmptyChanges()->useLogName('user');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'marketing_opt_in' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    public function getFilamentName(): string { return $this->name; }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'panel_user']) || $this->getAllPermissions()->isNotEmpty();
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(['super_admin', 'panel_user']) || $this->getAllPermissions()->isNotEmpty();
    }

    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return filled($this->two_factor_secret) && filled($this->two_factor_confirmed_at);
    }

    public function isAdmin(): bool { return $this->hasRole('super_admin'); }

    public function sendEmailVerificationNotification(): void { $this->notify(new VerifyEmail); }
    public function sendPasswordResetNotification($token): void { $this->notify(new ResetPassword($token)); }

    public function items(): HasMany { return $this->hasMany(Item::class); }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? Storage::disk('public')->url($this->avatar) : null;
    }
}
