<?php

namespace App\Traits;

use App\Models\FcmTokenUser;
use Illuminate\Support\Facades\Log;

trait NotificationTrait
{
    /**
     * Get data IOS of given user & send push notification
     */

    function sendIOSAlertNotification($userid, $title, $message, $actionid, $notification_type)
    {
        $iosTokens = FcmTokenUser::where('userid', $userid)->where('devicetype', 'IOS')->whereNotNull('fcmtoken')->pluck('fcmtoken')->all();
        if (!empty($iosTokens)) {
            $dataIos = [
                "registration_ids" => $iosTokens,
                "notification" => [
                    "title" => $title,
                    "body" => $message,
                ],
                "data" => [
                    "id" => $actionid,
                    "notification_type" => $notification_type,
                ],
            ];
            $iosStatus = $this->sendAlertNotification($dataIos);
        }
    }
    /**
     * Get data Android of given user & send push notification
     */

    function sendAndroidAlertNotification($userid, $title, $message, $actionid, $notification_type)
    {
        $androidTokens = FcmTokenUser::where('userid', $userid)->where('devicetype', 'Android')->whereNotNull('fcmtoken')->pluck('fcmtoken')->all();
        if (!empty($androidTokens)) {
            $dataAndroid = [
                "registration_ids" => $androidTokens,
                "data" => [
                    "id" => $actionid,
                    "title" => $title,
                    "body" => $message,
                    "notification_type" => $notification_type,
                ],
            ];
            $androidStatus = $this->sendAlertNotification($dataAndroid);
        }
    }
    /**
     * common function for send push notification of given android & ios data token
     */

    function sendAlertNotification($data)
    {
        try {
            $dataString = json_encode($data);
            $SERVER_API_KEY = config('params.firebase.server_api_key');
            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);
            Log::info("notify-msg-" . $response);
            return "success";
        } catch (\Exception$e) {
            return response()->json([
                'status_code' => 500,
                'response' => trans('message.api.error'),
                'message' => $e->getMessage(),
            ]);
        }
    }
}