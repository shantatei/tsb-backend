<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use File;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'getUserById']]);
    }



    //LOGIN FUNCTION
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = 24 * 60;

        $this->guard()->factory()->setTTL($token_validity);
        if (!$token = $this->guard()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized, Invalid Email or Password'], 401);
        }

        return $this->respondWithToken($token);
    }

    //REGISTER FUNCTION
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'profile_photo' => 'image|mimes:jpg,png,bmp,jpeg',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                $validator->errors()
            ], 422);
        }

        //check if request has File
        if ($request->hasFile('profile_photo')) {
            $image_name = time() . '.' . $request->profile_photo->extension();
            $request->profile_photo->move(public_path('/storage/profile_images'), $image_name);
            $user = User::create(array_merge(
                $validator->validated(),
                ['profile_photo' => $image_name],
                ['password' => bcrypt($request->password)]
            ));
            $role = Role::where('role_name', 'Customer')->first();
            $user->roles()->attach($role->id);
        } else {
            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            ));
            $role = Role::where('role_name', 'Customer')->first();
            $user->roles()->attach($role->id);
        }


        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    //LOGOUT FUNCTION
    public function logout()
    {

        $this->guard()->logout();

        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }

    //GET USER PROFILE
    public function profile()
    {
        $user =  $this->guard()->user();
        $user->roles;
        return response()->json($user);
    }

    //REFRESH TOKEN
    public function refresh()
    {

        return $this->respondWithToken($this->guard()->refresh());
    }

    //Edit Account
    public function editUser(Request $request)
    {

        $currentuser = $this->guard()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'profile_photo' => 'image|mimes:jpg,png,bmp,jpeg',
            'email' => 'required|email|',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'error' => $validator->errors()
            ], 400);
        }

        //check if request has File
        if ($request->hasFile('profile_photo')) {
            if ($currentuser->profile_photo) {
                $old_path = public_path() . '/storage/profile_images/' . $currentuser->profile_photo;
                if (File::exists($old_path)) {
                    File::delete($old_path);
                }
            }
            $image_name = time() . '.' . $request->profile_photo->extension();
            $request->profile_photo->move(public_path('/storage/profile_images'), $image_name);
        } else {
            $image_name = $currentuser->profile_photo;
        }

        $currentuser->update([
            'profile_photo' => $image_name,
            'name' => $request->name,
            'email' => $request->email,
        ]);


        $currentuser->roles;

        return response()->json([
            'message' => 'User Account Updated successfully',
            'user' => $currentuser
        ]);
    }

    //Delete Account
    public function deleteUser(Request $request)
    {

        $user = $this->guard()->user();

        $validator = Validator::make($request->all(), [
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'error' => $validator->errors()
            ], 400);
        }

        if (Hash::check($request->password, $user->password)) {

            $user->delete();
            return response()->json([
                'message' => 'User Account Deleted successfully',
                'user' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'Current password does not match',
            ], 400);
        }
    }



    //JWT RESPOND WITH TOKEN
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => $this->guard()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function getUserById($id)
    {
        $user = User::where('id', $id)->get();

        if ($user) {
            return $user;
        } else {
            return response()->json([
                'message' => 'No User is matched to this ID ',
            ], 403);
        }
    }

    public function change_password(Request $request)
    {

        $user =  $this->guard()->user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'error' => $validator->errors()
            ], 400);
        }

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => bcrypt($request->password)
            ]);
            return response()->json([
                'message' => 'Password successfully updated',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Old password does not match',
            ], 400);
        }
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
