<?php

namespace Tests\Feature;

use App\Email;
use App\Search;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_has_to_be_authenticated_to_download_user_emails()
    {
        $user = factory(User::class)->create();

        $this
            ->get(route('user.emails', $user))
            ->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function a_user_cannot_download_another_user_emails()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this
            ->actingAs($user2)
            ->get(route('user.emails', $user1))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_user_can_download_all_of_his_emails()
    {
        $user = factory(User::class)->create()->fresh();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $email0 = factory(Email::class)->create(['search_id' => $search->id]);
        $email1 = factory(Email::class)->create(['search_id' => $search->id]);
        $email2 = factory(Email::class)->create(['search_id' => $search->id]);

        $response = $this
            ->actingAs($user)
            ->get(route('user.emails', $user));

        $response->assertStatus(Response::HTTP_OK);

        /** @var \Symfony\Component\HttpFoundation\File\File $file */
        $file = $response->getFile();

        $this->assertSame('txt', $file->guessExtension());

        foreach ($file->openFile() as $lineNumber => $content) {
            $this->assertSame(${"email{$lineNumber}"}->name, trim($content));
        }
    }
}
