<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api'); */

//Open Routes
Route::post("players", [UserController::class, "register"]); //crea un jugador/a.
Route::post("login", [UserController::class, "login"]); //Get player Token

//Protected Routes
Route::group([
    "middleware" => ["auth:api"]
], function() {
    Route::get("profile", [UserController::class, "profile"]);
    Route::get("logout", [UserController::class, "logout"]);
});