<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use App\Traits\CreatedUpdatedBy;
use Spatie\Sluggable\SlugOptions;
use App\Core\Traits\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuids;

class Cms extends Model
{
    use HasFactory,HasSlug,CreatedUpdatedBy,SpatieLogsActivity, Uuids;

    protected $table = 'cms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'slug',
        'is_mobile_view'
    ];


    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
