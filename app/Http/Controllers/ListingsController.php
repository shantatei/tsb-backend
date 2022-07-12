<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ListingsController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['listings']]);
        $this->user = $this->guard()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Get User Listings
    public function index()
    {
        $listings = $this->user->listings()->get(['id','itemname','price', 'quantity', 'description', 'user_id']);
        return response()->json($listings->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    //Add new Listing
    public function store(Request $request)
    {
        // $listing = new listing();

        // $listing->user_id = $request->input('user_id');
        // $listing->price = $request->input('price');
        // $listing->quantity = $request->input('quantity');
        // $listing->description = $request->input('description');


        // $listing->save();

        // return $listing;

        $validator = Validator::make($request->all(), [
            'itemname' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $listing = new Listing();
        $listing->itemname = $request->itemname;
        $listing->price = $request->price;
        $listing->quantity = $request->quantity;
        $listing->description = $request->description;

        if ($this->user->listings()->save($listing)) {
            return response()->json(
                [
                    'status' => true,
                    'listing' => $listing
                ]
            );
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Oops, the listing could not be saved'
            ]);
        }
    }

    // public function checkListings(Request $request)
    // {
    //     $listing = Listing::where('user_id', $request->input('user_id'))->get();
    //     return $listing;
    // }

    //Update Listing
    public function update(Request $request, Listing $listing)
    {

        // $listing = listing::where('user_id', $request->input('user_id'));
        // $listing = listing::find($request->id);
        // $listing->price = $request->input('price');
        // $listing->quantity = $request->input('quantity');
        // $listing->description = $request->input('description');

        // if ($listing->save()) {
        //     return $listing;
        // }
        $validator = Validator::make($request->all(), [
            'itemname' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $listing->itemname = $request->itemname;
        $listing->price = $request->price;
        $listing->quantity = $request->quantity;
        $listing->description = $request->description;

        if ($this->user->listings()->save($listing)) {
            return response()->json(
                [
                    'status' => true,
                    'listing' => $listing
                ]
            );
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Oops, the listing could not be updated'
            ]);
        }
    }


    //Delete Listing
    public function destroy(Listing $listing)
    {
        // $listing = Listing::find($request->id);

        // if ($listing->delete()) {
        //     return $listing;
        // }

        if ($listing->delete()) {
            return response()->json(
                [
                    'status' => true,
                    'listing' => $listing
                ]
            );
        }
        else {
            return response()->json([
                'status' => false,
                'message' => 'Oops, the listing could not be deleted'
            ]);
        }
    }

    public function listings()
    {
        return Listing::all();
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
