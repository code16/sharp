<?php

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Arr;

it('sets default values in config', function () {
    $defaultEmbed = new class() extends SharpFormEditorEmbed
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('text')
            );
        }

        public function updateContent(array $data = []): array {}

        public function buildEmbedConfig(): void
        {
            $this->configureTagName('x-default-fake-sharp-form-editor-embed')
                ->configureLabel('default_fake_sharp_form_editor_embed');
        }
    };
    $defaultEmbed->buildEmbedConfig();

    expect(Arr::except($defaultEmbed->toConfigArray(true), ['fields']))
        ->toEqual([
            'key' => $defaultEmbed->key(),
            'label' => 'default_fake_sharp_form_editor_embed',
            'tag' => 'x-default-fake-sharp-form-editor-embed',
            'attributes' => ['text'],
            'icon' => null,
        ])
        ->and($defaultEmbed->toConfigArray(true))
        ->toHaveKey('fields.text')
        ->toHaveKey('fields.text.type', 'text');
});

it('allows to configure tag', function () {
    $defaultEmbed = new class() extends SharpFormEditorEmbed
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('text')
            );
        }

        public function updateContent(array $data = []): array {}

        public function buildEmbedConfig(): void
        {
            $this->configureTagName('my-tag');
        }
    };

    $defaultEmbed->buildEmbedConfig();

    expect($defaultEmbed->toConfigArray(true)['tag'])
        ->toEqual('my-tag');
});

it('allows to configure label', function () {
    $defaultEmbed = new class() extends SharpFormEditorEmbed
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('text')
            );
        }

        public function updateContent(array $data = []): array {}

        public function buildEmbedConfig(): void
        {
            $this->configureTagName('my-tag')
                ->configureLabel('Some Label');
        }
    };

    $defaultEmbed->buildEmbedConfig();

    expect($defaultEmbed->toConfigArray(true)['label'])
        ->toEqual('Some Label');
});

it('allows to configure both form & shows template', function () {
    $defaultEmbed = new class() extends SharpFormEditorEmbed
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('text')
            );
        }

        public function updateContent(array $data = []): array {}

        public function buildEmbedConfig(): void
        {
            $this->configureTagName('my-tag')
                ->configureTemplate('{{ $text }}');
        }
    };

    $defaultEmbed->buildEmbedConfig();

    expect($defaultEmbed->transformDataWithRenderedTemplate(['id' => 1, 'text' => 'Test'], isForm: false))->toEqual([
        'id' => 1,
        'text' => 'Test',
        '_html' => 'Test',
    ]);

    expect($defaultEmbed->transformDataWithRenderedTemplate(['id' => 1, 'text' => 'Test'], isForm: true))->toEqual([
        'id' => 1,
        'text' => 'Test',
        '_html' => 'Test',
    ]);
});

it('allows to configure form template', function () {
    $defaultEmbed = new class() extends SharpFormEditorEmbed
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('text')
            );
        }

        public function updateContent(array $data = []): array {}

        public function buildEmbedConfig(): void
        {
            $this->configureTagName('my-tag')
                ->configureFormTemplate('{{ $text }}');
        }
    };

    $defaultEmbed->buildEmbedConfig();

    expect($defaultEmbed->transformDataWithRenderedTemplate(['id' => 1, 'text' => 'Test'], isForm: false))->toEqual([
        'id' => 1,
        'text' => 'Test',
        '_html' => 'Empty template',
    ]);

    expect($defaultEmbed->transformDataWithRenderedTemplate(['id' => 1, 'text' => 'Test'], isForm: true))->toEqual([
        'id' => 1,
        'text' => 'Test',
        '_html' => 'Test',
    ]);
});

it('allows to configure show template', function () {
    $defaultEmbed = new class() extends SharpFormEditorEmbed
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('text')
            );
        }

        public function updateContent(array $data = []): array {}

        public function buildEmbedConfig(): void
        {
            $this->configureTagName('my-tag')
                ->configureShowTemplate('{{ $text }}');
        }
    };

    $defaultEmbed->buildEmbedConfig();

    expect($defaultEmbed->transformDataWithRenderedTemplate(['id' => 1, 'text' => 'Test'], isForm: false))->toEqual([
        'id' => 1,
        'text' => 'Test',
        '_html' => 'Test',
    ]);

    expect($defaultEmbed->transformDataWithRenderedTemplate(['id' => 1, 'text' => 'Test'], isForm: true))->toEqual([
        'id' => 1,
        'text' => 'Test',
        '_html' => 'Empty template',
    ]);
});

it('allows to configure icon', function () {
    $defaultEmbed = new class() extends SharpFormEditorEmbed
    {
        public function buildFormFields(FieldsContainer $formFields): void
        {
            $formFields->addField(
                SharpFormTextField::make('text')
            );
        }

        public function updateContent(array $data = []): array {}

        public function buildEmbedConfig(): void
        {
            $this->configureTagName('test')
                ->configureIcon('testicon-user');
        }
    };

    $defaultEmbed->buildEmbedConfig();

    expect($defaultEmbed->toConfigArray(true)['icon'])
        ->toEqual([
            'name' => 'testicon-user',
            'svg' => '<svg><!--user--></svg>',
        ]);
});
