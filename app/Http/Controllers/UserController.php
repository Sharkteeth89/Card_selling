<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use \Firebase\JWT\JWT;
use App\Http\Helpers\MyJWT;

class UserController extends Controller
{
    public function User_sign_up(Request $request){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            if (isset($data->username) && isset($data->email) && isset($data->password) && isset($data->role)) {
                if (!empty($data->username) && !empty($data->email) && !empty($data->password) && !empty($data->role)) {
                    $response="";

                    $searchForUsername = User::where('username', $data->username)->get()->first();
                    $searchForEmail = User::where('email', $data->email)->get()->first();

                    if (!$searchForEmail && !$searchForUsername) {
                        $user = new User();

                        $user->username = $data->username;
                        $user->email = $data->email;
                        $user->password = Hash::make($data->password);            

                        if ($data->role != 'admin') {
                            $user->role = $data->role;

                            try{
                                $user->save();
                                $response = "User Created now you need to log in";
                
                            }catch(\Exception $e){
                                $response = $e->getMessage();
                            }
                        }else{
                            $response = "No valid role";
                        }
                    }else{
                        $response = "User already exists";
                    }                    
                }else{
                    $response = "Empty data";
                }
            }else{
                $response = "No valid data";
            } 
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }
    public function User_log_in(Request $request){

        $data = $request->getContent();
        $data = json_decode($data);

        if ($data) {

            if (isset($data->username) && isset($data->password)) {
                if (!empty($data->username) && !empty($data->password)) {
                    $response="";

                    $user_username = $data->username;
                    $user =  User::where('username',$user_username)->get()->first();
            
                    if ($user){

                        $payload = MyJWT::generatePayload($user);
                        $key = MyJWT::getKey();

                        $jwt = JWT::encode($payload, $key);

                        if (Hash::check($data->password, $user->password)) {
                            $response = $jwt;                
                        }else{
                            $response = "user or password incorrect";
                        }
                    }else{
                        $response = "User not found";
                    }
                }else{
                    $response = "Empty data";
                }
            }else{
                $response = "No valid data";
            }                       
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }
    public function Password_reset(Request $request){

        $data = $request->getContent();
        $data = json_decode($data);

        if ($data) {
            if (isset($data->email)) {
                if (!empty($data->email)) {
                    $response="";
                    $user =  User::where('email',$data->email)->get()->first();
            
                    if ($user){
                        $new_password = Str::random(10);                
                        $user->password = Hash::make($new_password);
                        try{
                            $user->save();
                            $response = "Your new password is: " . $new_password;    
                        }catch(\Exception $e){
                            $response = $e->getMessage();
                        }
                    }else{
                        $response = "Email not valid";
                    }
                }else{
                    $response = "Empty data";
                }
            }else{
                $response = "No valid data";
            }                       
        }else{
            $response = "No valid data";
        }
        return response()->json($response);
    }
}
