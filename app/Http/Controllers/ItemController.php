<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
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
        return view('items.create');
    }


    /**
     * Stores the item in the database and redirects to the image upload page
     *
     * @param Request $request Form data
     * @return \Illuminate\Routing\Redirector
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
            $item = new Item;
            $item->title        = $request->title;
            $item->location     = $request->location;
            $item->description  = $request->description;
            $item->email        = $request->email;
            $item->type         = $request->type;
            $item->unique_id    = $uniqueId;

            // The item is not active yet! The 'active' column defaults to 0
            // Before activating it, first of all, let's save some images.
            // Secondly, the final activation will be performed by an admin
            $item->save();

            return redirect('items/images');
        }
    }

    /**
     * Upload images
     *
     * @param Request $request Form data
     * @return \Illuminate\View\View|\Illuminate\Routing\Redirector
     */
    public function images(Request $request)
    {
        // Let the image upload only if the item session exists in the database
        $item = Item::where('unique_id', Session::get('unique_id'))->first();
        if(count($item) !== 0) {
            if(!empty($request->all())) {
                $validate = Validator::make($request->all(), [
                    // TODO: image validation
                ]);

                if ($validate->fails()) {
                    return back()->witherrors($validate)->withInput();
                } else {
                    // TODO: storing the image, limit image number per user (set in config)
                    $imageLimit = Config::get('site.image_limit_per_user');
                    return back();
                }
            }
            return view('items.images')->with([
                'item' => $item,
            ]);
        } else {
            return redirect('items');
        }
    }

    /**
     * Finishing the submission, sending an email, displaying a success page
     *
     * @return \Illuminate\View\View
     */
    public function success()
    {
        // Is the item still in the database?
        $item = Item::where('unique_id', Session::get('unique_id'))->first();
        if(count($item) !== 0) {
            $email = $item->email;
            $itemActionsLink = \URL::to('items/edit') . '/' . $item->unique_id;
            Mail::send('emails.item-created-success', ['itemActionsLink' => $itemActionsLink], function ($message) use ($email) {
                $message->from(Config::get('site.success_email_from'), __('Your editing/deleting link for a Lost and Found item'));
                $message->to($email);
            });
            Session::forget('unique_id');
            return view('items.success');
        } else {
            return redirect('items');
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
