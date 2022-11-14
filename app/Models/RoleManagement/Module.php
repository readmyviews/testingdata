<?php

namespace App\Models\RoleManagement;

use App\Core\Traits\SpatieLogsActivity;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory, SpatieLogsActivity, Uuids;

    protected $table = 'modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];


    /**
     * Get all of the posts for the user.
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return ucfirst($this->attributes['name']);
    }


    /**
     * Set name.
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
    }
}
