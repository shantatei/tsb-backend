<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['login','register','users']]);
    }

    //GET ALL USERS
    public function users()
    {
        return User::all();
    }

    //LOGIN FUNCTION
    public function login(Request $request){

        $validator = Validator::make($request->all(),[
            'email'=> 'required|email',
            'password'=>'required|string|min:6'
        ]);

        if($validator ->fails()){
            return response()->json($validator->errors(),400);
        }

        $token_validity = 24*60;

        $this->guard()->factory()->setTTL($token_validity);
        if(!$token = $this->guard()->attempt($validator->validated())){
            return response()->json(['error'=>'Unauthorized, Invalid Email or Password'],401);
        }

        return $this->respondWithToken($token);
    }

    //REGISTER FUNCTION
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required|string|between:2,100',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed|min:6'
        ]);

        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],422);
        }

        $user = User::create(array_merge(
          $validator->validated(),
          ['password'=>bcrypt($request->password)]
        ));

        return response()->json([
            'message'=>'User created successfully',
            'user'=>$user
        ]);
    }

    //LOGOUT FUNCTION
    public function logout(){

        $this->guard()->logout();

        return response()->json([
            'message'=>'User logged out successfully'
        ]);
    }

    //GET USER PROFILE
    public function profile(){

        return response()->json($this->guard()->user());
    }

    //REFRESH TOKEN
    public function refresh(){

        return $this->respondWithToken($this->guard()->refresh());
    }

    //Edit Account
    public function editUser(Request $request){

        $currentuser = $this->guard()->user();

        $validator = Validator::make($request->all(),[
            'name'=>'required|string|between:2,100',
            'email'=>'required|email|unique:users',
        ]);

        if($validator->fails()){
            return response()->json([
                $validator->errors()
            ],422);
        }

        $currentuser->update($validator->validated());

        return response()->json([
            'message'=>'User Account Updated successfully',
            'user'=> $currentuser
        ]);

    }

    //Delete Account
    public function deleteUser(){

        $currentuser = $this->guard()->user();
        $this->guard()->user()->delete();

        return response()->json([
            'message'=>'User Account Deleted successfully',
            'user'=> $currentuser
        ]);
    }

    //JWT RESPOND WITH TOKEN
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' =>$token,
            'token_type'=>'bearer',
            'token_validity'=>$this->guard()->factory()->getTTL() *60,
            'user' => auth()->user()
        ]);
    }

    protected function guard(){
        return Auth::guard();
    }
}
