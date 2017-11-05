<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'title', 'email', 'description', 'location',
    ];

    /**
     * Get the location record associated with the item.
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    /**
     * Get the images associated with the item.
     */
    public function images()
    {
        return $this->hasMany('App\Image');
    }

    /**
     * Get item categories based on the current locale
     *
     * @return array Category list
     */
    public static function categories()
    {
        $locale = \App::getLocale();
        if (\Config::get('site.item_categories_' . $locale) === null) {
            return \Config::get('site.item_categories_' . \App::getLocale());
        } else {
            return \Config::get('site.item_categories_en');
        }
    }
}
