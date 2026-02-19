<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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