<?php

namespace Tests\Feature\Api\User;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class MeUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_authenticated_user_can_use_this_route()
    {
        $this
            ->getJson(route('api.users.me'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_should_show_the_curent_user()
    {
        $user = factory(User::class)->create()->fresh();

        $response = $this
            ->actingAs($user, 'api')
            ->getJson(route('api.users.me'));

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []])
            ->assertJson(['data' => $user->toArray()]);
    }
}
