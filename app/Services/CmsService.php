<?php

namespace App\Services;

use App\Models\Cms;
use Illuminate\Http\Request;

class CmsService
{
    /**
     * getCmsList function
     *
     * @param Request $request
     * @return object
     */
    public function getCmsList(Request $request)
    {
        return Cms::query();
    }
    /**
     * storeCms function
     *
     * @param Request $request
     * @return object
     */
    public function storeCms(Request $request)
    {
        $is_mobile_view = $request->is_mobile_view ?? 0;
        $cms = new Cms;
        $cms->fill($request->validated());
        $cms->is_mobile_view = $is_mobile_view;
        return $cms->save();
    }
    /**
     * getCms function
     *
     * @param string $uuid
     * @return object
     */
    public function getCms(string $uuid)
    {
        return Cms::where('uuid', $uuid)->firstOrFail();
    }
    /**
     * updateCms function
     *
     * @param Request $request
     * @return void
     */
    public function updateCms(Request $request)
    {
        $is_mobile_view = $request->is_mobile_view ?? 0;
        $data = $this->getCms($request->uuid);
        if (!empty($data)) {
            $data->fill($request->validated());
            $data->is_mobile_view = $is_mobile_view;
            $data->save();
        }
        return $data;
    }
    /**
     * deleteCms function
     *
     * @param string $uuid
     */
    public function deleteCms(string $uuid)
    {
        return Cms::where('uuid', $uuid)->delete();
    }
    /**
     * multipleDeleteCms function
     *
     * @param Request $request
     * @return void
     */
    public function multipleDeleteCms(Request $request)
    {
        $ids = explode(",", $request->get('ids'));
        return Cms::whereIn('uuid', $ids)->delete();
    }
}