<?php

namespace App\Models\Scopes;

use App\Enums\ResourceOwnership;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HideEmergencyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('type', '!=', ResourceOwnership::EMERGENCY);
    }
}
