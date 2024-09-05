<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Register a new user
     * POST [username, email, password]
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        //Validation
        $validatedData = $request->validate([
            "username" => "nullable|string|min:3",
            "email" => "required|string|email|unique:users,email",
            "password" => "required|string|min:8"
        ]);

    // Create User
        $user = User::create([
            "username" => $validatedData["username"] ?? "anonymous", // Default to 'anonymous' if username is null
            "role" => isset($validatedData["username"]) ? "user" : "guest", // Role is 'user' if username is set, otherwise 'guest'
            "email" => $validatedData["email"],
            "password" => Hash::make($validatedData["password"]), // Hash the password
        ]);

        return response()->json([
            "message" => "User registered successfully",
            "user" => $user
        ], 201);
    }
    /**
     * User login and get token
     * POST [email, password]
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //Validation
        $validatedData = $request->validate([
            "email" => "required|string|email",
            "password" => "required|string"
        ]);

        $user = User::where("email", $validatedData['email'])->first();

        if($user) {
            //If password matches, create Token
            if(Hash::check($validatedData["password"], $user->password)) {
                $token = $user->createToken("myToken")->accessToken;

                return response()->json([
                    "message" => "Login successful.",
                    "token" => $token
                ], 200);
            } else {
                return response()->json([
                    "message" => "Password didn't match."
                ], 401);
            }
        } else {
            return response()->json([
                "message" => "Invalid email value."
            ], 401);
        }
    }
    public function authenticateUser()
    {
        if (!Auth::check()) {
            return response()->json([
                "message" => "User not authenticated.",
            ], 401);
        }
        return null;
    }

    /**
     * Get the authenticated user's profile
     * GET [Auth: Token]
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json([
            "data" => Auth::user(),
        ], 200);
    }
    /**
     * Destroy token
     * GET [Auth: Token]
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
                "message" => "Logout successful.",
            ], 200);
    }
    /**
     * Edit a player's username
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function editName(Request $request, int $id)
    {
        // Validate the new username
        $validatedData = $request->validate([
            "username" => "nullable|string|min:3",
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "message" => "User not found.",
            ], 404);
        }

        if ($user->id !== Auth::id()) {
            return response()->json([
                "message" => "You're not authorized to edit this user's name.",
            ], 403);
        }

        // Update the user's name if the key exists
        if (isset($validatedData["username"])) {
            $user->username = $validatedData["username"];
            $user->save(); // Save the changes to the database

            return response()->json([
                "message" => "Username changed successfully.",
                "data" => $user
            ], 200);
        } else {
            return response()->json([
                "message" => "No username provided to update.",
            ], 400);
        }
    }
    /**
     * Get all players with their Success Percentage
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAllPlayers(Request $request)
    {
        $players = User::with('games')->whereIn('role', ['user', 'guest'])->get();

        if($players->isEmpty()) {
            return response()->json([
                "message" => "No players are registered."
            ], 204);
        };
        $playerData = [];
        foreach($players as $player) {
            $successPercentage = $player->calculateSuccessPercentage();
            $playerData[] = [
                'id' => $player->id,
                'username' => $player->username,
                'email' => $player->email,
                'successPercentage' => $successPercentage
            ];
        }
        return response()->json([
            "players" => $playerData,
        ], 200);
    }
    /**
     * Get the average success percentage of all players
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getRanking()
    {
        $players = User::with('games')->whereIn('role', ['user', 'guest'])->get();

        if($players->isEmpty()) {
            return response()->json([
                "message" => "No players are registered."
            ], 204);
        }
        // Directly call the static method without instantiating the User class
        $totalSuccessAverage = User::calculateTotalSuccessPercentage();

        return response()->json([
            "totalAverageSuccessPercentage" => $totalSuccessAverage . "%",
        ], 200);

    }
    /**
     * Get the winner
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getWinner()
    {
        $winners = User::getWinner();

        if (empty($winners)) {
            return response()->json([
                "message" => "No players are registered."
            ], 204);
        }

        $response = [
            "successPercentage" => $winners[0]->calculateSuccessPercentage()
        ];

        if (count($winners) === 1) {
            $response["winner"] = $winners;
        } else {
            $response["winners"] = $winners;
        }

        return response()->json($response, 200);

    }
    /**
     * Get the loser(s)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoser()
    {
        $losers = User::getLoser();

        if (empty($losers)) {
            return response()->json([
                "message" => "No players are registered."
            ], 204);
        }

        $response = [
            "successPercentage" => $losers[0]->calculateSuccessPercentage()
        ];

        if (count($losers) === 1) {
            $response["loser"] = $losers;
        } else {
            $response["losers"] = $losers;
        }

        return response()->json($response, 200);
    }

}
