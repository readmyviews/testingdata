<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Core\Traits\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Str;

class MasterCountry extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy,SpatieLogsActivity;
    
    /**
     * For table master_countries
     *
     * @var string
     */
    protected $table = "master_countries";

    protected $fillable = ['name', 'flag_url', 'country_code', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if($model->uuid == '')
                $model->uuid = (string) Str::uuid();
        });

        static::deleting(function ($country) {
            $country->state()->delete();
            $country->city()->delete();
        });
    }

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
     * getCountryCodeAttribute
     *
     * @param  mixed $countryCode
     * @return void
     */
    public function getCountryCodeAttribute($countryCode)
    {
        return $countryCode;
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
     * setCountryCodeAttribute
     *
     * @param  mixed $countryCode
     * @return void
     */
    public function setCountryCodeAttribute($countryCode)
    {
        $this->attributes['country_code'] = $countryCode;
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
    // /**
    //  * setModifiedByAttribute
    //  *
    //  * @param  mixed $modifiedBy
    //  * @return void
    //  */
    // public function getFlagUrlAttribute($flag)
    // {
    //     dd($flag);
    //     return
    //     // $this->attributes['modified_by'] = $modifiedBy;
    // }

    /**
     * Getter Setter Methods end
     */

    public function state()
    {
        return $this->hasMany(MasterState::class, 'country_id', 'id');
    }

    public function city()
    {
        return $this->hasMany(MasterCity::class, 'country_id', 'id');
    }

    public function masterSeller()
    {
        return $this->hasMany(MasterSeller::class, 'country_id', 'id');
    }

    public function sellerBusinessAddress()
    {
        return $this->hasMany(SellerBusinessAddress::class, 'country_id', 'id');
    }

    public function sellerBillingAddress()
    {
        return $this->hasMany(SellerBillingAddress::class, 'country_id', 'id');
    }

    public function sellerBankDetail()
    {
        return $this->hasMany(SellerBankDetail::class, 'country_id', 'id');
    }

    public function masterStore()
    {
        return $this->hasMany(MasterStore::class, 'shipping_country_id', 'id');
    }

    public function customerBillingAddress()
    {
        return $this->hasMany(CustomerBillingAddress::class, 'country_id', 'id');
    }

    public function customerShippingAddress()
    {
        return $this->hasMany(CustomerShippingAddress::class, 'country_id', 'id');
    }
}
