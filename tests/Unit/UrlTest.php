<?php

namespace Tests\Unit;

use App\Search;
use App\Url;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_typecast_its_attributes()
    {
        $url = factory(Url::class)->create();

        $this->assertInternalType('integer', $url->search_id);
        $this->assertInternalType('boolean', $url->is_crawled);
    }

    /** @test */
    public function it_should_have_a_search_relationship()
    {
        $url = factory(Url::class)->create();

        $this->assertInstanceOf(Search::class, $url->search);
    }

    /** @test */
    public function it_should_have_an_is_not_crawled_attribute()
    {
        $url = factory(Url::class)->states(['not_crawled'])->create();

        $this->assertSame(true, $url->is_not_crawled);
    }

    /** @test */
    public function it_should_have_a_not_crawled_scope()
    {
        $urlCrawled = factory(Url::class)->states(['crawled'])->create();
        $urlNotCrawled = factory(Url::class)->states(['not_crawled'])->create();

        $this->assertSame(
            $urlNotCrawled->fresh()->toArray(),
            Url::notCrawled()->first()->toArray()
        );
    }
}
