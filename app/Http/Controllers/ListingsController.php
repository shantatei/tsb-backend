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
        $this->middleware('auth:api', ['except' => ['listings','list']]);
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
                'status' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

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
                'message' => 'Oops, the listing could not be saved',
            ]);
        }
    }

    //search,sort and pagination
    public function list(Request $request){
        $listing_query = Listing::with(['user']);

        //sort by keyboard(search)
        if($request->keyword){
            $listing_query->where('itemname','LIKE','%'.$request->keyword.'%');
        }

        //sort by id
        if($request->sortBy && in_array($request->sortBy,['id','created_at'])){
            $sortBy=$request->sortBy;
        }else{
            $sortBy='id';
        }

        //sort by asc/desc time
        if($request->sortOrder && in_array($request->sortOrder,['asc','desc'])){
            $sortOrder=$request->sortOrder;
        }else{
            $sortOrder='desc';
        }

        //pagination per page (default is 5)

        if($request->perPage){
          $perPage=$request->perPage;
        }else{
            $perPage=5;
        }

        //pagination
        if($request->paginate){

            $listings = $listing_query->orderBY($sortBy,$sortOrder)->paginate($perPage);
        }else{
            $listings = $listing_query->orderBY($sortBy,$sortOrder)->get();
        }

        return response()->json([
            'message' => 'Listing successfully fetched',
            'data'=>$listings
        ]);
    }

    //Update Listing
    public function update(Request $request, Listing $listing)
    {

        $validator = Validator::make($request->all(), [
            'img_path' => 'nullable',
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

        $listing_query = Listing::with(['user']);
        $listings = $listing_query->get();

        return $listings;
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
