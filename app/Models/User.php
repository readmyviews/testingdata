<?php

namespace App\Models;

use App\Models\UserAddress;
use App\Models\MasterSeller;
use Laravel\Sanctum\HasApiTokens;
use App\Models\RoleManagement\Role;
use Spatie\Permission\Traits\HasRoles;
use App\Core\Traits\SpatieLogsActivity;
use App\Traits\Uuids;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;
    use SoftDeletes,SpatieLogsActivity, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'gender',
        'avatar',
        'status',
        'user_role',
        'password',
        'phone_no',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'postal_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all of the posts for the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'user_role');
    }

    /**
     * Get all of the verifiersApplication for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifiersApplication()
    {
        return $this->hasMany(MasterSeller::class, 'app_verifier_id', 'id');
    }
}
