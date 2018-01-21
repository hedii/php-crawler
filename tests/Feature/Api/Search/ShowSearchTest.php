<?php

namespace Tests\Feature\Api\Search;

use App\Search;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ShowSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_to_be_authenticated_to_see_a_search()
    {
        $search = factory(Search::class)->create();

        $this
            ->getJson(route('api.searches.show', $search))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_cannot_see_another_user_search()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user1->id]);

        $this
            ->actingAs($user2, 'api')
            ->getJson(route('api.searches.show', $search))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_show_a_search()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user, 'api')
            ->getJson(route('api.searches.show', $search));

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['data' => $search->toArray()]);
    }
}
