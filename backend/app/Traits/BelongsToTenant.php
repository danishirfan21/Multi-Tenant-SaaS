<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    /**
     * Boot the trait and add global scope for tenant isolation
     */
    protected static function bootBelongsToTenant(): void
    {
        // Automatically scope all queries to current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenantId = static::getCurrentTenantId()) {
                $builder->where(static::getQualifiedTenantColumn(), $tenantId);
            }
        });

        // Automatically set tenant_id when creating
        static::creating(function (Model $model) {
            if (!$model->tenant_id && $tenantId = static::getCurrentTenantId()) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    /**
     * Get current tenant ID from authenticated user
     */
    protected static function getCurrentTenantId(): ?int
    {
        $user = auth()->user();
        return $user?->tenant_id;
    }

    /**
     * Get the qualified tenant column name
     */
    protected static function getQualifiedTenantColumn(): string
    {
        return (new static())->getTable() . '.tenant_id';
    }
}
