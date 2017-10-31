<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

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
     * Store the item
     *
     * @param Request $request Data from the form
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
            $item = new Item;
            $item->title        = $request->title;
            $item->location     = $request->location;
            $item->description  = $request->description;
            $item->email        = $request->email;
            $item->type         = $request->type;
            $item->unique_id    = uniqid();
            $item->save();

            // TODO: send email with deletion link if an email was submitted

            return redirect('items/success');
        }
    }

    /**
     * Display a success page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success()
    {
        return view('items.success');
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
