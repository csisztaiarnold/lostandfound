<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Image extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id', 'filename', 'extension',
    ];

    /**
     * Checks if the image limit is reached for a certain item
     * TODO: This could be overwritten once user accounts are introduced
     *
     * @param int $item_id The item's ID
     * @return bool
     */
    public static function imageLimitReached($item_id = 0)
    {
        $numberOfImages = Image::where('item_id', $item_id)->count();
        if($numberOfImages >= Config::get('site.image_limit_per_user')) {
            return true;
        } else {
            return false;
        }
    }
}
