<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Game;

class GameController extends Controller
{
    public function throwDices(Request $request, int $id)
    {
        if(!Auth::check()) {
            return response()->json([
                "message" => "User not authenticated.",
            ], 401);
        }
        $user = User::find($id);
        if(!$user) {
            return response()->json([
                "message" => "User not found."
            ], 404);
        }
        if($user->id !== Auth::id()) {
            return response()->json([
                "message" => "You're not authorized to play for this user."
            ], 403);
        }

        $dice1 = random_int(1, 6);
        $dice2 = random_int(1, 6);
        $result = ($dice1 + $dice2 === 7) ? 'w' : 'l';
        $newGame = Game::create([
            'user_id' => $id,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'result' => $result
        ]);

        return response()->json([
            "message" => "Game played successfully",
            "newGame" => $newGame,
        ], 201);
    }
}
