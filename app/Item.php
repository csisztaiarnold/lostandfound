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

    /**
     * Finds all the nearby items
     *
     * @param int $latitude Latitude of the submitted item location
     * @param int $longitude Longitude of the sumbitted item location
     * @param int $distance Distance radius
     * @return object|void
     */
    public static function nearbyItems($latitude = 0, $longitude = 0, $distance = 30, $paginateBy = 10)
    {
        $items = Item::selectRaw('*')
            ->join('locations as l', 'l.id', '=', 'location_id')
            ->join('images as i', 'i.item_id', '=', 'items.id')
            ->whereRaw($distance.' > (6371 * acos(cos(radians(' . $latitude . ')) * cos(radians(`lat`)) * cos(radians(`lng`) - radians(' . $longitude . ')) + sin(radians(' . $latitude . ')) * sin(radians(`lat`)))) AND `active` = 1')
            ->orderBy('items.created_at','desc')
            ->paginate($paginateBy);

        if($items) {
            return $items;
        }
    }
}
