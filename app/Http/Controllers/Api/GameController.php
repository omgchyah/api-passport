<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Game;

class GameController extends Controller
{
    /**
     * Handle dice throw action and create a new game record.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function throwDices(Request $request, int $id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(["message" => "User not authenticated."], 401);
        }

        // Check if the user exists
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "User not found."], 404);
        }

        // Check if the authenticated user has permission to perform this action
        if ($user->id !== Auth::id()) {
            return response()->json(["message" => "You're not authorized to play for this user."], 403);
        }

        // Generate dice rolls
        $dice1 = random_int(1, 6);
        $dice2 = random_int(1, 6);
        $result = ($dice1 + $dice2 === 7) ? 'w' : 'l';

        // Create a new game record
        $newGame = Game::create([
            'user_id' => $id,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'result' => $result
        ]);

        return response()->json([
            "message" => $result === 'w' ? "Congratulations! You've won!" : "You've lost. Better luck next time!",
            "newGame" => $newGame,
        ], 201);
    }

    /**
     * Delete all games associated with a user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGames(Request $request, int $id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return response()->json(["message" => "User not authenticated."], 401);
        }

        // Check if the user exists
        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "User not found."], 404);
        }

        // Check if the authenticated user has permission to perform this action
        if ($user->id !== Auth::id()) {
            return response()->json(["message" => "You're not authorized to delete this user's game history."], 403);
        }

        // Delete all games associated with the user
        $user->games()->delete();

        return response()->json([
            "message" => "User's game history has been successfully deleted."
        ], 200);
    }
    /**
     * Get all games associated to a single player.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGames(Request $request, int $id)
    {
        if(!Auth::check()) {
            return response()->json([
                "message" => "User not authenticated."
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
                "message" => "You're not authorized to acces this player's profile."
            ], 403);
        }
        //Get all games from a player
        $games = $user->games;
        //If no games found
        if($games->isEmpty()) {
            return response()->json([
                "user" => $user,
                "games" => $games,
                "message" => "No games found for this user."
            ], 200);
        }
        // Calculate the success percentage
        $successPercentage = $user->games->first()->getAverageSuccessPercentage($id);

        return response()->json([
            "user" => $user,
            "games" => $games,
            "message" => "Your success percentage is " . $successPercentage . " %"
        ], 200);
    }

}
