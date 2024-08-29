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
     * Authorize the user to ensure they are authenticated and can access resources.
     *
     * @param int $id
     * @return \App\Models\User|\Illuminate\Http\JsonResponse
     */
    public function authorizeUser(int $id)
    {
        if (!Auth::user()) {
            return response()->json(["message" => "User not authenticated."], 401);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(["message" => "User not found."], 404);
        }

        if ($user->id !== Auth::id()) {
            return response()->json(["message" => "You're not authorized to access this resource."], 403);
        }

        return $user;
    }

    /**
     * Handle dice throw action and create a new game record.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function throwDices(Request $request, int $id)
    {
        $user = $this->authorizeUser($id);

        // Check if the response is a JSON error response and return if so
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
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
        $user = $this->authorizeUser($id);

        // Check if the response is a JSON error response and return if so
        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        // Delete all games associated with the user
        $user->games()->delete();

        return response()->json([
            "message" => "User's game history has been successfully deleted."
        ], 200);
    }
}
