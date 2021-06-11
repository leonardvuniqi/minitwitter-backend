<?php


namespace Tests\Http\Controllers\Tweet;

use App\Http\Controllers\Tweet\Create as CreateController;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class Create extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testItCreatesANewTweetForAnAuthenticatedUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );

        $this->postJson('/api/tweet/create', [
            "tweet" => [
                "user_id" => $user->id,
                "message" => "Hello testing tweet 123, testing tweet 123."
            ]
        ])
            ->assertSuccessful()
            ->assertJsonStructure();
        $this->assertDatabaseCount(Tweet::class, 1);
    }

    public function testItReturnsA401WhenAnAuthenticatedUserCreatesATweet()
    {
        $user = User::factory()->create();

        $this->postJson('/api/tweet/create', [
            "tweet" => [
                "user_id" => $user->id,
                "message" => "Hello testing tweet 123, testing tweet 123."
            ]
        ])
            ->assertStatus(401);
    }

    public function testItReturnsAnErrorWhenCreatingATweetWithAnEmptyMessage()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $this->postJson('/api/tweet/create', [
            "tweet" => [
                "user_id" => $user->id,
                "message" => ''
            ]
        ])
            ->assertStatus(422)
            ->assertJson([
                "message" => "Error",
                "errors" => [
                    "message" => [
                        CreateController::ERROR_MESSAGES["tweet"]["message"]["required"]
                    ]
                ]
            ]);
    }

    public function testItReturnsAnErrorWhenCreatingATweetThatIsLongerThan140CharactersLong()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $this->postJson('/api/tweet/create', [
            "tweet" => [
                "user_id" => $user->id,
                "message" => $this->faker->sentence(200)
            ]
        ])
            ->assertStatus(422)
            ->assertJson([
                "message" => "Error",
                "errors" => [
                    "message" => [
                        CreateController::ERROR_MESSAGES["tweet"]["message"]["max"]
                    ]
                ]
            ]);
    }
}
