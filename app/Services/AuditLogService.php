<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;

class AuditLogService
{

    /**
     * Get All data
     *
     * @return object
     */
    public function getActivityData()
    {
        return Activity::query();
    }
}