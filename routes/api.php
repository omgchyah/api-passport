<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GameController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api'); */

//Open Routes
//crea un jugador/a.
Route::post("players", [UserController::class, "register"]); 
//Get player Token
Route::post("login", [UserController::class, "login"]); 

//Protected Routes
Route::group([
    "middleware" => ["auth:api"]
], function() {

    //Destroy token
    Route::get("logout", [UserController::class, "logout"]);
    
    // Player-specific routes (only accessible to users with role 'user' or 'guest')
    Route::middleware(['role:user,guest'])->group(function () {
        //modify player's name
        Route::patch("players/{id}", [UserController::class, 'editName']);
        //Player can access their own profile
        Route::get("players/profile", [UserController::class, "profile"]);
        //un jugador/a específic realitza una tirada dels daus.
        Route::post("players/{id}/games", [GameController::class, "throwDices"]);
        //elimina les tirades del jugador/a.
        Route::delete("players/{id}/games", [GameController::class, "deleteGames"]);
        //retorna el llistat de jugades per un jugador/a.
        Route::get("players/{id}/games", [GameController::class, "getGames"]);
    });

    //Admin-specific routes
    Route::middleware(['role:admin'])->group(function() {
        Route::get("players", [UserController::class, "getAllPlayers"]);
        Route::get("players/ranking", [UserController::class, "getRanking"]);
        Route::get("players/ranking/loser", [UserController::class, "getLoser"]);
        Route::get("players/ranking/winner", [UserController::class, "getWinner"]);
    });
    
});
