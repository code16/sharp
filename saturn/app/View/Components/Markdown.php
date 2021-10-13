<?php

namespace App\View\Components;


use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverterInterface;

class Markdown extends Component
{
    protected bool $nl2br;
    protected array $options;
    
    public function __construct($nl2br = false, array $options = [])
    {
        $this->nl2br = $nl2br;
        $this->options = $options;
    }
    
    public function render(): View
    {
        return view('components.markdown');
    }
    
    public function toHtml(string $markdown): string
    {
        return $this->converter()->convertToHtml($markdown);
    }
    
    protected function converter(): MarkdownConverterInterface
    {
        $options = array_merge($this->options, [
            'html_input' => 'allow',
            'renderer' => [
                'soft_break' => $this->nl2br ? "<br>" : "\n",
            ],
        ]);
        
        return new GithubFlavoredMarkdownConverter($options);
    }
}
