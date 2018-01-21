<?php

namespace Tests\Unit;

use App\Email;
use App\Search;
use App\User;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_have_a_searches_relationship()
    {
        $user = factory(User::class)->create();

        $this->assertInstanceOf(Collection::class, $user->searches);
    }

    /** @test */
    public function it_should_have_an_emails_relationship()
    {
        $user = factory(User::class)->create();
        $search = factory(Search::class)->create(['user_id' => $user->id]);
        $email1 = factory(Email::class)->create(['search_id' => $search->id]);
        $email2 = factory(Email::class)->create(['search_id' => $search->id]);
        $email3 = factory(Email::class)->create(['search_id' => $search->id]);

        $this->assertInstanceOf(Collection::class, $user->emails);
        $this->assertCount(3, $user->emails);
        $this->assertTrue($user->emails->contains($email1));
        $this->assertTrue($user->emails->contains($email2));
        $this->assertTrue($user->emails->contains($email3));
    }
}
