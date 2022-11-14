<?php

namespace App\Services;

use App\Models\RoleManagement\Permission;
use App\Models\RoleManagement\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionService
{

    public function getPermissionObject()
    {
        return new Permission();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getPermissionData()
    {
        return $this->getPermissionObject()->with('module');
    }
    /**
     * storeCountry function
     *
     * @param Request $request
     * @return object
     */
    public function storePermission(Request $request)
    {
        $permission = Permission::create($request->validated());
        $permission->syncRoles(config('params.admin_roles'));
        return $permission;
    }
    /**
     * getProductAttribute function
     *
     * @param string $uuid
     * @return object
     */
    public function getPermission(string $uuid)
    {
        return $this->getPermissionObject()->where('uuid', $uuid)->first();
    }
    /**
     * updateCountry function
     *
     * @param Request $request
     * @return void
     */
    public function updatePermission(Request $request, $uuid)
    {
        $permission = $this->getPermission($uuid);
        $permission->update($request->validated());
        //remove permission for login user role
        if ($request->status == config('params.in_active')) {
            $this->removePermissionForRoles($permission);
        }
        //add permission for login user role
        if ($request->status == config('params.active')) {
            $this->addPermissionForRoles($permission);
        }

    }
    /**
     * deletePermission function
     *
     * @param string $uuid
     * @return boolean
     */
    public function deletePermission(string $uuid)
    {
        $permission = $this->getPermission($uuid);
        $permission->delete();
        return true;
    }
    /**
     * getRoleIdsByPermission function
     *
     * @param integer $permission_id
     * @return array
     */
    public function getRoleIdsByPermission(int $permission_id)
    {
        return DB::table('role_has_permissions')->where('permission_id', $permission_id)->pluck('role_id');
    }
    public function removePermissionForRoles($permission)
    {
        $role_ids = $this->getRoleIdsByPermission($permission->id);
        $roles = Role::whereIn('id', $role_ids)->get();
        foreach ($roles as $role) {
            $role->revokePermissionTo($permission);
            $permission->removeRole($role);
        }
    }
    public function addPermissionForRoles($permission)
    {
        $roles = Role::where('status', config('params.active'))->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
            $permission->assignRole($role);
        }
    }
}