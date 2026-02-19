<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToOwner
{
    protected static function bootBelongsToOwner()
    {
        // 1. Automatically assign the logged-in owner's ID when creating a record
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->owner_id = auth()->id();
            }
        });

        // 2. Automatically filter all queries so owners ONLY see their own data
        if (auth()->check()) {
            static::addGlobalScope('owner', function (Builder $builder) {
                $builder->where('owner_id', auth()->id());
            });
        }
    }
}