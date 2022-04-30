<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\View\Utils\Content\ComponentAttributeBagCollection;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Component;

class Content extends Component
{
    public ComponentAttributeBagCollection $contentComponentAttributes;
    public self $contentComponent;

    public function __construct(
        public ?int $imageThumbnailWidth = null,
        public ?int $imageThumbnailHeight = null,
    ) {
        $this->contentComponentAttributes = new ComponentAttributeBagCollection();
        $this->contentComponentAttributes->put('sharp-image', [
            'thumbnail-width' => $this->imageThumbnailWidth,
            'thumbnail-height' => $this->imageThumbnailHeight,
        ]);
        $this->contentComponent = $this;
    }

    public function getRenderedContent(string $content): string
    {
        $replacements = [
            '@' => '&commat;',
            '{' => '&lbrace;',
            '}' => '&rbrace;',
            '<?' => '&lt;?',
            '?>' => '?&gt;',
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        $content = preg_replace_callback(
            "/<x-([^>\s]+)([^>]*)>/",
            function ($matches) {
                $componentName = $matches[1];
                $attributes = trim(preg_replace('/(^|")\s+:+/', '$1 ::', $matches[2]));

                return "<x-$componentName :attributes=\"\$getAttributes('$componentName')\" $attributes>";
            },
            $content
        );

        return Blade::render(
            $content,
            [
                'getAttributes' => function ($componentName) {
                    return $this->contentComponentAttributes->get($componentName);
                },
            ],
            deleteCachedView: true
        );
    }

    public function render(): string
    {
        return '{!! $getRenderedContent($slot) !!}';
    }
}
