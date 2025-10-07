<?php

namespace App\Models\Concerns;

use App\Models\Scopes\CompanyScope;
use App\Support\CompanyContext;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (empty($model->company_id) && ($cid = CompanyContext::id())) {
                $model->company_id = $cid;
            }
        });
    }
}
