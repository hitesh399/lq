<?php

namespace Singsys\LQ\Lib;

use Cache;
use Singsys\LQ\Models\Permission;
use Singsys\LQ\Models\Role;
use Illuminate\Routing\Route;

class PermissionRepository
{
    protected $permissions = null;
    protected $currentRoute = null;
    protected $currentPermission = null;
    protected $currentRolePermissions = null;
    protected $roleCurrentPermission = null;

    public function __construct()
    {
        $this->currentRoute = app(Route::class);
    }
    /**
     * To get the permissions;
     */
    public function all()
    {

        /**
         * If we have already permission data into local variable then no need to intract with cache and database.
         */
        if ($this->permissions) {
            return $this->permissions;
        }
        /**
         * Get the all permission in Array format
         */
        $this->permissions = Cache::rememberForever('permission_repository', function () {
            return Permission::get(['name', 'limitations','is_public','encrypted'])->keyBy('name')->toArray();
        });

        return $this->permissions;
    }

    /**
     * To get the permission of given role
     * @param $role_id Integer
     */
    public function rolePermissions($role_id)
    {
        if ($this->currentRolePermissions) {
            return $this->currentRolePermissions;
        }

        #Get the all permission of given order in Array format

        $this->currentRolePermissions = Cache::rememberForever('permission_role_repository_'.$role_id, function () use ($role_id) {
            $role =  Role::with('rolePermissions.rolePermissionFields.permissionFields')->find($role_id);
            $role_permissions = $role->rolePermissions->map(function ($role_permission) {
                return collect([
                    'name' => $role_permission->permission->name,
                    'limitations'=> $role_permission->limitations ? json_decode($role_permission->limitations, true) : $role_permission->limitations,
                    'fields' => $role_permission->rolePermissionFields->map(function ($rolePermissionField) {
                        return \collect([
                            'authority' => $rolePermissionField->authority,
                            'client_field' => $rolePermissionField->permissionFields->client_field,
                            'table_columns' => $rolePermissionField->permissionFields->table_columns,
                        ]);
                    })
                ]);
            })->keyBy('name');

            return $role_permissions->toArray();
        });

        return $this->currentRolePermissions;
    }

    /**
     * Get the current permission information
     */
    public function current()
    {
        if ($this->currentPermission) {
            return $this->currentPermission;
        }

        #Get the current permission from the permissions list.

        $permissions = $this->all();

        $name = $this->currentRoute->getName();
        return $name ? isset($permissions[$name]) ? $permissions[$name]: false : null;
    }

    /**
     * find the permission form given name
     * @param {String} $name (Permission name)
     */
    public function find($name)
    {
        $permissions = $this->all();
        return $name ? isset($permissions[$name]) ? $permissions[$name]: false : null;
    }

    /**
     * To check that  can user access current Route ?
     */
    public function canAccess($role_id)
    {
        return $this->roleCurrentPermission($role_id) ? true : false;
    }

    /**
     * Get role current Permission
     */
    public function roleCurrentPermission($role_id)
    {
        if ($this->roleCurrentPermission) {
            return $this->roleCurrentPermission;
        }
        $name = $this->currentRoute->getName();

        $role_permissions =  $this->rolePermissions($role_id);

        $permission =  isset($role_permissions[$name]) ? $role_permissions[$name]: null;
        $this->roleCurrentPermission = $permission;

        return $this->roleCurrentPermission;
    }

    /**
     * To get the role current permission tag
     */
    public function roleCurrentPermissionTag($role_id)
    {
        $current_permission = $this->roleCurrentPermission($role_id);
        return isset($current_permission['limitations']['tags']) && $current_permission['limitations']['tags'] ? $current_permission['limitations']['tags'] : null;
    }

    /**
     * Check user does have the Security Tag
     */
    public function hasTag($tag)
    {
        if (!\Auth::user()) {
            return false;
        }
        $role_id = \Auth::user()->role_id;
        $security_tag = $this->roleCurrentPermissionTag($role_id);
        return ($tag == $security_tag);
    }
}
