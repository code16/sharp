<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Carbon\Carbon;
use Code16\Sharp\EntityList\EntityListQueryParams;
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

    /** @test */
    function we_can_find_a_filter_value()
    {
        $this->assertEquals(
            "1",
            $this->buildParams(1, "", null, null, ["type" => 1])->filterFor("type")
        );

        $this->assertEquals(
            [
                "toto",
                "titi",
                "tata"
            ],
            $this->buildParams(1, "", null, null, ["type" => "toto,titi,tata"])->filterFor("type")
        );

        $this->assertEquals(
            [
                "start" => Carbon::createFromFormat('Y-m-d','2019-02-01'),
                "end" => Carbon::createFromFormat('Y-m-d','2019-02-10'),
            ],
            $this->buildParams(1, "", null, null, ["range" => "20190201..20190210"])->filterFor("range")
        );

        $this->assertNull(
            $this->buildParams()->filterFor("type")
        );
    }

    private function buildParams($p=1, $s="", $sb=null, $sd=null, $f=null)
    {
        return new class($p, $s, $sb, $sd, $f) extends EntityListQueryParams  {
            public function __construct($p, $s, $sb, $sd, $f)
            {
                $this->page = $p;
                $this->search = $s;
                $this->sortedBy = $sb;
                $this->sortedDir = $sd;
                if($f) {
                    $this->filters = $f;
                }
            }
        };
    }
}