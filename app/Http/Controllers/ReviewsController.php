<?php

namespace App\Http\Controllers;

use App\Models\reviews;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getReview']]);
        $this->user = $this->guard()->user();
    }

    public function index()
    {
        $reviews = $this->user->reviews()->get(['username', 'review', 'created_by']);
        return response()->json($reviews->toArray());
    }

    public function getReview()
    {
        return  reviews::all();
    }

    public function postReview($id, Request $request)
    {

        $usercheck = User::where('id', $id)->first();

        if ($usercheck) {
            $user = $this->user;

            $validator = Validator::make($request->all(), [
                'review' => 'required|string',
                'rating' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()
                ], 400);
            }

            $review = new reviews();
            $review->review = $request->review;
            $review->rating = $request->rating;
            $review->reviewedby_id = $user->id;
            $review->user_id = $id;

            if ($this->user->reviews()->save($review)) {
                return response()->json([
                    'status' => true,
                    'review' => $review
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Oops, the review could not be saved'
                ]);
            }
        } else {
            return response()->json([
                'message' => 'No User is matched to this id',
            ], 403);
        }
    }

    public function update(Request $request, reviews $review)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'review' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ], 400);
        }

        $review->username = $request->username;
        $review->review = $request->review;

        if ($this->user->reviews()->save($review)) {
            return response()->json([
                'status' => true,
                'review' => $review
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Oops, the review could not be updated'
            ]);
        }
    }

    public function deleteReview($id)
    {
        $review = reviews::where('id', $id)->first();

        if ($review) {
            if ($review->user_id == $this->user->id) {
                $review->delete();
                return response()->json([
                    'status' => true,
                    'review' => $review
                ]);
            } else {
                return response()->json([
                    'message' => 'Access denied',
                ]);
            }
        } else {
            return response()->json([
                'message' => 'No Review Found',
            ], 403);
        }
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
