<?php

namespace Code16\Sharp\Tests\Unit\EntityList;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\EntityList\Utils\SharpEntityDefaultTestList;

class SharpEntityListReorderTest extends SharpTestCase
{

    /** @test */
    function we_can_configure_a_reorder_handler()
    {
        $list = new class extends SharpEntityDefaultTestList {
            function buildListConfig(): void
            {
                $this->setReorderable(new class implements ReorderHandler {
                    function reorder(array $ids): void {}
                });
            }
        };

        $list->buildListConfig();

        $this->assertArraySubset([
            "reorderable" => true
        ], $list->listConfig());

        $this->assertInstanceOf(ReorderHandler::class, $list->reorderHandler());
    }

}