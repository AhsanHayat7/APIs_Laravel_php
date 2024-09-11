<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// For Practice Apis
// Route::get('/users', function(){
//     return 'Hello Wolrd';
// });

// Route::post('/users', function(){
//     return response()->json('Posts Api hit Successfully');
// });


// Route::delete('/users/{id}', function($id){
//     return response("Put " . $id,200);
// });


// Route::put('/users/{id}', function($id){
//     return response("Delete ". $id,200);
// });

//

Route::get('/test', function() {
    p("Working");
});



Route::post('users/store','App\Http\Controllers\Api\UserController@store');

Route::get('users/get/{flag}',[UserController::class, 'index']);
Route::get('user/{id}',  [UserController::class, 'show']);
Route::delete('user/delete/{id}',[UserController::class, 'destroy']);


Route::get('users/{id?}', [UserController::class, 'get']);
Route::put('update/{id}',[UserController::class, 'update']);
Route::patch('change-password',[UserController::class, 'changedpassword']);
