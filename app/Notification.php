<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'category_id', 'lat', 'lng', 'distance',
    ];


    /**
     * Compares the submitted item location with the notification location distances
     * and returns all those notification emails where the distance is lower than the
     * max. distance specified in the notification
     *
     * @param int $latitude Latitude of the submitted item location
     * @param int $longitude Longitude of the sumbitted item location
     * @param int $category Category of the item
     * @return object|void
     */
    public static function nearbyItemNotificationRequestEmails($latitude = 0, $longitude = 0, $category = 0)
    {
        if($category !== 0) {
            $category = $category;
        } else {
            $category = null;
        }

        $notifications = Notification::select('email')
            ->whereRaw('distance > (6371 * acos(cos(radians('.$latitude.')) * cos(radians(`lat`)) * cos(radians(`lng`) - radians('.$longitude.')) + sin(radians('.$latitude.')) * sin(radians(`lat`))))')
            ->when($category, function($query) use ($category) {
                $query->where('category_id', $category);
            })->get();

        if(count($notifications) > 0) {
            return $notifications;
        }
    }

}
