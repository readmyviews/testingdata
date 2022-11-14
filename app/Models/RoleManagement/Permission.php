<?php

namespace App\Models\RoleManagement;

use App\Core\Traits\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as Permissions;
use App\Traits\Uuids;

class Permission extends Permissions
{
    use HasFactory,SpatieLogsActivity, Uuids;

    protected $table = 'permissions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
        'status',
        'module_id'

    ];

    /**
     * Get the author that wrote the book.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
