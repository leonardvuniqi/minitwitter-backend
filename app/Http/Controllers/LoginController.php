<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password')))
        {
            $token = $request->user()->createToken('name');
            return response()->json([
                "message" => "success",
                "token" => $token->plainTextToken
            ], 200);
        }

        return response()->json([
            "message" => "Invalid login attempt.",
        ], 404);
    }
}
