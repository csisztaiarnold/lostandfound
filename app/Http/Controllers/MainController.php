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
        return view('index')->with([
            'categories' => array_merge(\App\Item::categories(), ['0' => __('All')]),
        ]);
    }

}
