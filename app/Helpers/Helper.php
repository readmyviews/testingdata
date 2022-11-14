<?php

use App\Models\FcmTokenUser;
use App\Models\HelpcenterContactus;
use App\Models\MasterCustomer;
use App\Models\MasterOrder;
use App\Models\oauthAccessToken;
use App\Models\OrderStatusTrack;
use App\Models\ProductAttributeMapping;
use App\Models\PurchaseOrder\MasterPurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

//create fcm notification
if (!function_exists('CreateNotification')) {
    function CreateNotification($data)
    {
        $title = "Carizone";
        $LoggedInUser = auth()->guard('api')->user();
        $badge = 1;
        $registrationIds = FcmTokenUser::where('id', $data['receiver_id'])->whereNotNull('fcmtoken')->pluck('fcmtoken')->all();
        $notification = array(
            "body" => $data['message'],
            "title" => $title,
            "vibrate" => 1,
            "sound" => 1,
            "badge" => 0,
            "color" => "#3364ac",
            "user_id" => auth()->guard('api')->user()->id,
            "type" => $data['from'],
            "name" => auth()->guard('api')->user()->name,
            "avatar" => auth()->guard('api')->user()->image,
        );
        $fields = array(
            "to" => $registrationIds,
            "priority" => "high",
            "notification" => $notification,
            "data" => $notification,
            "type" => $data['from'],
            "name" => auth()->guard('api')->user()->name,
            "avatar" => auth()->guard('api')->user()->image,
        );
        $SERVER_API_KEY = config('params.firebase.server_api_key');
        $headers = array(
            'Authorization: key=' . $SERVER_API_KEY,
            "Content-Type: application/json",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //Log::info("emergency-notify-status-" . $result);
        if (curl_error($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return "success";
    }
}

//add user fcm token
if (!function_exists('AddFcmToken')) {
    function AddFcmToken($device_id, $user)
    {
        try {
            $delete_record = FcmTokenUser::where(['device_id' => $device_id])->delete();
            $record = FcmTokenUser::create([
                'user_id' => $user->id,
                'device_id' => $device_id,
                'device_type' => $user->device_type,
                'fcm_token' => $user->device_token,
            ]);
            return "success";
        } catch (\Exception $e) {
            Log::info('add-fcm-token-error-' . $e);
        }
    }
}

//expire user token
if (!function_exists('expireUserToken')) {
    function expireUserToken($user_id, $device_id = '')
    {
        $userData = MasterCustomer::find($user_id);
        if (!empty($userData)) {
            //delete from api side
            if (!empty(auth()->guard('api')->user())) {
                $oauth_tokenid = auth()->guard('api')->user()->token()->id;
                // delete from
                oauthAccessToken::where([['user_id', $userData->id], ['id', $oauth_tokenid]])->delete();
            } else {
                //delete from admin side
                oauthAccessToken::where('user_id', $userData->id)->delete();
            }
            // update device_token null in users table
            MasterCustomer::where('id', $userData->id)->update(['device_token' => '']);
            // delete record from fcm token user
            $fcm_user = FcmTokenUser::where('userid', $userData->id);
            if (!empty($device_id)) {
                $fcm_user = $fcm_user->where('deviceid', $device_id);
            }
            $fcm_user->delete();
            return true;
        } else {
            return false;
        }
    }
}

/**active menu for sidebar */
function activeMenu($uri = '')
{
    $active = '';
    // if (Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/*' . $uri) || Request::is($uri)) {
    if (Request::routeIs($uri . '*')) {
        $active = 'active';
    }
    return $active;
}

/**active menu for sidebar */
function activeSubMenu($uris = [])
{
    $active = '';
    if (!empty($uris) && count($uris) > 0) {
        foreach ($uris as $key => $uri) {
            // if (Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri)) {
            if (Request::routeIs($uri . '*')) {
                $active = 'show';
            }
        }
    }

    return $active;
}

/**
 * get random string
 */
if (!function_exists('generateRandomString')) {
    function generateRandomString($length = '')
    {
        if (empty($length)) {
            $length = 5;
        }
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

