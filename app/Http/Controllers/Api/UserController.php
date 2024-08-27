<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // POST [username, email, password]
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
            "user" => $user
        ], 201);
    }
    //POST [email, password]
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

        //Auth Token


    }
    // GET [Auth: Token]
    public function profile()
    {
         $userData = auth()->user();

        return response()->json([
            "data" => $userData,
        ], 200);

        // Check if the authenticated user is an admin
        if (auth()->check() && auth()->user()->role !== 'admin') {
            // Return user data if the user is not an admin
            return response()->json([
                "data" => auth()->user(),
            ], 200);
        }

        // Return a 403 Forbidden response if the user is not an admin
        return response()->json([
            "message" => "Admins.",
        ], 403);

    }
    //GET [Auth: Token]
    public function logout()
    {
        
    }
}
