<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmTokenUser extends Model
{
    use HasFactory;
    protected $table = 'customer_fcm_tokens';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'userid',
        'deviceid',
        'devicetype',
        'fcmtoken',
    ];
}