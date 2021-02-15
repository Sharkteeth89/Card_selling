<?php

namespace App\Http\Helpers;
use Firebase\JWT\JWT;

class MyJWT{

    private const KEY = 'PvldlWfJym5O59v2nYmpeyU1xKY6fRXkdasda';
    
    public static function generatePayload($user){
        $payload = array(
            'role' => $user->role,
            'id' => $user->id
        );

        return $payload;
    }

    public static function getKey(){
        return self::KEY;
    }

}