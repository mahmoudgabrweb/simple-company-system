<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Support\CompanyContext;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        // Super admin sees selected context if set; if none, show all
        if ($user && $user->hasRole('super_admin')) {
            if ($cid = CompanyContext::id()) {
                $builder->where($model->getTable() . '.company_id', $cid);
            }
            return;
        }

        // Regular users → must be scoped
        if ($cid = CompanyContext::id()) {
            $builder->where($model->getTable() . '.company_id', $cid);
        }
    }
}
