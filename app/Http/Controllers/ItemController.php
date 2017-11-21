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
                        $message->subject(__('Your editing/deleting link for a Lost and Found item'));
                        $message->from(Config::get('site.success_email_from'));
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
                    $itemActionsLink = \URL::to('items').'/'.$item->id;
                    // Send an activation link to each moderator
                    foreach(Config::get('site.moderator_email_array') as $moderatorEmail) {
                        Mail::send('emails.item-created-moderation', ['itemActionsLink' => $itemActionsLink], function ($message) use ($email) {
                            $message->subject(__('New item submitted and awaiting moderation'));
                            $message->from(Config::get('site.success_email_from'));
                            $message->to($moderatorEmail);
                        });
                    }
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
     * @param int $id The item's ID
     * @param string $action Moderator action
     * @return
     */
    public function show($id = 0, $action = '')
    {
        // Is a moderator logged in?
        if(\Auth::id() && (in_array(\Auth::user()->email,Config::get('site.moderator_email_array')))){
            $item = Item::where('id', $id)->first();
            if($action === 'activate') {
                // TODO: Add activation_date field to the table. Low priority.
                Item::where('id', $id)->update([
                    'active' => 1,
                ]);
                // Send notification email to the item submitter if the item is activated
                $notificationEmailArray = \App\Notification::nearbyItemNotificationRequestEmails($item->location()->first()->lat, $item->location()->first()->lng, $item->category_id);
                \App\Notification::sendNotificationEmails($notificationEmailArray, $item->type, $item->id, $item->title, $item->description);
                return back()->with('success', __('The item is successfully activated'));
            } elseif($action === 'deactivate') {
                Item::where('id', $id)->update([
                    'active' => 0,
                ]);
                // TODO: Send notification email to the item submitter if the item is deactivated
                // TODO: Send possible reason too? Low priority.
                return back()->with('success', __('The item is successfully deactivated'));
            } elseif($action === 'delete') {
                Item::where('id', $id)->delete();
                // TODO: Hard delete ATM, change it to soft delete
                // TODO: Confirmation dialog on the frontend side
                // TODO: Send notification email to the item submitter if the item is deleted manually
                // TODO: Send possible reason too? Low priority.
                return back()->with('success', __('The item is successfully deleted'));
            }

            $loggedInModerator = true;
        } else {
            $item = Item::where('id', $id)->where('active', 1)->first();
            $loggedInModerator = false;
        }

        $images = [];
        if(count($item) > 0) {
            $images = $item->images()->get();
        }

        return view('items.show')->with([
            'item' => $item,
            'images' => $images,
            'moderation' => $loggedInModerator,
        ]);
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

    /**
     * List the nearby items on the home page
     *
     * @param Request $request Ajax post data
     * @return
     */
    public function listItemsOnHomepage(Request $request)
    {
        $postData = $request->all();
        if($postData) {
            $lat = $postData['lat'];
            $lng = $postData['lng'];
            $nearbyItems = \App\Item::nearbyItems($lat, $lng, $distance = 30, $paginateBy = 10);
            header('Content-type: application/json');
            echo json_encode($nearbyItems);
        }
    }

}
