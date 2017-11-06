<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
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
     * Saves the notification
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email'         => 'required|email',
            'location'      => 'required',
            'location_lat'  => 'required',
            'location_lng'  => 'required',
            'category_id'   => 'required',
            'distance'      => 'required',
        ]);

        if($validate->fails()) {
            return back()->witherrors($validate)->withInput();
        } else {
            $uniqueId = \App\Helpers\RandomStringHelper::getToken(32);

            $location = new Notification;
            $location->email        = $request->email;
            $location->location     = $request->location;
            $location->lat          = $request->location_lat;
            $location->lng          = $request->location_lng;
            $location->category_id  = $request->category_id;
            $location->distance     = $request->distance;
            $location->unique_id    = $uniqueId;
            $location->save();

            return redirect('notifications/success');
        }
    }


    /**
     * Returns the success page upon successful save
     *
     * @return \Illuminate\View\View
     */
    public function success()
    {
        return view('notifications.success');
    }
}
