<?php

namespace Tests\Feature\Api\Search;

use App\Email;
use App\Search;
use App\Url;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ShowSearchStatisticsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_to_be_authenticated_to_see_search_statistics()
    {
        $search = factory(Search::class)->create();

        $this
            ->getJson(route('api.searches.statistics.show', $search))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function a_user_cannot_see_another_user_search_statistics()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user1->id]);

        $this
            ->actingAs($user2, 'api')
            ->getJson(route('api.searches.statistics.show', $search))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_should_show_search_statistics()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $url1 = factory(Url::class)->states(['not_crawled'])->create(['search_id' => $search->id]);
        $url2 = factory(Url::class)->states(['not_crawled'])->create(['search_id' => $search->id]);
        $url3 = factory(Url::class)->states(['crawled'])->create(['search_id' => $search->id]);
        $email1 = factory(Email::class)->create(['search_id' => $search->id]);
        $email2 = factory(Email::class)->create(['search_id' => $search->id]);

        $response = $this
            ->actingAs($user, 'api')
            ->getJson(route('api.searches.statistics.show', $search));

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data' => []])
            ->assertJsonFragment($search->toArray())
            ->assertJsonFragment(['urls_count' => '3'])
            ->assertJsonFragment(['not_crawled_urls_count' => '2'])
            ->assertJsonFragment(['crawled_urls_count' => '1'])
            ->assertJsonFragment(['emails_count' => '2']);
    }
}
