<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use \Firebase\JWT\JWT;
use App\Http\Helpers\JWTtoken;

class EnsureUserIsNotAdmin
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
        $key = JWTtoken::getKey();

        $headers = getallheaders();
       
        if(array_key_exists('api_token', $headers)){

            if(!empty($headers['api_token'])){
                $decoded = JWT::decode($headers['api_token'], $key, array('HS256'));
                
                if(isset($decoded->role)){
                    if($decoded->role != "admin" ){
                        return $next($request);
                    }else{
                        abort(403, "User not allowed");
                    }
                }else{
                    abort(403, "No valid token");
                }
            }else{
                abort(403, "Empty token");
            }
        }else{
            abort(403, "No token");
        }
    }
}
