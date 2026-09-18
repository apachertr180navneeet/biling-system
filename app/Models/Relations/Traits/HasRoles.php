<?php

namespace App\Models\Relations\Traits;

use App\Models\Permission;
use App\Models\Role;

trait HasRoles
{
    public function roleDetail()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasRole($slug)
    {
        $role = $this->roleDetail;
        if (!$role) {
            return false;
        }
        if (is_array($slug)) {
            return in_array($role->slug, $slug);
        }
        return $role->slug === $slug;
    }

    public function hasPermission($permissionSlug)
    {
        $role = $this->roleDetail;
        if (!$role) {
            return false;
        }
        return $role->permissions()->where('slug', $permissionSlug)->exists();
    }

    public function hasAnyPermission(array $permissionSlugs)
    {
        $role = $this->roleDetail;
        if (!$role) {
            return false;
        }
        return $role->permissions()->whereIn('slug', $permissionSlugs)->exists();
    }

    public function hasAllPermissions(array $permissionSlugs)
    {
        $role = $this->roleDetail;
        if (!$role) {
            return false;
        }
        $count = $role->permissions()->whereIn('slug', $permissionSlugs)->count();
        return $count === count($permissionSlugs);
    }

    public function getAllPermissions()
    {
        $role = $this->roleDetail;
        if (!$role) {
            return collect();
        }
        return $role->permissions;
    }

    public function getModulePermissions($module)
    {
        $role = $this->roleDetail;
        if (!$role) {
            return collect();
        }
        return $role->permissions()->where('module', $module)->get();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }
}
