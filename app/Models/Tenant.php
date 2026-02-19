<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOwner;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use BelongsToOwner;

    protected $guarded = [];

    // The login account for the tenant (for the future React portal)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); 
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }
}