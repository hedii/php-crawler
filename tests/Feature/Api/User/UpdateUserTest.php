<?php

namespace Tests\Feature\Api\User;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_authenticated_user_can_use_this_route()
    {
        $user = factory(User::class)->create();

        $this
            ->putJson(route('api.users.update', $user))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_cannot_update_another_user()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this
            ->actingAs($user2, 'api')
            ->putJson(route('api.users.update', $user1))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_update_a_user()
    {
        $user = factory(User::class)->create();
        $update = ['name' => 'a new name', 'email' => 'new@example.com'];

        $response = $this
            ->actingAs($user, 'api')
            ->putJson(route('api.users.update', $user), $update);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []])
            ->assertJsonFragment($update);

        $this->assertDatabaseHas('users', array_merge($update, ['id' => $user->id]));
    }
}
