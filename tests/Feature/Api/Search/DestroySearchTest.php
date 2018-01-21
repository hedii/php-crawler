<?php

namespace Tests\Feature\Api\Search;

use App\Search;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class DestroySearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_to_be_authenticated_to_destroy_a_search()
    {
        $search = factory(Search::class)->create();

        $this
            ->deleteJson(route('api.searches.destroy', $search))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_cannot_delete_another_user_search()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user1->id]);

        $this
            ->actingAs($user2, 'api')
            ->deleteJson(route('api.searches.destroy', $search))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_destroy_a_search()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);

        $this
            ->actingAs($user, 'api')
            ->deleteJson(route('api.searches.destroy', $search))
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('searches', $search->toArray());
    }
}
