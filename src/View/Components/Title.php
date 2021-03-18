<?php

namespace Code16\Sharp\View\Components;

use Illuminate\View\Component;

class Title extends Component
{
    public function currentEntityLabel(): ?string
    {
        return collect(config("sharp.menu"))
                ->mapWithKeys(function($itemOrCategory) {
                    return isset($itemOrCategory["entities"])
                        ? collect($itemOrCategory["entities"])
                            ->mapWithKeys(function($item) {
                                return $this->extractKeyAndLabel($item);
                            })
                        : $this->extractKeyAndLabel($itemOrCategory);
                })
                ->filter()[currentSharpRequest()->entityKey()] ?? null;
    }

    public function render()
    {
        return view('sharp::components.title', [
            'component' => $this,
        ]);
    }

    private function extractKeyAndLabel(array $item): array
    {
        if(isset($item["entity"])) {
            return [$item["entity"] => $item["label"] ?? ""];
        }

        if(isset($item["dashboard"])) {
            return [$item["dashboard"] => $item["label"] ?? ""];
        }
        
        return [];
    }
}
