<?php

namespace App\Services;

use App\Models\RoleManagement\Permission;
use App\Models\RoleManagement\Role;
use Illuminate\Http\Request;

class RoleService
{

    public function getRoleObject()
    {
        return new Role();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getRoleData()
    {
        return $this->getRoleObject()->query();
    }
    /**
     * storeCountry function
     *
     * @param Request $request
     * @return object
     */
    public function storeRole(Request $request)
    {
        $role = $this->getRoleObject()->create($request->validated());
        $permissions = $request->get('permissions');
        if (!empty($permissions)) {
            foreach ($permissions as $value) {
                $permission = new Permission();
                $permission->id = $value;
                $data[] = $permission;
            }
            $role->syncPermissions($data);
        }
        return $role;
    }
    /**
     * getProductAttribute function
     *
     * @param string $uuid
     * @return object
     */
    public function getRole(string $uuid)
    {
        return $this->getRoleObject()->where('uuid', $uuid)->first();
    }
    /**
     * updateCountry function
     *
     * @param Request $request
     * @return void
     */
    public function updateRole(Request $request, $uuid)
    {
        $role = $this->getRole($uuid);

        $role->update($request->validated());
        $permissions = $request->get('permissions');

        if (!empty($permissions)) {
            foreach ($permissions as $value) {
                $permission = new Permission();
                $permission->id = $value;
                $data[] = $permission;
            }
            $role->syncPermissions($data);
        }
        return $role;
    }
    /**
     * deleteModule function
     *
     * @param string $uuid
     * @return boolean
     */
    public function deleteRole(string $uuid)
    {
        $role = $this->getRole($uuid);
        $role->delete();
        return true;
    }
    public function getActiveModules()
    {
        return (new ModuleService())->getModuleData()->with('permissions')->whereHas('permissions', function ($q) {
            $q->where('status', '=', config('params.active'));
            $q->orderBy('module_id');
        })->get();

    }
}