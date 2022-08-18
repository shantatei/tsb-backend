<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = $this->guard()->user();
    }

    //GET ALL USERS
    public function users()
    {
        return User::all();
    }

    public function createRole(Request $request)
    {

        $role = new Role();
        $role->role_name = $request->input('role_name'); // name of the new role
        $role->role_description = $request->input('role_descripition'); // name of the new role
        $role->save();

        return response()->json([
            'message' => 'Role successfully created',
            'role' => $role
        ]);
    }

    public function assignRole(Request $request)
    {
        // responsible for assigning a given role to a user.
        // It needs a role ID and a user object
        $user = User::whereEmail($request->input('email'))->first();
        $role = Role::where('role_name', $request->input('role'))->first();
        $user->roles()->attach($role->id);

        return response()->json([
            'message' => "Role successfully assigned",
            'role' => $role
        ]);
    }

    protected function guard()
    {
        return Auth::guard();
    }

}
