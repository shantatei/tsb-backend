<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use File;

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
        $listings = $this->user->listings()->get(['id', 'image', 'itemname', 'price', 'quantity', 'description', 'user_id']);
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

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,png,bmp',
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

        // if ($request->file('file') == null) {
        //     $filepath = "";
        // } else {
        //     $filepath = $request->file('file')->store('products');
        // }

        $image_name = time() . '.' . $request->image->extension();
        $request->image->move(public_path('/storage/products'), $image_name);

        $listing = new Listing();
        $listing->image = $image_name;
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


    //Update Listing
    public function update(Request $request, Listing $listing)
    {

        $validator = Validator::make($request->all(), [
            'img_path' => 'required|image|mimes:jpg,png,bmp',
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

        $image_name = time() . '.' . $request->img_path->extension();
        $request->img_path->move(public_path('/storage/products'), $image_name);

        $listing->img_path = $image_name;
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
        } else {
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
