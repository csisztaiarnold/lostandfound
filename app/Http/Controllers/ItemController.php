<?php

namespace App\Http\Controllers;

use App\Item;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class ItemController extends Controller
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
     * Display the items
     *
     * @return Response
     */
    public function index()
    {
        $items = Item::all();
        return view('items.index')->with([
            'items' => $items,
        ]);
    }

    /**
     * Show the form for adding a new item
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('items.create')->with([
            'categories' => Item::categories(),
        ]);
    }


    /**
     * Stores the item in the database and redirects to the image upload page
     *
     * @param Request $request Form data
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'type'          => 'required',
            'title'         => 'required',
            'location'      => 'required',
            'description'   => 'required',
            'email'         => 'nullable|sometimes|email',
        ]);

        if($validate->fails()) {
            return back()->witherrors($validate)->withInput();
        } else {
            $uniqueId = \App\Helpers\RandomStringHelper::getToken(32);
            Session::put('unique_id', $uniqueId);

            $location = new Location;
            $location->location = $request->location;
            $location->lat      = $request->location_lat;
            $location->lng      = $request->location_lng;
            $location->save();

            $item = new Item;
            $item->title        = $request->title;
            $item->location     = $request->location; // TODO: this field is unnecessary; low priority
            $item->description  = $request->description;
            $item->email        = $request->email;
            $item->type         = $request->type;
            $item->category_id  = $request->category_id;
            $item->unique_id    = $uniqueId;
            $item->admin_hash   = \App\Helpers\RandomStringHelper::getToken(32);

            // The item is not active yet. The 'active' column defaults to 0,
            // unless administrator approval is set to false
            if(Config::get('site.administrator_approval') === false) {
                $item->active = 1;
            }

            $item->location()->associate($location);
            $item->save();

            return redirect('images/upload'); // ImageController@upload
        }
    }

    /**
     * Finishing the submission, sending an email, displaying a success page
     *
     * @return \Illuminate\View\View
     */
    public function success()
    {
        if(Session::get('unique_id') !== null) {
            // Is the item still in the database?
            $item = Item::where('unique_id', Session::get('unique_id'))->with('location')->first();
            if (count($item) !== 0) {
                $email = $item->email;
                // Send a mail to the user
                if ($email) {

                    $additionalActivationMessage = 'Your item was submitted to the site. You are ready to go!';
                    if($item->active === 0) {
                        $additionalActivationMessage = __('Your item is not active yet, it has to be reviewed by an admin. You will be immediately notified when your item gets activated.');
                    }
                    $itemActionsLink = \URL::to('items').'/'.$item->unique_id .'/edit';
                    Mail::send('emails.item-created-success', ['itemActionsLink' => $itemActionsLink, 'additionalActivationMessage' => $additionalActivationMessage], function ($message) use ($email) {
                        $message->from(Config::get('site.success_email_from'), __('Your editing/deleting link for a Lost and Found item'));
                        $message->to($email);
                    });
                    // If the items are not required to be approved by an admin, send the notification emails immediately
                    if(Config::get('site.administrator_approval') === false) {
                        $notificationEmailArray = \App\Notification::nearbyItemNotificationRequestEmails($item->location()->first()->lat, $item->location()->first()->lng, $item->category_id);
                        \App\Notification::sendNotificationEmails($notificationEmailArray, $item->type, $item->id, $item->title, $item->description);
                    }
                }
                // Send a moderation mail to the admin
                if ($email) {
                    $itemActionsLink = \URL::to('items/moderate').'/'.$item->id.'/'.$item->unique_id.'/'.$item->admin_hash;
                    Mail::send('emails.item-created-moderation', ['itemActionsLink' => $itemActionsLink], function ($message) use ($email) {
                        $message->from(Config::get('site.success_email_from'), __('New item submitted and awaiting moderation'));
                        $message->to(Config::get('site.administrator_email'));
                    });
                }

                Session::forget('unique_id');
                return view('items.success');
            } else {
                return redirect('items');
            }
        } else {
            return redirect('/');
        }
    }

    /**
     * Display the item
     *
     * @param  int  $id
     * @return
     */
    public function show($id)
    {
        // TODO
    }

    /**
     * Edit the item
     *
     * @param  int  $id
     * @return
     */
    public function edit($id)
    {
        // TODO
    }

    /**
     * Moderate the item
     *
     * @param  int  $id
     * @return
     */
    public function moderate($item_id = 0, $unique_id = '', $admin_hash = '', $action = '')
    {
        $item = Item::where('id', $item_id)->where('unique_id', $unique_id)->where('admin_hash',$admin_hash)->with('location')->with('images')->first();

        if(isset($item->id)) {
            if($action === 'activate') {

                Item::where('id', $item_id)->where('unique_id', $unique_id)->where('admin_hash',$admin_hash)->update([
                    'active' => 1,
                ]);

                // TODO: Send notification email to the item submitter if the item is activated

                // TODO: Send notification emails to those who requested nearby item notifications on activation

                return back()->with('success', __('The item is successfully activated'));
            }

            if($action === 'delete') {

            }

            return view('items.show')->with([
                'item' => $item,
                'action' => $action,
                'images' => $item->images()->get(),
                'moderation' => true,
            ]);
        } else {
            // TODO: temporary error message
            die('Unauthorized access');
        }
    }

    /**
     * Update the item
     *
     * @param  int  $id
     * @return
     */
    public function update($id)
    {
        // TODO
    }

    /**
     * Remove the item
     *
     * @param  int  $id
     * @return
     */
    public function destroy($id)
    {
        // TODO
    }

}
