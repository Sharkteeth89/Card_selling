<?php

use Illuminate\Support\Facades\Route;

Route::get('/not-authorized', function () {
    return "You are not authorized";
});

Route::get('/', function () {
    return view('login');
});

Route::get('login', 'LoginController@index');
Route::get('login/{provider}', 'LoginController@redirectToProvider');
Route::get('{provider}/callback', 'LoginController@handleProviderCallback');
Route::get('/home', function () {
    return 'User is logged in';
});
