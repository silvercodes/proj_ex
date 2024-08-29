<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;


/**
 * Trait HasPermissionsTrait
 * @package App\Traits
 */
trait HasPermissionsTrait
{
    /**
     * Associated roles
     *
     * @return mixed
     */
    public function roles() {

        return $this->belongsToMany(Role::class,'users_roles');
    }

    /**
     * Associated permissions
     *
     * @return mixed
     */
    public function permissions() {

        return $this->belongsToMany(Permission::class,'users_permissions');
    }


    /**
     * Get permissions by permissions slugs
     *
     * @param array $permissions
     * @return mixed
     */
    protected function getPermissionsBySlug(array $permissions)
    {
        return Permission::whereIn('slug', $permissions)->get();
    }

    /**
     * Check if permission exists
     *
     * @param $permission
     * @return bool
     */
    protected function hasPermission($permission) {

        return (bool) $this->permissions->where('slug', $permission->slug)->count();
    }


    /**
     * Check if role exists
     *
     * @param mixed ...$roles
     * @return bool
     */
    public function hasRole(...$roles ) {

        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set permissions
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function setPermissions(...$permissions)
    {
        $findedPermissions = $this->getPermissionsBySlug($permissions);

        if ($findedPermissions === null)
            return $this;

        $this->permissions()->saveMany($permissions);

        return $this;
    }

    /**
     * Withdraw permissions from user
     *
     * @param mixed ...$permissions
     * @return $this
     */
    public function withdrawPermissions(...$permissions)
    {
        $findedPermissions = $this->getPermissionsBySlug($permissions);

        $this->permissions()->detach($findedPermissions);

        return $this;
    }

    /**
     * Refresh user permissions
     *
     * @param mixed ...$permissions
     * @return HasPermissionsTrait
     */
    public function refreshPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->setPermissions($permissions);
    }

    /**
     * Check if own roles contains permission
     *
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission)
    {
        foreach($permission->roles as $role) {
            if($this->roles->contains($role))
                return true;
        }

        return false;
    }

    /**
     * Check permission completely
     *
     * @param $permission
     * @return bool
     */
    public function hasPermissionComplete($permission)
    {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }
}


