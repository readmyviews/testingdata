<?php

namespace App\Models\RoleManagement;

use App\Core\Traits\SpatieLogsActivity;
use Spatie\Permission\Models\Role as Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuids;

class Role extends Roles
{
    use HasFactory,SpatieLogsActivity, Uuids;

    protected $table = 'roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
        'status'
    ];
}
