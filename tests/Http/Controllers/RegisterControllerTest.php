<?php

namespace Tests\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesAnAccountOnSuccessfulRegister()
    {
        $email = "testemail@gmail.com";
        $password = "Password123!";
        $name = "john smith";

        $this->postJson('/api/register', [
            "name" => $name,
            "email" => $email,
            "password" => $password
        ])
            ->assertJsonStructure([
                "message",
                "user",
                "token"
            ])
            ->assertStatus(200);

        $this->assertDatabaseCount('users', 1)
             ->assertDatabaseHas('users', ["email" => $email]);
    }

    public function testItReturnsValidationErrorMessages()
    {
        $email = "testemail@gmail.com";
        $badPassword = "pass";
        $name = "john smith";

        $this->postJson('/api/register', [
            "name" => $name,
            "email" => $email,
            "password" => $badPassword
        ])
            ->assertJson([
                "password" => ["The password must be at least 8 characters."]
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('users', 0)
            ->assertDatabaseMissing('users', ["email" => $email]);
    }

    public function testItDoesntCreateAUserWithAnAlreadyExistingEmail()
    {
        $email = "testemail@gmail.com";
        $password = "pass";
        $name = "john smith";
        User::factory()->create([
            "email" => $email,
            "password" => $password,
            "name" => $name
        ]);

        $this->postJson('/api/register', [
            "name" => "joseph joestar",
            "email" => $email,
            "password" => "Testing123&"
        ])
            ->assertJson([
                "email" => ["The email has already been taken."]
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('users', 1);
    }
}
