<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;


class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {

            $user =  User::where('user_token',$data->user_token)->get()->first();

            if ($user) {

                if ($user->role === "admin"){
                    return $next($request);
                }else{
                    abort(403, "Not authorized");                   
                }                
            }else{
                abort(403, "User not found or not logged in");
            }
        }else{
            abort(403, "Invalid Data");
        }         
    }
}
