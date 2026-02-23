<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $table = $model->getTable();
            
            // For models with 'is_system' column (like VoucherTemplate), 
            // allow seeing system-wide records or user-owned records.
            // We check the model directly or columns if needed. 
            // To be efficient, we check if the model class is VoucherTemplate.
            if ($model instanceof \App\Models\VoucherTemplate) {
                $builder->where(function ($query) use ($table) {
                    $query->where($table . '.user_id', auth()->id())
                          ->orWhere($table . '.is_system', true);
                });
            } else {
                $builder->where($table . '.user_id', auth()->id());
            }
        }
    }
}
