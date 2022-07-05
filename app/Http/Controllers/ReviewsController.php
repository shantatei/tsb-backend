<?php

namespace App\Http\Controllers;

use App\Models\reviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = $this->guard()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = $this->user->reviews()->get(['username','review','created_by']);
        return response()->json($reviews->toArray());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username'=>'required|string',
            'review'=>'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'error'=>$validator->errors()
            ],400);
        }

        $review = new reviews();
        $review->username= $request->username;
        $review->review= $request->review;

        if($this->user->reviews()->save($review)){
            return response()->json([
                'status'=>true,
                'review'=>$review
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'Oops, the review could not be saved'
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(reviews $review)
    {
        return $review;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, reviews $review)
    {
        $validator = Validator::make($request->all(),[
            'username'=>'required|string',
            'review'=>'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'error'=>$validator->errors()
            ],400);
        }

        $review->username= $request->username;
        $review->review= $request->review;

        if($this->user->reviews()->save($review)){
            return response()->json([
                'status'=>true,
                'review'=>$review
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'Oops, the review could not be updated'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(reviews $review)
    {
        if($review ->delete()){
            return response()->json([
                'status'=>true,
                'review'=>$review
            ]);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'Oops, the review could not be deleted'
            ]);
        }
    }

    protected function guard(){
        return Auth::guard();
    }
}
