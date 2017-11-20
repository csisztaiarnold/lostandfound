<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
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
     * The site's homepage
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categoryArray = \App\Item::categories();
        $categoryArray[0] = __('All');
        $items = \App\Item::where('active',1)->with('location')->with('images')->get();
        $locationLatCookie = \Cookie::get('location_lat');
        $locationLngCookie = \Cookie::get('location_lng');
        return view('index')->with([
            'categories' => $categoryArray,
            'items' => $items,
            'location_lat_cookie' => $locationLatCookie,
            'location_lng_cookie' => $locationLngCookie,
        ]);
    }

}
