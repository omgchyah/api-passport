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

    Route::get("logout", [UserController::class, "logout"]);
    // Player-specific routes (only accessible to users with role 'user' or 'guest')
    Route::middleware(['role:user,guest'])->group(function () {
        Route::patch("players/{id}", [UserController::class, 'editName']); //modify player's name
        Route::get("profile", [UserController::class, "profile"]);
    });

    
});

    // Player-specific routes
    /*/POST /players:

    Description: Create a new player.
    Role: Accessible to anyone (open route) to allow new player registration.

PUT /players/{id}:

    Description: Update the name of the player.
    Role: Accessible to the specific player (who owns the account).

POST /players/{id}/games/:

    Description: A specific player rolls the dice.
    Role: Accessible only to the player making the request.

DELETE /players/{id}/games:

    Description: Delete all the rolls of a specific player.
    Role: Accessible only to the player making the request.

GET /players/{id}/games:

    Description: Return the list of games (dice rolls) for a specific player.
    Role: Accessible only to the player making the request.
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