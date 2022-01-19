<?php

namespace Code16\Sharp\View\Components\Content;

use Code16\Sharp\View\Components\Content\Utils\Fragment;
use Code16\Sharp\View\Components\Content\Utils\FragmentsFactory;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class RenderContent extends Component
{
    public function __construct(
        public string $content,
        public FragmentsFactory $fragmentsFactory
    ) {
    }

    public function fragments(): Collection
    {
        return $this->fragmentsFactory->fromHTML(trim($this->content));
    }

    public function fragmentComponent(Fragment $fragment): ?string
    {
        if ($fragment->type === 'html') {
            return 'sharp::content.render-html';
        }
        if ($fragment->type === 'component') {
            return 'sharp::content.render-component';
        }

        return null;
    }

    public function render()
    {
        return view('sharp::components.content.render-content');
    }
}
