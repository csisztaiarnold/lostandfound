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
            $images = Image::where('item_id', $item->id)->get()->all();
            if(!empty($request->all())) {
                $validate = Validator::make($request->all(), [
                    'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:12048',
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

                        $imageUpload = ImageManager::make($image->getRealPath());
                        $imageUpload->save(public_path($dirPath.'/'.$filenameUniqueId.'.'.$extension));
                        // TODO: keep the original file too.
                        $imageUpload->resize(400, 400, function($constraint) {
                            $constraint->aspectRatio();
                        });
                        $imageUpload->save(public_path($dirPath.'/'.$filenameUniqueId.'_thumb.'.$extension));

                        $image = new Image;
                        $image->item_id     = $item->id;
                        $image->filename    = $filenameUniqueId;
                        $image->extension   = $extension;
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
}
