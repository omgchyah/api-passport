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

    // Player-specific routes
    /*POST /players:
    PUT /players/{id}:
    POST /players/{id}/games/:
    DELETE /players/{id}/games:
    GET /players/{id}/games:
    */



    //Admin-specific routes
/*     GET /players:

    Description: Return the list of all players in the system with their average success rate.
    Role: Accessible only to admins.

GET /players/ranking:

    Description: Return the average ranking (success rate) of all players in the system.
    Role: Accessible only to admins.

GET /players/ranking/loser:

    Description: Return the player with the worst success rate.
    Role: Accessible only to admins.

GET /players/ranking/winner:

    Description: Return the player with the best success rate.
    Role: Accessible only to admins. */