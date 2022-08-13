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
        $this->middleware('auth:api', ['except' => ['getReview', 'getReviewById']]);
        $this->user = $this->guard()->user();
    }

    public function getReviewById($id)
    {
        $review = reviews::with(['user'])->where('reviewed_id', $id)->get();

        if ($review) {
            return $review;
        } else {
            return response()->json([
                'message' => 'No Reviews Found ',
            ], 403);
        }
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

            // $checkreview = reviews::where('reviewed_id', $id)
            //     ->where('user_id', $user->id)->first();

            // if ($checkreview) {
            //     return response()->json([
            //         'message' => 'You have already reviewed this user',
            //     ], 200);
            // } else {
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
            $review->reviewed_id = $id;

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

    public function updateReview($id, Request $request)
    {
        $review  = reviews::with(['user'])->where('id', $id)->first();

        if ($review) {

            if ($review->user_id == $this->user->id) {
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

                $review->update([
                    'review' => $request->review,
                    'rating' => $request->rating,

                ]);

                return response()->json([
                    'message' => 'Review successfully updated',
                    'data' => $review
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

    public function deleteReview($id)
    {
        $review = reviews::where('id', $id)->first();

        if ($review) {
            if ($review->user_id == $this->user->id) {
                $review->delete();
                return response()->json([
                    'message' => 'Review Deleted'
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
