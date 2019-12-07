<?php

namespace App\Http\Controllers;

use App\CategorieSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategorieSpotController extends Controller
{
    public function getAllCategories()
    {

        $cat = CategorieSpot::all();

        Log::info($cat);

        return response()
            ->json($cat);
    }
}
