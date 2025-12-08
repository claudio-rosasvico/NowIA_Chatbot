<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class OrganizationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 1. Si NO es super admin, siempre filtrar por su org actual
        if (Auth::check() && !Auth::user()->is_super_admin && Auth::user()->current_organization_id) {
            $builder->where($model->getTable() . '.organization_id', Auth::user()->current_organization_id);
            return;
        }

        // 2. Si ES super admin, verificar si está "impersonando" una org
        if (Auth::check() && Auth::user()->is_super_admin) {
            $impersonatedOrgId = session('admin_impersonated_org_id');
            if ($impersonatedOrgId) {
                // Aplicar filtro como si fuera esa org
                $builder->where($model->getTable() . '.organization_id', $impersonatedOrgId);
            }
            // Si no hay impersonation, NO aplicar filtro (ver todo)
        }
    }
}
