<?php

namespace Tests\Unit;

use App\Email;
use App\Search;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_typecast_its_attributes()
    {
        $email = factory(Email::class)->create();

        $this->assertInternalType('integer', $email->search_id);
    }

    /** @test */
    public function it_should_have_a_search_relationship()
    {
        $email = factory(Email::class)->create();

        $this->assertInstanceOf(Search::class, $email->search);
    }
}
