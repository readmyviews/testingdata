<?php
namespace App\Traits;

trait CommonQuery
{
    public static function scopeGetSingleRow($query, $parameter)
    {
        return $query->where('uuid',$parameter);
    }
}
