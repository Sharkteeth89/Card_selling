<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function User_sign_up(Request $request){

        $data = $request->getContent();

        $data = json_decode($data);

        if ($data) {
            $response="";
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
                $response = "Rol no válido";
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
            $response="";
            $user_username = $data->username;
            $user =  User::where('username',$user_username)->get()->first();
            
            if ($user){
                if (Hash::check($data->password, $user->password)) {
                    $user->user_token = Str::random(30);
                    try{
                        $user->save();
                        $response = "Login successful";    
                    }catch(\Exception $e){
                    $response = $e->getMessage();
                    }                
                }else{
                   $response = "user or password incorrect";
                }
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
            $response = "No valid data";
        }
        return response()->json($response);
    }
    public function Set_admin(Request $request, $id){

        $response="";
        $user = User::find($id);

        if ($user) {
            $user->role = "admin";
            try{
                $user->save();
                $response = "Admin assigned";

            }catch(\Exception $e){
                $response = $e->getMessage();
            } 
        }else{
            $response = "No valid user";
        }           
    
        return response()->json($response);
    }
}
