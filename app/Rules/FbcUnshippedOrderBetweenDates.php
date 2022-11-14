<?php

namespace App\Rules;

use Carbon\Carbon;
use App\Models\MasterOrderItem;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Validation\Rule;

class FbcUnshippedOrderBetweenDates implements Rule
{
   
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $dateRange = explode('-', str_replace(" ", "", $value));
        $orderData = MasterOrderItem::where('status', Config::get('params.order_status_key.unshipped'))->whereHas('product', function ($query) {
            $query->where('fulfilment_type', Config::get('params.product_fulfilment_types_key.FBC'));
        })->whereHas('order', function ($q) use ($dateRange) {
            $startDate = Carbon::createFromFormat('d/m/Y', $dateRange[0])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $dateRange[1])->format('Y-m-d');
            if ($startDate == $endDate) {
                $q->whereDate('created_at', $startDate);
            } else {
                $q->whereBetween('created_at', [$startDate,$endDate]);
            }
        })->get();

        if (!$orderData->isEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'No orders to be picked between selected dates';
    }
}
