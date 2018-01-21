<?php

namespace Tests\Feature\Api\UserSearch;

use App\Search;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class IndexUserSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_to_be_authenticated_index_his_searches()
    {
        $user = factory(User::class)->create();

        $this
            ->getJson(route('api.users.searches.index', $user))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_cannot_index_another_user_searches()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this
            ->actingAs($user2, 'api')
            ->getJson(route('api.users.searches.index', $user1))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_index_user_searches()
    {
        $user = factory(User::class)->create();
        $search1 = factory(Search::class)->create(['user_id' => $user->id]);
        $search2 = factory(Search::class)->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user, 'api')
            ->getJson(route('api.users.searches.index', $user));

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => [], 'links' => []])
            ->assertJsonFragment($search1->toArray())
            ->assertJsonFragment($search2->toArray());
    }
}
