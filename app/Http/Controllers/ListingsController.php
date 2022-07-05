<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\listing;

class ListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listings()
    {
        return listing::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addListing(Request $request)
    {
        $listing = new listing();

        $listing->user_id = $request->input('user_id');
        $listing->price = $request->input('price');
        $listing->quantity = $request->input('quantity');
        $listing->description = $request->input('description');


        $listing->save();

        return $listing;
    }

    public function checkListings(Request $request){
        $listing = listing::where('user_id', $request->input('user_id'))->get();
        return $listing;
    }

    public function updateListing(Request $request){

        $listing = listing::where('user_id', $request->input('user_id'));
        $listing = listing::find($request->id);
        $listing->price = $request->input('price');
        $listing->quantity = $request->input('quantity');
        $listing->description = $request->input('description');

        if ($listing->save()) {
            return $listing;
        }

    }

    public function deleteListing(Request $request){
        $listing =listing::find($request->id);

        if ($listing->delete()) {
            return $listing;
        }
    }


}
