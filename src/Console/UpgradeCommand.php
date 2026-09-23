<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpgradeCommand extends Command
{
    protected $signature = 'sharp:upgrade {--dry-run : Run Rector in dry-run mode} {path}';
    protected $description = 'Upgrade Sharp to current major version';

    public function handle()
    {
        $directories = [
            base_path($this->argument('path')),
        ];

        $replacements = [
            '/->setCropRatio\s*\(/' => '->setImageCropRatio(',
            '/->shouldOptimizeImage\s*\(/' => '->setImageOptimize(',
            '/->setTransformable\s*\(/' => '->setImageTransformable(',
            '/->setFileFilterImages\s*\(/' => '->setImageOnly(',
            '/->setFileFilter\s*\(/' => '->setAllowedExtensions(',
            '/->withSingleField\s*\(/' => '->withField(',

            '/Code16\\\\Sharp\\\\EntityList\\\\Filters\\\\EntityList([A-Za-z]+Filter)/' => 'Code16\\Sharp\\Filters\\\\$1',
            '/Code16\\\\Sharp\\\\Utils\\\\Filters\\\\([A-Za-z]+Filter)/' => 'Code16\\Sharp\\Filters\\\\$1',

            '/\bEntityList(CheckFilter|DateRangeFilter|DateRangeRequiredFilter|SelectFilter|SelectMultipleFilter|SelectRequiredFilter)\b/' => '$1',
        ];

        $detections = [
            'currentSharpRequest()' => '/currentSharpRequest\s*\(/',
            'SharpAuthenticationCheckHandler' => '/SharpAuthenticationCheckHandler/',
            'SharpFormRequest' => '/SharpFormRequest/',
            'BindSharpValidationResolver' => '/BindSharpValidationResolver/',
            'SharpFormAutocompleteField' => '/SharpFormAutocompleteField::make\s*\(/',
            '->setWidthOnSmallScreens()' => '/setWidthOnSmallScreens\s*\(/',
            '->setWidthOnSmallScreensFill()' => '/setWidthOnSmallScreensFill\s*\(/',
            '->configureMultiformAttribute()' => '/configureMultiformAttribute\s*\(/',
            '->setDisplayFormat()' => '/setDisplayFormat\s*\(/',
            'getMultiforms()' => '/getMultiforms\s*\(/',
            'execute(): array' => '/function\s+execute\w*\s*\((?:[^()]|\([^()]*\))*\)\s*:\s*\??array\b/',
        ];

        $updatedFiles = [];
        $detectedFiles = [];
        $isDryRun = $this->option('dry-run');

        foreach ($directories as $directory) {
            if (! File::exists($directory)) {
                continue;
            }

            $files = File::allFiles($directory);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $filePath = $file->getPathname();
                $originalContent = File::get($filePath);
                $content = $originalContent;

                foreach ($replacements as $pattern => $replacement) {
                    $content = preg_replace($pattern, $replacement, $content);
                }

                $content = $this->upgradeCommandReturnTypes($content);

                foreach ($detections as $label => $pattern) {
                    if (preg_match($pattern, $content)) {
                        $detectedFiles[$filePath][] = $label;
                    }
                }

                if ($content !== $originalContent) {
                    $relativePath = $file->getRelativePathname();
                    $updatedFiles[] = $relativePath;

                    if (! $isDryRun) {
                        File::put($filePath, $content);
                    }
                }
            }
        }

        foreach ($updatedFiles as $filePath) {
            if ($isDryRun) {
                $this->line("<comment>[Dry Run]</comment> Would update: {$filePath}");
            } else {
                $this->info("Updated: {$filePath}");
            }
        }

        foreach ($detectedFiles as $filePath => $labels) {
            $this->line('The removed symbol(s) <comment>'.implode(', ', $labels)."</comment> have been detected in {$filePath}");
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info(sprintf('Dry run complete. %d files would be updated.', count($updatedFiles)));
        } else {
            $this->info(sprintf('Finished! %d files were successfully updated.', count($updatedFiles)));
        }
    }

    /**
     * Commands must now return a CommandReturn object instead of an array.
     */
    private function upgradeCommandReturnTypes(string $content): string
    {
        $commandBaseClasses = 'EntityCommand|InstanceCommand|SingleInstanceCommand|DashboardCommand'
            .'|EntityState|SingleEntityState|EntityWizardCommand|InstanceWizardCommand'
            .'|SingleInstanceWizardCommand|QuickCreationCommand';

        if (! preg_match('/\bextends\s+('.$commandBaseClasses.')\b/', $content)) {
            return $content;
        }

        $params = '\((?:[^()]|\([^()]*\))*\)';
        $returnTypes = [
            'execute\w*' => ['CommandReturn', ['CommandReturn']],
            'updateState' => ['CommandReloadReturn|CommandRefreshReturn|null', ['CommandRefreshReturn', 'CommandReloadReturn']],
            'updateSingleState' => ['CommandReloadReturn', ['CommandReloadReturn']],
        ];

        $imports = [];
        foreach ($returnTypes as $methodPattern => [$returnType, $classes]) {
            $content = preg_replace(
                '/(function\s+'.$methodPattern.'\s*'.$params.'\s*:\s*)\??array\b/',
                '$1'.$returnType,
                $content,
                -1,
                $count,
            );

            if ($count > 0) {
                $imports = [...$imports, ...$classes];
            }
        }

        foreach (array_unique($imports) as $class) {
            $content = $this->addImport($content, 'Code16\\Sharp\\EntityList\\Commands\\Returns\\'.$class);
        }

        return $content;
    }

    private function addImport(string $content, string $fqcn): string
    {
        if (preg_match('/^use\s+'.preg_quote($fqcn, '/').'\s*;/m', $content)) {
            return $content;
        }

        // Insert after the last top-level use statement, or after the namespace declaration
        if (preg_match_all('/^use\s+[^;]+;\R/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
            [$lastUse, $offset] = end($matches[0]);
            $position = $offset + strlen($lastUse);

            return substr($content, 0, $position)."use {$fqcn};\n".substr($content, $position);
        }

        if (preg_match('/^namespace\s+[^;]+;\R/m', $content, $match, PREG_OFFSET_CAPTURE)) {
            $position = $match[0][1] + strlen($match[0][0]);

            return substr($content, 0, $position)."\nuse {$fqcn};\n".substr($content, $position);
        }

        return $content;
    }
}
