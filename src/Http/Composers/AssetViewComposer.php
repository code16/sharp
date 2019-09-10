<?php

namespace Code16\Sharp\Http\Composers;

use Code16\Sharp\Exceptions\View\SharpInvalidAssetRenderStrategy;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Provide asset templates to views defined in config
 *
 * @package Code16\Sharp
 */
class AssetViewComposer
{
    /**
     * The acceptable assset types to output
     *
     * @var array
     */
    protected $assetTypes = ['css'];

    /**
     * The strategies for outputting the asset paths
     *
     * - raw   - to output the raw string
     * - asset - to output through the asset() function
     * - mix   - to output through the mix() function
     *
     * @var array
     */
    protected $renderStrategies = ['raw', 'asset', 'mix'];

    /**
     * The templates to inject asset paths into based on file type
     *
     * @var array
     */
    protected $renderTemplates = [
        'css' => '<link rel="stylesheet" href="%s">',
    ];

    /**
     * Bind data to the view
     *
     * @param View $view
     * @throws SharpInvalidAssetRenderStrategy
     */
    public function compose(View $view)
    {
        $strategy = $this->getValidatedStrategyFromConfig();
        $output = [];

        foreach((array)config("sharp.extensions.assets") as $position => $paths) {
            foreach((array)$paths as $assetPath) {
                if(!isset($output[$position])) {
                    $output[$position] = [];
                }

                // Only render valid assets
                if(Str::endsWith($assetPath, $this->assetTypes)) {
                    // Grab the relevant template based on the filetype
                    $template = Arr::get($this->renderTemplates, $this->getAssetFileType($assetPath));

                    // Apply the strategy (run through asset() or mix()
                    $resolvedAssetPath = $this->getAssetPathWithStrategyApplied($strategy, $assetPath);

                    $output[$position][] = sprintf($template, $resolvedAssetPath);
                }
            }
        }

        // Build the strings to output in the blades
        $toBind = [];
        foreach((array)$output as $position => $toOutput) {
            $toBind[$position] = implode('', $toOutput);
        }

        $view->with('injectedAssets', $toBind);
    }

    /**
     * Get the asset file type
     *
     * @param  string $assetPath
     * @return string
     */
    protected function getAssetFileType($assetPath):string
    {
        $parts = explode('.', $assetPath);

        return Arr::last($parts);
    }

    /**
     * Apply the strategy to the asset path to insert into the html template
     *
     * @param  string $strategy
     * @param  string $assetPath
     * @return string
     */
    protected function getAssetPathWithStrategyApplied($strategy, $assetPath):string
    {
        switch($strategy)
        {
            case 'asset':
            case 'mix':
                return $strategy($assetPath);
                break;

            default:
                return $assetPath;
                break;
        }
    }

    /**
     * Get the strategy to render the assets
     *
     * @return string
     * @throws SharpInvalidAssetRenderStrategy
     */
    protected function getValidatedStrategyFromConfig():string
    {
        $strategy = config('sharp.extensions.assets.strategy', 'raw');

        if(!is_string($strategy)) {
            throw new SharpInvalidAssetRenderStrategy('The asset strategy defined at sharp.extensions.assets.strategy is not a string');
        }

        if(!in_array($strategy, $this->renderStrategies)) {
            throw new SharpInvalidAssetRenderStrategy("The asset strategy [$strategy] is not raw, asset, or mix");
        }

        return $strategy;
    }
}