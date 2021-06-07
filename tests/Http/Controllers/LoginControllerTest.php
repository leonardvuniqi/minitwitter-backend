<?php

namespace Tests\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesATokenOnSuccessfulLoginAttempt()
    {
        $email = "testemail@test.com";
        $password = "Testpass123!";
        User::factory()->create([
            "email" => $email,
            "password" => bcrypt($password)
        ]);

        $this->post('/api/login', [
            "email" => $email,
            "password" => $password
        ])
            ->assertSuccessful()
            ->assertJsonStructure([
                "string" => "message",
                "string" => "token"
            ]);
    }

    public function testItReturnsAnErrorOnUnsuccessfulLoginAttempt()
    {
        $email = "testemail@test.com";
        $password = "Testpass123!";
        User::factory()->create([
            "email" => $email,
            "password" => bcrypt($password)
        ]);

        $this->post('/api/login', [
            "email" => $email,
            "password" => "wrongpassword555!"
        ])
            ->assertStatus(404)
            ->assertJson([
                "message" => "Invalid login attempt."
            ]);
    }
}
