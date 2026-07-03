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
}
