<?php

namespace App\Services;

use App\Models\MasterState;
use Illuminate\Http\Request;

class StateService
{

    public function getSateObject()
    {
        return new MasterState();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getStateData()
    {
        return $this->getSateObject()->with('country')->select('master_states.*');
    }
    /**
     * storeState function
     *
     * @param Request $request
     * @return object
     */
    public function storeState(Request $request)
    {
        $data = $request->validated();
        return $this->getSateObject()->create($data);
    }
    /**
     * getState function
     *
     * @param string $uuid
     * @return object
     */
    public function getState(string $uuid)
    {
        return $this->getSateObject()->where('uuid', $uuid)->first();
    }
    /**
     * updateState function
     *
     * @param Request $request
     * @param string $uuid
     * @return void
     */
    public function updateState(Request $request, string $uuid)
    {
        $data = $request->validated();
        $masterState = $this->getState($uuid);
        $masterState->update($data);
    }
    /**
     * deleteState function
     *
     * @param string $uuid
     * @return boolean
     */
    public function deleteState(string $uuid)
    {
        $masterState = $this->getState($uuid);
        $masterState->delete();
        return true;
    }
    /**
     * multipleDeleteCountry function
     *
     * @param Request $request
     * @return boolean
     */
    public function multipleDeleteState(Request $request)
    {
        $ids = explode(",", $request->get('ids'));
        $bulk_delete_failure = true;
        $ids_arr = [];
        foreach ($ids as $uuid) {
            // get master state by id
            $masterState = $this->getState($uuid);
            // check any user or address that has been associated with a specific state.
            array_push($ids_arr, $uuid);
        }
        // delete state
        if(count($ids_arr) > 0)
        {
            $this->getSateObject()->whereIn('uuid', $ids_arr)->delete();
            $bulk_delete_failure = false;
        }

        return $bulk_delete_failure;
    }
    /**
     * checkUserAssociatedWithCountry function
     *
     * @param string $uuid
     * @return boolean
     */
    public function checkUserAssociatedWithState(string $uuid)
    {
        $masterState = $this->getState($uuid);
        $isUsed = false;
        if ($masterState->masterCity()->count() || $masterState->sellerBusinessAddress()->count() || $masterState->sellerBillingAddress()->count() || $masterState->customerBillingAddress()->count() || $masterState->customerShippingAddress()->count()) {
            $isUsed = true;
        }
        return $isUsed;
    }
}
