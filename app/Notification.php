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
}
