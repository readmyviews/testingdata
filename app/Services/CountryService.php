<?php

namespace App\Services;

use App\Models\MasterCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CountryService
{

    public function getCountryObject()
    {
        return new MasterCountry();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getCountryData(int $countryid = null)
    {
        //$countryid for not getting state of given country
        return $this->getCountryObject()->when($countryid, function ($query) use ($countryid) {
            $query->where('id', '!=', $countryid);
        });
    }
    /**
     * storeCountry function
     *
     * @param Request $request
     * @return object
     */
    public function storeCountry(Request $request)
    {
        $data = $request->validated();
        if ($request->hasFile('file')) {
            $data['flag_url'] = Storage::disk('country_flag')->put(config('params.upload.country_flag'), $request->file);
        }
        return $this->getCountryObject()->create($data);
    }
    /**
     * getProductAttribute function
     *
     * @param string $uuid
     * @return object
     */
    public function getCountry(string $uuid)
    {
        return $this->getCountryObject()->where('uuid', $uuid)->first();
    }
    /**
     * updateCountry function
     *
     * @param Request $request
     * @param string $uuid
     * @return void
     */
    public function updateCountry(Request $request, string $uuid)
    {
        $data = $request->validated();
        $masterCountry = $this->getCountry($uuid);
        if ($request->hasFile('file')) {
            $data['flag_url'] = Storage::disk('country_flag')->put(config('params.upload.country_flag'), $request->file);
            if ($masterCountry->flag_url != '' && Storage::disk('country_flag')->exists($masterCountry->flag_url)) {
                Storage::disk('country_flag')->delete($masterCountry->flag_url);
            }
        }
        return $masterCountry->update($data);
    }
    /**
     * deleteCountry function
     *
     * @param string $uuid
     * @return boolean
     */
    public function deleteCountry(string $uuid)
    {
        $masterCountry = $this->getCountry($uuid);
        // unlink country flag
        if ($masterCountry->flag_url != '' && Storage::disk('country_flag')->exists($masterCountry->flag_url)) {
            Storage::disk('country_flag')->delete($masterCountry->flag_url);
        }
        // delete country
        $masterCountry->delete();
        return true;
    }
    /**
     * multipleDeleteCountry function
     *
     * @param Request $request
     * @return boolean
     */
    public function multipleDeleteCountry(Request $request)
    {
        $ids = explode(",", $request->get('ids'));
        $bulk_delete_failure = true;
        $ids_arr = [];
        foreach ($ids as $uuid) {
            // get master country by id
            $masterCountry = $this->getCountry($uuid);
            // unlink country flag
            if ($masterCountry->flag_url != '' && Storage::disk('country_flag')->exists($masterCountry->flag_url)) {
                Storage::disk('country_flag')->delete($masterCountry->flag_url);
            }
            array_push($ids_arr, $uuid);
        }
        // delete country
        if(count($ids_arr) > 0)
        {
            $this->getCountryObject()->whereIn('uuid', $ids_arr)->delete();
            $bulk_delete_failure = false;
        }

        return $bulk_delete_failure;
    }

}
