<?php

namespace Tests\Feature\Api\Search;

use App\Search;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UpdateSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_to_be_authenticated_to_update_a_search()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $update = ['status' => Search::STATUS_PAUSED];

        $this
            ->putJson(route('api.searches.update', $search), $update)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_cannot_update_another_user_search()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user1->id]);
        $update = ['status' => Search::STATUS_PAUSED];

        $this
            ->actingAs($user2, 'api')
            ->putJson(route('api.searches.update', $search), $update)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_update_a_search()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $update = ['status' => Search::STATUS_PAUSED];

        $response = $this
            ->actingAs($user, 'api')
            ->putJson(route('api.searches.update', $search), $update);

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []])
            ->assertJsonFragment(array_merge($search->toArray(), $update));

        $this->assertDatabaseHas('searches', [
            'id' => $search->id,
            'status' => $update['status']
        ]);
    }

    /** @test */
    public function is_limited_is_nullable()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $update = ['is_limited' => null];

        $this
            ->actingAs($user, 'api')
            ->putJson(route('api.searches.update', $search), $update)
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function is_limited_must_be_a_boolean()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $update = ['is_limited' => 'not a boolean'];

        $this
            ->actingAs($user, 'api')
            ->putJson(route('api.searches.update', $search), $update)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors' => ['is_limited' => []]]);
    }

    /** @test */
    public function status_is_nullable()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $update = ['status' => null];

        $this
            ->actingAs($user, 'api')
            ->putJson(route('api.searches.update', $search), $update)
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function status_must_be_a_valid_status()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $update = ['status' => 'not a valid status'];

        $this
            ->actingAs($user, 'api')
            ->putJson(route('api.searches.update', $search), $update)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors' => ['status' => []]]);
    }
}
