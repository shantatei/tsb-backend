<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

    public function roles()
    {
        return Role::all();
    }

    //GET ALL USERS
    public function users()
    {
        return User::with('roles')->get();
    }

    public function createRole(Request $request)
    {

        $role = new Role();
        $role->role_name = $request->input('role_name'); // name of the new role
        $role->role_description = $request->input('role_description'); // name of the new role
        $role->save();

        return response()->json([
            'message' => 'Role successfully created',
            'role' => $role
        ]);
    }
    public function deleteRole($id)
    {
        $role = Role::where('id', $id)->first();
        if ($role) {
            $role->delete();

            return response()->json([
                'message' => 'Role successfully deleted',
                'data' => $role
            ]);
        } else {
            return response()->json([
                'message' => 'No Role Found',
            ], 403);
        }
    }

    public function assignRole(Request $request)
    {
        // responsible for assigning a given role to a user.
        // It needs a role ID and a user object
        $user = User::whereEmail($request->input('email'))->first();
        $role = Role::where('role_name', $request->input('role'))->first();


        if ($user->roles()->where('id', $role->id)->exists()) {
            return response()->json([
                'message' => "Role has already been assigned to this user",
            ]);
        } else {
            $user->roles()->attach($role->id);
            return response()->json([
                'message' => "Role successfully assigned",
                'role' => $role,
                'user' => $user
            ]);
        }
    }

    public function detachRole(Request $request)
    {
        // responsible for assigning a given role to a user.
        // It needs a role ID and a user object
        $user = User::whereEmail($request->input('email'))->first();
        $role = Role::where('role_name', $request->input('role'))->first();
        $user->roles()->detach($role->id);

        return response()->json([
            'message' => "Role successfully detached",
            'role' => $role
        ]);
    }

    public function orders()
    {
        return Order::all();

    }

    protected function guard()
    {
        return Auth::guard();
    }
}
