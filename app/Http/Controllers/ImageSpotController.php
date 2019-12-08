<?php

namespace App\Http\Controllers;

use App\ImageSpot;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageSpotController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'file_name.*' => 'image|mimes:jpg,jpeg,png,bmp',
            'user_id' => 'required',
        ]);

        $images = $this->uploadFiles($request);
        $url = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . 'amazonaws.com/';

        foreach ($images as $imageFile) {
            list($fileName) = $imageFile;

            $spotImage = new ImageSpot;

            $spotImage->file_name = Storage::disk('s3')->url($fileName);
            $spotImage->user_id = $request->user_id;
            $spotImage->spot_id = $request->spot_id;

            $spotImage->save();
        }

        return response()->json();
    }

    protected function uploadFiles($request)
    {
        $uploadedImages = [];

        if ($request->hasFile('file_name')) {
            $images = $request->file('file_name');

            foreach ($images as $image) {
                $uploadedImages[] = $this->uploadFile($image);
            }
        }

        return $uploadedImages;
    }

    protected function uploadFile($image)
    {

        $originalFileName = $image->getClientOriginalName();
        $extension = $image->getClientOriginalExtension();
        $fileNameOnly = pathinfo($originalFileName, PATHINFO_FILENAME);
        $fileName = Str::slug($fileNameOnly) . "-" . time() . "." . $extension;

        Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');

        $uploadFileName = $fileName;

        return [$uploadFileName];
    }

    public function getImageBySpot($spot_id)
    {
        $image = ImageSpot::where('spot_id', $spot_id)->get();

        return response()
            ->json($image);
    }
}
