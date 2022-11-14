<?php

namespace App\Services;

use App\Models\MasterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;
use App\Traits\FileManager;


class CategoryService
{

    use FileManager;
    public function getCategoryObject()
    {
        return new MasterCategory();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getCategoryData(Request $request)
    {
        return $this->getCategoryObject()->with('sub_child', 'parent')->select('master_categories.*')->when(request('status'), function ($query) {
            $query->where('status', request('status'));
        })->when(request('parent_id_null'), function ($query) {
            $query->where('parent_id', '=', 0);
        });
    }
    /**
     * storeCity function
     *
     * @param Request $request
     * @return object
     */
    public function storeCategory(Request $request)
    {
        $data = $request->validated();
        $masterCategory = $this->getCategoryObject()->create($data);
        $uuid = $masterCategory->uuid;
        $id = $masterCategory->id;
        $uploadedFiles = [];
        // upload & update files (icon)
        if ($request->hasFile('icon_url')) {
            if($icon_url = $this->createOrReplaceFile($request->file('icon_url'),'category_image',"icon_url",$uuid.'/icon_url_'))
            {
                $uploadedFiles['icon_url'] = $icon_url;
            }
        }

        if ($request->hasFile('mobile_icon_url')) {
            if ($mobile_icon_url = $this->createOrReplaceFile($request->file('mobile_icon_url'),'category_image','mobile_icon_url', $uuid . '/mobile_icon_url_')) {
                $uploadedFiles['mobile_icon_url'] = $mobile_icon_url;
            }
        }

        if(!empty($uploadedFiles))
        {
            $this->getCategoryObject()->where('id', $id)->update($uploadedFiles);
        }

        return $masterCategory;
    }
    /**
     * getCity function
     *
     * @param string $uuid
     * @return object
     */
    public function getCategory(string $uuid)
    {
        return $this->getCategoryObject()->where('uuid', $uuid)->first();
    }
    /**
     * getCategoryById function
     *
     * @param integer $id
     * @return object
     */
    public function getCategoryById(int $id)
    {
        return $this->getCategoryObject()->where('id', $id)->first();
    }

    /**
     * updateCity function
     *
     * @param Request $request
     * @param string $uuid
     * @return void
     */
    public function updateCategory(Request $request, string $uuid)
    {
        $data = $request->validated();
        $masterCategory = $this->getCategory($uuid);
        $id = $masterCategory->id;
        $masterCategory->update($data);
        $uploadedFiles = [];
        // upload & update files (icon)
        if ($request->hasFile('icon_url')) {

            if($icon_url = $this->createOrReplaceFile($request->file('icon_url'),'category_image',"icon_url",$uuid.'/icon_url_',$masterCategory))
            {
                $uploadedFiles['icon_url'] = $icon_url;
                //Storage::disk('category_image')->delete($masterCategory->icon_url);
            }


        }
       if ($request->hasFile('mobile_icon_url')) {

            if($mobile_icon_url = $this->createOrReplaceFile($request->file('mobile_icon_url'),'category_image',"mobile_icon_url",$uuid.'/mobile_icon_url_',$masterCategory))
            {
                $uploadedFiles['mobile_icon_url'] = $mobile_icon_url;
                //Storage::disk('category_image')->delete($masterCategory->mobile_icon_url);
            }

       }

        if(!empty($uploadedFiles))
        {
            $this->getCategoryObject()->where('uuid', $uuid)->update($uploadedFiles);
        }


        return $masterCategory;
    }
    /**
     * deleteCity function
     *
     * @param string $uuid
     * @return boolean
     */
    public function deleteCategory(string $uuid)
    {
        $masterCategory = $this->getCategory($uuid);
        // unlink directory
        if (Storage::disk('category_image')->exists($masterCategory->id)) {
            Storage::disk('category_image')->deleteDirectory($masterCategory->id);
        }
        $masterCategory->delete();
        return true;
    }
    /**
     * multipleDeleteCity function
     *
     * @param Request $request
     * @return boolean
     */
    public function multipleDeleteCategory(Request $request)
    {
        $ids = explode(",", $request->get('ids'));
        // unlink directory
        foreach ($ids as $id) {
            if (Storage::disk('category_image')->exists($id)) {
                Storage::disk('category_image')->deleteDirectory($id);
            }
        }
        // delete category
        $this->getCategoryObject()->whereIn('uuid', $ids)->delete();
        return true;
    }

}
