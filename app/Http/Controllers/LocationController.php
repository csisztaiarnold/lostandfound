<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Used for saving the location coordinates, it's triggered on a new location query
     *
     * @param Request $request Ajax post data
     * @return void
     */
    public function saveLocationCookie(Request $request)
    {
        $postData = $request->all();
        $lat = $postData['lat'];
        $lng = $postData['lng'];
        \Cookie::queue('location_lat', $lat, 525600);
        \Cookie::queue('location_lng', $lng, 525600);
    }
}
