<?php

namespace App\Services;

use App\Models\RoleManagement\Module;
use App\Models\RoleManagement\Permission;
use Illuminate\Http\Request;

class ModuleService
{

    public function getModuleObject()
    {
        return new Module();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getModuleData()
    {
        return $this->getModuleObject()->query();
    }
    /**
     * storeCountry function
     *
     * @param Request $request
     * @return object
     */
    public function storeModule(Request $request)
    {
        $module = $this->getModuleObject()->create($request->validated());
        $defaultPermissions = config('params.module_default_permissions');
        if ($request->has('default_permission')) {
            $data = [];
            foreach ($defaultPermissions as $value) {
                $name = str_replace(" ", "-", trim($request->get('name')));
                $permission = new Permission();
                $permission->name = strtolower($value . '-' . $name);
                $permission->status = config('params.active');
                $data[] = $permission;
            }
            $module->permissions()->saveMany($data);
        }
        return $module;
    }
    /**
     * getProductAttribute function
     *
     * @param string $uuid
     * @return object
     */
    public function getModule(string $uuid)
    {
        return $this->getModuleObject()->where('uuid', $uuid)->first();
    }
    /**
     * updateCountry function
     *
     * @param Request $request
     * @return void
     */
    public function updateModule(Request $request, $module)
    {
        return $module->update($request->validated());
    }
    /**
     * deleteModule function
     *
     * @param App\Models\RoleManagement\Module $module
     * @return boolean
     */
    public function deleteModule($module)
    {
        $module->delete();
        return true;
    }
}