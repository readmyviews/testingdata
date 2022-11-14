<?php

namespace App\Models;

use App\Core\Traits\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Traits\GlobalScope;

class MasterCategory extends Model
{
    use HasFactory, HasSlug, SpatieLogsActivity, SoftDeletes, GlobalScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'uuid',
        'description',
        'slug',
        'icon_url',
        'mobile_icon_url',
        'parent_id',
        'status',
        'created_by',
        'modified_by',
    ];

    /**
     * boot use to override create method
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($seller) {
            $seller->uuid = (string) Str::uuid();
        });
    }

    /**
     * child relation with self
     * */
    public function sub_child()
    {
        return $this->hasMany(self::class, 'parent_id')->with([
            'sub_child',
        ]);
    }

    /**
     * get parent category
     * */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getParentsNames()
    {
        if ($this->parent) {
            return $this->parent->getParentsNames() . " > " . $this->name;
        } else {
            return $this->name;
        }
    }
}
