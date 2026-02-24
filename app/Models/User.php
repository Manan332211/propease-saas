<?php

namespace App\Models;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Only users with the 'admin' role can access the admin panel
return in_array($this->role, ['admin', 'landlord']);    }

    // A Landlord has many properties
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    // A Landlord has many tenants
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'owner_id');
    }
}