<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails())
        {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            "name" => $request->input('name'),
            "email" => $request->input('email'),
            "password" => bcrypt($request->input('password'))
        ]);

        $token = $user->createToken('name');
        return response()->json([
            "message" => "Success. Account created.",
            "user" => $user,
            "token" => $token->plainTextToken
        ], 200);
    }

    public function rules(): array
    {
        return [
            "email" => 'required|email|unique:users|max:100',
            "name" => 'required|max:60',
            "password" => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()]
        ];
    }
}
