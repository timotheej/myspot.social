<?php

namespace App\Http\Controllers;

use App\Spot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpotController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'spot_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'uid' => 'required'
        ]);

        $spot = new Spot;

        $spot->spot_id = $request->spot_id;
        $spot->lat = $request->lat;
        $spot->long = $request->long;
        $spot->user_id = $request->uid;

        $spot->save();
    }

    public function fetchSpot($spot_id)
    {
        $spot = Spot::where('spot_id', $spot_id)->with('user', 'categorie')->get();
        return response()
            ->json($spot);
    }

    public function updateSpot(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:80',
            'description' => 'required|string|max:300',
            'categorie_id' => 'required'
        ]);

        $spot = Spot::findOrFail($id);
        $spot->name = $request->name;
        $spot->description = $request->description;
        $spot->categorie_id = $request->categorie_id;
        $spot->save();

        return response()
            ->json($spot);
    }
}
