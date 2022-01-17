<?php

namespace Code16\Sharp\Tests\Unit\Show;

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowHtmlField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpShowTest extends SharpTestCase
{
    /** @test */
    public function we_can_add_and_entity_list_section_to_the_layout()
    {
        $sharpShow = new class() extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow {
            public function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowEntityListField::make('entityList', 'entityKey')
                        ->setLabel('Test')
                );
            }

            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addEntityListSection('entityList');
            }
        };

        $this->assertEquals([
            'sections' => [
                [
                    'collapsable' => false,
                    'title'       => '',
                    'columns'     => [
                        [
                            'size'   => 12,
                            'fields' => [
                                [
                                    [
                                        'key'    => 'entityList',
                                        'size'   => 12,
                                        'sizeXS' => 12,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $sharpShow->showLayout());
    }

    /** @test */
    public function we_can_declare_a_collapsable_section()
    {
        $sharpShow = new class() extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow {
            public function buildShowFields(FieldsContainer $showFields): void
            {
                $showFields->addField(
                    SharpShowTextField::make('test')
                        ->setLabel('Test')
                );
            }

            public function buildShowLayout(ShowLayout $showLayout): void
            {
                $showLayout->addSection('test', function (ShowLayoutSection $section) {
                    $section->setCollapsable()
                        ->addColumn(12, function (ShowLayoutColumn $column) {
                            $column->withSingleField('test');
                        });
                });
            }
        };

        $this->assertEquals([
            'sections' => [
                [
                    'collapsable' => true,
                    'title'       => 'test',
                    'columns'     => [
                        [
                            'size'   => 12,
                            'fields' => [
                                [
                                    [
                                        'key'    => 'test',
                                        'size'   => 12,
                                        'sizeXS' => 12,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $sharpShow->showLayout());
    }

    /** @test */
    public function we_can_declare_a_multiformAttribute()
    {
        $sharpShow = new class() extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow {
            public function buildShowConfig(): void
            {
                $this->configureMultiformAttribute('role');
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertArraySubset(
            [
                'multiformAttribute' => 'role',
            ],
            $sharpShow->showConfig(1)
        );
    }

    /** @test */
    public function we_can_declare_a_global_message_field()
    {
        $sharpShow = new class() extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow {
            public function buildShowConfig(): void
            {
                $this->configurePageAlert('template', static::$pageAlertLevelWarning, 'test-key');
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertEquals(
            'test-key',
            $sharpShow->showConfig(1)['globalMessage']['fieldKey']
        );

        $this->assertEquals(
            'warning',
            $sharpShow->showConfig(1)['globalMessage']['alertLevel']
        );

        $this->assertEquals(
            SharpShowHtmlField::make('test-key')->setInlineTemplate('template')->toArray(),
            $sharpShow->fields()['test-key']
        );
    }

    /** @test */
    public function we_can_associate_data_to_a_global_message_field()
    {
        $sharpShow = new class() extends \Code16\Sharp\Tests\Unit\Show\BaseSharpShow {
            public function buildShowConfig(): void
            {
                $this->configurePageAlert('Hello {{name}}', null, 'test-key');
            }

            public function find($id): array
            {
                return [
                    'test-key' => [
                        'name' => 'Bob',
                    ],
                ];
            }
        };

        $sharpShow->buildShowConfig();

        $this->assertEquals(
            ['name' => 'Bob'],
            $sharpShow->instance(1)['test-key']
        );
    }

    /** @test */
    public function single_shows_have_are_declared_in_config()
    {
        $sharpShow = new class() extends \Code16\Sharp\Tests\Unit\Show\BaseSharpSingleShow {
        };

        $this->assertArraySubset(
            [
                'isSingle' => true,
            ],
            $sharpShow->showConfig(null)
        );
    }
}

class BaseSharpShow extends SharpShow
{
    public function find($id): array
    {
    }

    public function buildShowFields(FieldsContainer $showFields): void
    {
    }

    public function buildShowLayout(ShowLayout $showLayout): void
    {
    }
}

class BaseSharpSingleShow extends SharpSingleShow
{
    public function buildShowFields(FieldsContainer $showFields): void
    {
    }

    public function buildShowLayout(ShowLayout $showLayout): void
    {
    }

    public function findSingle(): array
    {
    }
}
