<?php

namespace Code16\Sharp\Tests\Unit\Utils\Menu;

use Code16\Sharp\Tests\Fixtures\PersonEntity;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Menu\SharpMenu;
use Code16\Sharp\Utils\Menu\SharpMenuSection;

class SharpMenuTest extends SharpTestCase
{
    /** @test */
    function we_can_add_an_entity_link()
    {
        $menu = new class extends SharpMenu {
            public function build(): void
            {
                $this->addEntityLink(PersonEntity::class);
            }
            public function getSections(): array
            {
                return $this->sections;
            }
        };
        
        $menu->build();
        $this->assertCount(1, $menu->getSections());
        $this->assertEquals("test", $menu->getSections()[0]->getTitle());
    }

    /** @test */
    function we_can_build_an_empty_section()
    {
        $menu = new class extends SharpMenu {
            public function build(): void
            {
                $this->addSection("test", function(SharpMenuSection $section) {

                });
            }
            public function getSections(): array
            {
                return $this->sections;
            }
        };

        $menu->build();
        $this->assertCount(1, $menu->getSections());
        $this->assertEquals("test", $menu->getSections()[0]->getTitle());
    }
}