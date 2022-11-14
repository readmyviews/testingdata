<?php

namespace App\Services;

use App\Models\MasterCity;
use Illuminate\Http\Request;

class CityService
{

    public function getCityObject()
    {
        return new MasterCity();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getCityData()
    {
        return $this->getCityObject()->with(['country', 'state'])->select('master_cities.*');
    }
    /**
     * storeCity function
     *
     * @param Request $request
     * @return object
     */
    public function storeCity(Request $request)
    {
        $data = $request->validated();
        return $this->getCityObject()->create($data);
    }
    /**
     * getCity function
     *
     * @param string $uuid
     * @return object
     */
    public function getCity(string $uuid)
    {
        return $this->getCityObject()->where('uuid', $uuid)->first();
    }
    /**
     * updateCity function
     *
     * @param Request $request
     * @param string $uuid
     * @return void
     */
    public function updateCity(Request $request, string $uuid)
    {
        $data = $request->validated();
        $masterState = $this->getCity($uuid);
        $masterState->update($data);
    }
    /**
     * deleteCity function
     *
     * @param string $uuid
     * @return boolean
     */
    public function deleteCity(string $uuid)
    {
        $masterCity = $this->getCity($uuid);
        $masterCity->delete();
        return true;
    }
    /**
     * multipleDeleteCity function
     *
     * @param Request $request
     * @return boolean
     */
    public function multipleDeleteCity(Request $request)
    {
        $ids = explode(",", $request->get('ids'));
        $bulk_delete_failure = false;
        $ids_arr = [];
        foreach ($ids as $uuid) {
            // get master city by id
            $masterCity = $this->getCity($uuid);
            array_push($ids_arr, $uuid);
            // check any user or address that has been associated with a specific city.
            if ($masterCity->sellerBusinessAddress()->count() || $masterCity->sellerBillingAddress()->count() || $masterCity->customerBillingAddress()->count() || $masterCity->customerShippingAddress()->count()) {
                $bulk_delete_failure = true;
            } else {

            }
        }
        // delete city
        $this->getCityObject()->whereIn('uuid', $ids_arr)->delete();

        return $bulk_delete_failure;
    }
    /**
     * checkUserAssociatedWithCountry function
     *
     * @param string $uuid
     * @return boolean
     */
    public function checkUserAssociatedWithCity(string $uuid)
    {
        $masterCity = $this->getCity($uuid);
        $isUsed = false;
        if ($masterCity->sellerBusinessAddress()->count() || $masterCity->sellerBillingAddress()->count() || $masterCity->customerBillingAddress()->count() || $masterCity->customerShippingAddress()->count()) {
            $isUsed = true;
        }
        return $isUsed;
    }
}
