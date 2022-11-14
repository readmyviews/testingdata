<?php

namespace App\Traits;
use DB;

trait GlobalScope
{
    public function scopeCountQuery($query,$name,$filter='')
    {
        if($filter != '')
        {
            return $query->selectRaw('"'.$name.'" as `key`,COUNT(id) as `value`,'.$filter);
        }else{
            return $query->selectRaw('"'.$name.'" as `key`,COUNT(id) as `value`');
        }
    }
}
