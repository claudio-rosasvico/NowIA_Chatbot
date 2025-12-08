<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization()
    {
        static::addGlobalScope(new \App\Models\Scopes\OrganizationScope);

        // Asignar org automáticamente al crear si hay usuario autenticado
        static::creating(function (Model $model) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();

                // Definir Org ID a usar:
                // 1. Si es Super Admin y tiene impersonation, usar ese.
                // 2. Si no, usar su current_organization_id.
                $orgIdToAssign = $user->current_organization_id;

                if ($user->is_super_admin && session('admin_impersonated_org_id')) {
                    $orgIdToAssign = session('admin_impersonated_org_id');
                }

                if ($orgIdToAssign && !$model->organization_id) {
                    $model->organization_id = $orgIdToAssign;
                }
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }
}