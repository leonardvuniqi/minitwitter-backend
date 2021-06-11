<?php

namespace App\Http\Controllers\Tweet;

use App\Http\Controllers\Controller;
use App\Models\Tweet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class Create extends Controller
{
    const ERROR_MESSAGES = [
        "tweet" => [
            "message" => [
                "required" => "Tweet cannot be empty.",
                "max" => "Your tweet cannot be longer than 140 characters."
            ]
        ]
    ];

    public function __invoke(Request $request)
    {
        $message = $request->input("tweet.message");
        $userId = $request->input("tweet.user_id");

        $validator = Validator::make([
            "message" => $message,
            "user_id" => $userId
        ],
        $this->rules(),
        $this->messages()
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error",
                "errors" => $validator->errors()
            ], 422);
        }

        $tweet = Tweet::create([
            "message" => $message,
            "user_id" => $userId
        ]);

        return response()->json([
            "message" => "Success. Tweet created",
            "tweet" => $tweet
        ]);
    }

    private function rules(): array
    {
        return [
            "message" => 'required|max:140'
        ];
    }

    private function messages(): array
    {
        return [
            "message.required" => self::ERROR_MESSAGES["tweet"]["message"]["required"],
            "message.max" => self::ERROR_MESSAGES["tweet"]["message"]["max"]
        ];
    }
}
