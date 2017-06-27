<?php

namespace Code16\Sharp\Tests\Unit\EntitiesList;

use Code16\Sharp\EntitiesList\EntitiesListQueryParams;
use Code16\Sharp\Tests\SharpTestCase;

class EntitiesListQueryParamsTest extends SharpTestCase
{

    /** @test */
    function we_can_know_there_is_a_search_with_hasSearch()
    {
        $this->assertTrue($this->buildParams(1, "test")->hasSearch());
        $this->assertFalse($this->buildParams(1, "")->hasSearch());
    }

    /** @test */
    function we_can_explode_a_search_in_words()
    {
        $this->assertEquals(
            ["%the%", "%little%", "%cat%", "%is%", "%dead%"],
            $this->buildParams(1, "the little cat is dead")->searchWords()
        );
    }

    /** @test */
    function we_can_search_without_like()
    {
        $this->assertEquals(
            ["the", "little", "cat", "is", "dead"],
            $this->buildParams(1, "the little cat is dead")->searchWords(false)
        );
    }

    /** @test */
    function we_can_use_a_star_in_search()
    {
        $this->assertEquals(
            ["cat%"],
            $this->buildParams(1, "cat*")->searchWords()
        );

        $this->assertEquals(
            ["%cat"],
            $this->buildParams(1, "*cat")->searchWords()
        );
    }

    private function buildParams($p=1,$s="",$sb=null,$sd=null)
    {
        return new class($p, $s, $sb, $sd) extends EntitiesListQueryParams  {
            public function __construct($p, $s, $sb, $sd)
            {
                $this->page = $p;
                $this->search = $s;
                $this->sortedBy = $sb;
                $this->sortedDir = $sd;
            }
        };
    }
}