<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionController;
use App\Http\Middleware\EnsureTokenIsValid;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});

Route::prefix('user')->group(function (){

    Route::post('/signup',[UserController::class, 'User_sign_up']);
    Route::post('/login',[UserController::class, 'User_log_in']);
    Route::post('/password/reset',[UserController::class, 'Password_reset']);    
});

Route::prefix('card')->group(function (){

    Route::post('/create',[CardController::class, 'Create_card'])->middleware('check.admin');
    Route::post('/update/{id}',[CardController::class, 'Card_update'])->middleware('check.admin');;
    Route::post('/list',[CardController::class, 'Card_list']);    
    Route::get('/get/card/{id}',[CardController::class, 'get_card_by_ID']);    
    Route::post('/sell',[CardController::class, 'Sell_card'])->middleware('check.user');
    Route::post('/list/sale',[CardController::class, 'Cards_in_sale']);
        
});

Route::prefix('collection')->group(function (){

    Route::post('/create',[CollectionController::class, 'Create_collection'])->middleware('check.admin');
    Route::post('/update/{id}',[CollectionController::class, 'Collection_update'])->middleware('check.admin');
    Route::post('/add/card',[CollectionController::class, 'Add_card_to_collection'])->middleware('check.admin');
        
});