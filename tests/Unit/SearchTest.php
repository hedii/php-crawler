<?php

namespace Tests\Unit;

use App\Search;
use App\User;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_typecast_its_attributes()
    {
        $search = factory(Search::class)->create();

        $this->assertInternalType('integer', $search->user_id);
        $this->assertInternalType('boolean', $search->is_limited);
    }

    /** @test */
    public function it_should_have_a_user_relationship()
    {
        $search = factory(Search::class)->create();

        $this->assertInstanceOf(User::class, $search->user);
    }

    /** @test */
    public function it_should_have_a_urls_relationship()
    {
        $search = factory(Search::class)->create();

        $this->assertInstanceOf(Collection::class, $search->urls);
    }

    /** @test */
    public function it_should_have_an_emails_relationship()
    {
        $search = factory(Search::class)->create();

        $this->assertInstanceOf(Collection::class, $search->emails);
    }

    /** @test */
    public function it_should_have_a_is_created_attribute()
    {
        $search = factory(Search::class)->states('created')->create();

        $this->assertSame(true, $search->is_created);
    }

    /** @test */
    public function it_should_have_a_is_running_attribute()
    {
        $search = factory(Search::class)->states('running')->create();

        $this->assertSame(true, $search->is_running);
    }

    /** @test */
    public function it_should_have_a_is_finished_attribute()
    {
        $search = factory(Search::class)->states('finished')->create();

        $this->assertSame(true, $search->is_finished);
    }

    /** @test */
    public function it_should_have_a_is_failed_attribute()
    {
        $search = factory(Search::class)->states('failed')->create();

        $this->assertSame(true, $search->is_failed);
    }

    /** @test */
    public function it_should_have_a_domain_attribute()
    {
        $this->assertSame(
            'http://example.com',
            factory(Search::class)->create(['url' => 'http://example.com'])->domain
        );
        $this->assertSame(
            'https://example.com',
            factory(Search::class)->create(['url' => 'https://example.com'])->domain
        );
        $this->assertSame(
            'http://example.com',
            factory(Search::class)->create(['url' => 'http://example.com/'])->domain
        );
        $this->assertSame(
            'http://example.com',
            factory(Search::class)->create(['url' => 'http://example.com/some-path?foo=bar'])->domain
        );
    }
}
