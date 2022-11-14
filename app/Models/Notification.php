<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'master_customer_id',
        'device_type',
        'device_token',
        'model',
        'model_id',
        'action',
    ];

    public function chats()
    {
        return $this->belongsTo(MasterChat::class,'model_id','id');
    }
}
