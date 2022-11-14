<?php

namespace App\Traits;

use App\Jobs\FbcOrderManageNotificationQueue;
use App\Models\MasterBank;
use App\Models\MasterCountry;
use App\Models\MasterOrderItem;
use App\Models\MasterShippingCarrier;
use Illuminate\Support\Facades\Config;

trait CommonTrait
{
    /**
     * getTrinidadTobagoId function
     *
     * @return int
     */
    public function getTrinidadTobagoId()
    {
        $getId = MasterCountry::where('name', Config::get('params.trinadad_tobago'))->first();
        if (!empty($getId)) {
            return $getId->id;
        }
        return "";
    }
    /**
     * getOtherBankId function
     *
     * @return int
     */
    public function getOtherBankId()
    {
        $getId = MasterBank::where('name', Config::get('params.other_bank'))->first();
        if (!empty($getId)) {
            return $getId->id;
        }
        return "";
    }
    /**
     * getOtherShippingCarrierId function
     *
     * @return int
     */
    public function getOtherShippingCarrierId()
    {
        $getId = MasterShippingCarrier::where('name', Config::get('params.other_shipping_carrier'))->first();
        if (!empty($getId)) {
            return $getId->id;
        }
        return "";
    }
    /**
     * emailNotificationData function
     *
     * @param integer $id
     * @return void
     */
    public function emailNotificationData(int $id)
    {
        $emailData = MasterOrderItem::with(['orderCancellationReason', 'customer', 'order', 'product', 'order.shippingAddress' => function ($q) {
            $q->with('country', 'state', 'city');
        }])->where('id', $id)->first();
        if (!empty($emailData)) {
            $fbmOrderNotificationQueue = new FbcOrderManageNotificationQueue($emailData);
            dispatch($fbmOrderNotificationQueue)->delay(now()->addSeconds(Config::get('params.queue_dispach_minutes')));
        }
    }
}