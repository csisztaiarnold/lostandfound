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
        if(Session::get('unique_id') !== null) {
            // Let the image upload only if the item session exists in the database
            $item = Item::where('unique_id', Session::get('unique_id'))->first();
            $imageLimitReached = Image::imageLimitReached($item->id);
            if (count($item) !== 0) {
                $imageLimitReached = Image::imageLimitReached($item->id);
                $images = Image::where('item_id', $item->id)->orderBy('image_order', 'asc')->get()->all();
                if (!empty($request->all())) {
                    $validate = Validator::make($request->all(), [
                        'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:3000',
                    ]);

                    if ($validate->fails()) {
                        return back()->witherrors($validate)->withInput();
                    } else {
                        if ($imageLimitReached === false) {
                            $dirPath = './item_images/' . $item->id;
                            if (!is_dir($dirPath)) {
                                mkdir($dirPath);
                            }
                            $image = $request->file('image');
                            $extension = $image->getClientOriginalExtension();

                            // TODO: Maybe a check against the database if the filename already exits. Low priority.
                            $filenameUniqueId = uniqid();
                            $currentMax = Image::where('item_id', $item->id)->max('image_order');
                            $newMax = 0;
                            if (Image::where('item_id', $item->id)->count() >= 1) {
                                $newMax = $currentMax + 1;
                            }
                            $imageUpload = ImageManager::make($image->getRealPath());
                            $imageUpload->save(public_path($dirPath . '/' . $filenameUniqueId . '.' . $extension));
                            $imageUpload->fit(500, 500);
                            $imageUpload->save(public_path($dirPath . '/' . $filenameUniqueId . '_medium.' . $extension));
                            $imageUpload->fit(200, 200);
                            $imageUpload->save(public_path($dirPath . '/' . $filenameUniqueId . '_thumb.' . $extension));

                            $image = new Image;
                            $image->item_id = $item->id;
                            $image->filename = $filenameUniqueId;
                            $image->extension = $extension;
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
        } else {
            return redirect('/');
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
        if(Session::get('unique_id') !== null) {
            try {
                if (is_array($request->imageOrderArray)){
                    foreach ($request->imageOrderArray as $key => $value) {
                        Image::where('id', $value)->update([
                            'image_order' => $key
                        ]);
                    }
                    $response_array['status'] = 'success';
                } else {
                    $response_array['status'] = 'error';
                    $response_array['message'] = __('Request is not an array.');
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $response_array['status'] = 'error';
                $response_array['message'] = __('Database error.');
            }
        } else {
            $response_array['status'] = 'error';
            $response_array['message'] = __('Your session has expired.');
        }
        header('Content-type: application/json');
        echo json_encode($response_array);
    }

     /**
     * Delete an image
     *
     * @param integer $id Image ID
     * @return \Illuminate\Routing\Redirector
     */
    public function delete($id)
    {
        // Does the image belongs to the item?
        $image = Image::where('id', $id)->first();
        if(count($image) > 0) {
            $item = $image->item()->first();
            if($item->unique_id === Session::get('unique_id')) {
                // Delete from the database first
                Image::where('id', $id)->delete();
                // Delete from the server as well
                $mainImage = './item_images/'.$item->id.'/'.$image->filename.'.'.$image->extension;
                if(file_exists($mainImage)) {
                    unlink($mainImage);
                }
                $mediumImage = './item_images/'.$item->id.'/'.$image->filename.'.'.$image->extension;
                if(file_exists($mediumImage)) {
                    unlink($mediumImage);
                }
                $thumbImage = './item_images/'.$item->id.'/'.$image->filename.'_thumb.'.$image->extension;
                if(file_exists($thumbImage)) {
                    unlink($thumbImage);
                }
                return redirect('images/upload')->with('success', __('The image was successfully deleted!'));
            } else {
                return redirect('images/upload')->with('error', __('You don\'t have access to this image!'));
            }
        } else{
            return redirect('images/upload')->with('error', __('This image doesn\'t exists!'));
        }
    }
}
