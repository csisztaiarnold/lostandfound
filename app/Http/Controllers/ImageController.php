<?php

namespace App\Http\Controllers;

use App\Item;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as ImageManager;

class ImageController extends Controller
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
     * Upload images
     *
     * @param Request $request Form data
     * @return \Illuminate\View\View|\Illuminate\Routing\Redirector
     */
    public function upload(Request $request)
    {
        // Let the image upload only if the item session exists in the database
        $item = Item::where('unique_id', Session::get('unique_id'))->first();
        $imageLimitReached = Image::imageLimitReached($item->id);
        if(count($item) !== 0) {
            $imageLimitReached = Image::imageLimitReached($item->id);
            $images = Image::where('item_id', $item->id)->orderBy('image_order','asc')->get()->all();
            if(!empty($request->all())) {
                $validate = Validator::make($request->all(), [
                    'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:3000',
                ]);

                if ($validate->fails()) {
                    return back()->witherrors($validate)->withInput();
                } else {
                    if($imageLimitReached === false) {
                        $dirPath = './item_images/'.$item->id;
                        if(!is_dir($dirPath)) {
                            mkdir($dirPath);
                        }
                        $image      = $request->file('image');
                        $extension  = $image->getClientOriginalExtension();

                        // TODO: Maybe a check against the database if the filename already exits. Low priority.
                        $filenameUniqueId  = uniqid();
                        $currentMax = Image::where('item_id', $item->id)->max('image_order');
                        $newMax = 0;
                        if(Image::where('item_id', $item->id)->count() >= 1) {
                            $newMax = $currentMax+1;
                        }
                        $imageUpload = ImageManager::make($image->getRealPath());
                        $imageUpload->save(public_path($dirPath.'/'.$filenameUniqueId.'.'.$extension));
                        $imageUpload->fit(200, 200);
                        $imageUpload->save(public_path($dirPath.'/'.$filenameUniqueId.'_thumb.'.$extension));

                        $image = new Image;
                        $image->item_id     = $item->id;
                        $image->filename    = $filenameUniqueId;
                        $image->extension   = $extension;
                        $image->image_order = $newMax;
                        $image->save();

                        return back()->with('success', __('The image has been uploaded successfully'));
                    } else {
                        // Actually, this error message shouldn't be displayed,
                        // as the upload form won't be visible after the image limit is reached.
                        // The user is forced to delete an image before continuing
                        return back()->with('error', __('Sorry, the image limit for this item is reached!'));
                    }

                }
            }
            return view('images.upload')->with([
                'item' => $item,
                'image_limit_reached' => $imageLimitReached,
                'images' => $images,
            ]);
        } else {
            return redirect('items');
        }
    }


    /**
     * Reorder images and return a JSON status message
     *
     * @param Request $request Form data
     * @return string JSON
     */
    public function reorder(Request $request)
    {
        try {
            foreach($request->imageOrderArray as $key => $value){
                Image::where('id', $value)->update([
                    'image_order' => $key
                ]);
            }
            $response_array['status'] = 'success';
        } catch(\Illuminate\Database\QueryException $ex){
            $response_array['status'] = 'error';
        }
        header('Content-type: application/json');
        echo json_encode($response_array);
    }
}
