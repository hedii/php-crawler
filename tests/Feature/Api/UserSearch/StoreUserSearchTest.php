<?php

namespace Tests\Feature\Api\UserSearch;

use App\Jobs\CrawlJob;
use App\Search;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreUserSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_to_be_authenticated_to_create_a_search()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->make();

        $this
            ->postJson(route('api.users.searches.store', $user), $search->toArray())
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_cannot_create_a_search_for_another_user()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $search = factory(Search::class)->make();

        $this
            ->actingAs($user2, 'api')
            ->postJson(route('api.users.searches.store', $user1), $search->toArray())
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_store_a_new_user_search()
    {
        Bus::fake();

        $user = factory(User::class)->create();
        $search = factory(Search::class)->make(['user_id' => $user->id]);

        $response = $this
            ->actingAs($user, 'api')
            ->postJson(route('api.users.searches.store', $user), $search->toArray());

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []])
            ->assertJsonFragment($search->toArray());

        $this->assertDatabaseHas('searches', $search->toArray());

        Bus::assertDispatched(CrawlJob::class, function (CrawlJob $job) use ($search) {
            return $job->search->url === $search->url;
        });
    }

    /** @test */
    public function url_is_required()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->make([
            'user_id' => $user->id,
            'url' => null
        ]);

        $response = $this
            ->actingAs($user, 'api')
            ->postJson(route('api.users.searches.store', $user), $search->toArray());

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors' => ['url' => []]]);
    }

    /** @test */
    public function url_must_be_a_valid_url()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->make([
            'user_id' => $user->id,
            'url' => 'not a url'
        ]);

        $response = $this
            ->actingAs($user, 'api')
            ->postJson(route('api.users.searches.store', $user), $search->toArray());

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors' => ['url' => []]]);
    }

    /** @test */
    public function is_limited_is_required()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->make([
            'user_id' => $user->id,
            'is_limited' => null
        ]);

        $response = $this
            ->actingAs($user, 'api')
            ->postJson(route('api.users.searches.store', $user), $search->toArray());

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors' => ['is_limited' => []]]);
    }

    /** @test */
    public function is_limited_must_be_a_boolean()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->make(['user_id' => $user->id,])->toArray();
        $search['is_limited'] = 'not a boolean';

        $response = $this
            ->actingAs($user, 'api')
            ->postJson(route('api.users.searches.store', $user), $search);

        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors' => ['is_limited' => []]]);
    }
}
