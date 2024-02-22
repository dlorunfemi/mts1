<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Tenancy
{

    public static function boot()
    {
        parent::boot;

        $getTenantId = auth()->user()->tenant_id; // TODO: switch to global helper function


        static::creating(function($model) use($getTenantId) {
            $model->tenant_id = $getTenantId;
        });

        //static::addGlobalScope(new TenantScope);
        static::addGlobalScope(function(Builder $builder) use($getTenantId) {
            $builder->where('tenant_id', $getTenantId);
        });
    }

    // TODO: Make this a global function and reuse
    public function getTenantId(): string
    {
        return auth()->user()->tenant_id;
    }
}

