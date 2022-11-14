<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Core\Traits\SpatieLogsActivity;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterState extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy,SpatieLogsActivity, Uuids;
    
    /**
     * For table master_states
     *
     * @var string
     */
    protected $table = "master_states";

    protected $fillable = ['country_id', 'name', 'status'];

    /**
     * Getter Setter Methods start
     *
     * Getter methods
     */

    /**
     * getNameAttribute
     *
     * @param  mixed $name
     * @return void
     */
    public function getNameAttribute($name)
    {
        return $name;
    }
    /**
     * getCountryIdAttribute
     *
     * @param  mixed $countryId
     * @return void
     */
    public function getCountryIdAttribute($countryId)
    {
        return $countryId;
    }
    /**
     * getStatusAttribute
     *
     * @param  mixed $status
     * @return void
     */
    public function getStatusAttribute($status)
    {
        return $status;
    }
    /**
     * getCreatedByAttribute
     *
     * @param  mixed $createdBy
     * @return void
     */
    public function getCreatedByAttribute($createdBy)
    {
        return $createdBy;
    }
    /**
     * getModifiedByAttribute
     *
     * @param  mixed $modifiedBy
     * @return void
     */
    public function getModifiedByAttribute($modifiedBy)
    {
        return $modifiedBy;
    }

    /**
     *
     * Setter methods
     */

    /**
    * setNameAttribute
    *
    * @param  mixed $name
    * @return void
    */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
    }
    /**
     * setCountryIdAttribute
     *
     * @param  mixed $countryId
     * @return void
     */
    public function setCountryIdAttribute($countryId)
    {
        $this->attributes['country_id'] = $countryId;
    }
    /**
     * setStatusAttribute
     *
     * @param  mixed $status
     * @return void
     */
    public function setStatusAttribute($status)
    {
        $this->attributes['status'] = $status;
    }
    /**
     * setCreatedByAttribute
     *
     * @param  mixed $createdBy
     * @return void
     */
    public function setCreatedByAttribute($createdBy)
    {
        $this->attributes['created_by'] = $createdBy;
    }
    /**
     * setModifiedByAttribute
     *
     * @param  mixed $modifiedBy
     * @return void
     */
    public function setModifiedByAttribute($modifiedBy)
    {
        $this->attributes['modified_by'] = $modifiedBy;
    }

    /**
     * Getter Setter Methods end
     */

    public function country()
    {
        return $this->belongsTo(MasterCountry::class);
    }

    public function city()
    {
        return $this->hasMany(MasterCity::class, 'state_id', 'id');
    }

    public function masterCity()
    {
        return $this->hasMany(MasterCity::class, 'state_id', 'id');
    }

    public function sellerBusinessAddress()
    {
        return $this->hasMany(SellerBusinessAddress::class, 'state_id', 'id');
    }

    public function sellerBillingAddress()
    {
        return $this->hasMany(SellerBillingAddress::class, 'state_id', 'id');
    }
    
    public function customerBillingAddress()
    {
        return $this->hasMany(CustomerBillingAddress::class, 'state_id', 'id');
    }

    public function customerShippingAddress()
    {
        return $this->hasMany(CustomerShippingAddress::class, 'state_id', 'id');
    }
}
