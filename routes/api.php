<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api'); */

//Open Routes
Route::post("register", [UserController::class, "register"]);
Route::post("login", [UserController::class, "login"]);

//Protected Routes
Route::group([
    "midleware" => ["auth:api"]
], function() {
    Route::get("profile", [UserController::class, "profile"]);
    Route::get("logout", [UserController::class, "logout"]);
});