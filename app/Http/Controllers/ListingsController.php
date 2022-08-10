<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\ProductLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use File;

class ListingsController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['listings', 'list', 'getListingById']]);
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
            'image' => 'required|image|mimes:jpg,png,bmp,jpeg',
            'itemname' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 400);
        }

        $image_name = time() . '.' . $request->image->extension();
        $request->image->move(public_path('/storage/products_images'), $image_name);

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
                'message' => 'Oops, the listing could not be saved',
            ]);
        }
    }

    //search,sort and pagination
    public function list(Request $request)
    {
        $listing_query = Listing::with(['user']);

        //sort by keyboard(search)
        if ($request->keyword) {
            $listing_query->where('itemname', 'LIKE', '%' . $request->keyword . '%');
        }

        //sort by id
        if ($request->sortBy && in_array($request->sortBy, ['id', 'created_at'])) {
            $sortBy = $request->sortBy;
        } else {
            $sortBy = 'id';
        }

        //sort by asc/desc time
        if ($request->sortOrder && in_array($request->sortOrder, ['asc', 'desc'])) {
            $sortOrder = $request->sortOrder;
        } else {
            $sortOrder = 'desc';
        }

        //pagination per page (default is 5)

        if ($request->perPage) {
            $perPage = $request->perPage;
        } else {
            $perPage = 5;
        }

        //pagination
        if ($request->paginate) {

            $listings = $listing_query->orderBY($sortBy, $sortOrder)->paginate($perPage);
        } else {
            $listings = $listing_query->orderBY($sortBy, $sortOrder)->get();
        }

        return response()->json([
            'message' => 'Listing successfully fetched',
            'data' => $listings
        ]);
    }

    //Update Listing
    public function updateListing($id, Request $request)
    {

        $listing = Listing::with(['user'])->where('id', $id)->first();


        //if the id of the listing is found
        if ($listing) {

            if ($listing->user_id == $this->user->id) {

                //request inputs
                $validator = Validator::make($request->all(), [
                    'image' => 'nullable|image|mimes:jpg,png,bmp,jpeg',
                    'itemname' => 'required|string',
                    'price' => 'required|numeric',
                    'quantity' => 'required|integer',
                    'description' => 'required|string',
                ]);

                //validate inputs
                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'Validation errors',
                        'errors' => $validator->errors()
                    ], 400);
                }
                //check if request has File
                if ($request->hasFile('image')) {
                    $image_name = time() . '.' . $request->image->extension();
                    $request->image->move(public_path('/storage/products_images'), $image_name);
                    $old_path = public_path() . '/storage/products_images/' . $listing->image;

                    if (File::exists($old_path)) {
                        File::delete($old_path);
                    }
                } else {
                    $image_name = $listing->image;
                }

                $listing->update([
                    'image' => $image_name,
                    'itemname' => $request->itemname,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'description' => $request->description
                ]);

                return response()->json([
                    'message' => 'Listing successfully updated',
                    'data' => $listing
                ]);
            } else {
                return response()->json([
                    'message' => 'Access denied',
                ]);
            }
        } else {
            return response()->json([
                'message' => 'No Listing Found',
            ], 403);
        }
    }


    //Delete Listing
    public function deleteListing($id, Request $request)
    {

        $listing = Listing::where('id', $id)->first();

        if ($listing) {

            if ($listing->user_id == $this->user->id) {

                $old_path = public_path() . '/storage/products_images/' . $listing->image;

                if (File::exists($old_path)) {
                    File::delete($old_path);
                }

                $listing->delete();


                return response()->json([
                    'message' => 'Listing successfully deleted',
                    'data' => $listing
                ]);
            } else {
                return response()->json([
                    'message' => 'Access denied',
                ]);
            }
        } else {
            return response()->json([
                'message' => 'No Listing Found',
            ], 403);
        }
    }

    public function listings()
    {

        $listing_query = Listing::withCount(['likes'])->with(['user']);
        $listings = $listing_query->get();



        return $listings;
    }

    public function getListingById($id)
    {

        $listing = Listing::with(['user'])->where('id', $id)->first();

        if ($listing) {
            return $listing;
        } else {
            return response()->json([
                'message' => 'No Listing Found',
            ], 403);
        }
    }

    public function toggle_like($id, Request $request)
    {
        $listing = Listing::where('id', $id)->first();

        if ($listing) {

            $user = $this->user;
            $product_like = ProductLike::where('listing_id', $listing->id)
                ->where('user_id', $user->id)->first();
            if ($product_like) {
                $product_like->delete();

                return response()->json([
                    'message' => 'Like successfully removed',
                ], 200);

            } else {

                ProductLike::create([
                    'listing_id' => $listing->id,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'message' => 'Product successfully liked',
                ], 200);


            }
        } else {
            return response()->json([
                'message' => 'No Listing Found',
            ], 400);
        }
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
