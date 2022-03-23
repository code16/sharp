<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpFormEditorEmbedTest extends SharpTestCase
{
    /** @test */
    public function default_values_are_set_in_config()
    {
        $defaultEmbed = new DefaultFakeSharpFormEditorEmbed();
        $defaultEmbed->buildEmbedConfig();

        $this->assertEquals(
            [
                'key' => $defaultEmbed->key(),
                'label' => 'default_fake_sharp_form_editor_embed',
                'tag' => 'x-default-fake-sharp-form-editor-embed',
                'attributes' => ['text'],
                'template' => 'Empty template',
            ],
            $defaultEmbed->toConfigArray(true),
        );
    }

    /** @test */
    public function we_can_configure_tag()
    {
        $defaultEmbed = new class() extends DefaultFakeSharpFormEditorEmbed
        {
            public function buildEmbedConfig(): void
            {
                $this->configureTagName('my-tag');
            }
        };

        $defaultEmbed->buildEmbedConfig();

        $this->assertEquals(
            'my-tag',
            $defaultEmbed->toConfigArray(true)['tag'],
        );
    }

    /** @test */
    public function we_can_configure_label()
    {
        $defaultEmbed = new class() extends DefaultFakeSharpFormEditorEmbed
        {
            public function buildEmbedConfig(): void
            {
                $this->configureTagName('my-tag')
                    ->configureLabel('Some Label');
            }
        };

        $defaultEmbed->buildEmbedConfig();

        $this->assertEquals(
            'Some Label',
            $defaultEmbed->toConfigArray(true)['label'],
        );
    }

    /** @test */
    public function we_can_configure_form_template()
    {
        $defaultEmbed = new class() extends DefaultFakeSharpFormEditorEmbed
        {
            public function buildEmbedConfig(): void
            {
                $this->configureTagName('my-tag')
                    ->configureFormInlineTemplate('{{text}}');
            }
        };

        $defaultEmbed->buildEmbedConfig();

        $this->assertEquals(
            '{{text}}',
            $defaultEmbed->toConfigArray(true)['template'],
        );
    }

    /** @test */
    public function we_can_configure_show_template()
    {
        $defaultEmbed = new class() extends DefaultFakeSharpFormEditorEmbed
        {
            public function buildEmbedConfig(): void
            {
                $this->configureTagName('my-tag')
                    ->configureShowInlineTemplate('show {{text}}');
            }
        };

        $defaultEmbed->buildEmbedConfig();

        $this->assertEquals(
            'show {{text}}',
            $defaultEmbed->toConfigArray(false)['template'],
        );

        $this->assertEquals(
            'Empty template',
            $defaultEmbed->toConfigArray(true)['template'],
        );
    }
}

class DefaultFakeSharpFormEditorEmbed extends SharpFormEditorEmbed
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('text')
        );
    }

    public function updateContent(array $data = []): array
    {
    }
}
