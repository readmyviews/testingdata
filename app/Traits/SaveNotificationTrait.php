<?php

namespace App\Traits;

use App\Models\Notification;

trait SaveNotificationTrait
{
    /**
     * save notification to database
     */
    function saveNotification($user, $type, $model, $title, $action)
    {
        return Notification::create([
            'master_customer_id' => !empty($user) ? $user->id : '',
            'device_type' => !empty($user) ? $user->devicetype : '',
            'device_token' => !empty($user) ? $user->devicetoken : '',
            'model' => $type,
            'model_id' => $model->id,
            'title' => $title,
            'action' => $action,
        ]);
    }

}