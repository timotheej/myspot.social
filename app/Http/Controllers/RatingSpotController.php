<?php

namespace App\Http\Controllers;

use App\RatingSpot;
use Illuminate\Http\Request;

class RatingSpotController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'spot_id' => 'required',
            'rating' => 'required'
        ]);

        $rating = new RatingSpot;

        $rating->user_id = $request->user_id;
        $rating->spot_id = $request->spot_id;
        $rating->rating = $request->rating;

        $rating->save();

        return response()
            ->json($rating);
    }

    public function getAllRatesBySpot($spot_id)
    {
        $rates = RatingSpot::where('spot_id', $spot_id)->get();

        return response()
            ->json($rates);
    }
}
