<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\account;
use Illuminate\Support\Str;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function accounts()
    {
        return account::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $account = account::find($request->input('email'));

        if ($account) {
            return response::json(['error' => 'An account with this email already exist!']);
        }

        $account = new account;

        $account->name = $request->input('name');
        $account->username = $request->input('username');
        $account->email = $request->input('email');
        $account->password = $request->input('password');

        $account->token = "";

        $account->save();

        return $account;
    }

     //Login Function
     public function login(Request $request)
     {
         // Fetch user based on email
         $account = account::where('email', $request->input('email'))->first();

         // If the email can be found
         if ($account != null) {
             // If the given password matches with the one in database
             if ($request->password == $account->password) {

                 //use Illuminate\Support\Str; before you use this
                 //Generate a random 60 chars String for the token
                 $account->token = Str::random(60);
                 $account->save();

                 return response::json([
                     'token' => $account->token,
                 ]);
             } else { // Given password does not match with the one in database
                 return response::json(['message' => 'Invalid Password']);
             }
         } else { // The account cannot be found
             return response::json(['message' => 'No Account has been registered with this email']);
         }
     }

     //Edit Account Function
     public function edit(Request $request)
     {

         $account = account::find($request->id);

         $account->username = $request->input('username');
         $account->email = $request->input('email');
         $account->password = $request->input('password');

         if ($account->save()) {
             return $account;
         }
     }

     //Delete Account Function
     public function deleteAcc(Request $request)
     {
         $account =account::find($request->id);

         if ($account->delete()) {
             return "account has been deleted";
         }
     }

}
