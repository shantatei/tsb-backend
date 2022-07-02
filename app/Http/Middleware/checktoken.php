<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\account;

class checktoken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $token = $request->header('token');
        // If no token is provided
        if ($token == null || $token ==""){
            return response()->json([ 'message' => 'Please provide a Token']);
        }
         // Fetching the first account with that token
        $account = account::where('token',  $token)->first();

        // If there is such an account with that token, proceed to pass on request
        if ($account) {
            return $next($request);
        } else { // No account with that specified token
            return response()->json(['401 - Unauthorised']);
        }
    }

}
