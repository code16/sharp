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

            // Commands moved out of EntityList (except QuickCreate); ReorderHandler moved to EntityList
            '/Code16\\\\Sharp\\\\EntityList\\\\Commands\\\\ReorderHandler\b/' => 'Code16\\\\Sharp\\\\EntityList\\\\ReorderHandler',
            '/Code16\\\\Sharp\\\\EntityList\\\\Commands\\\\(?!QuickCreate\\\\)/' => 'Code16\\\\Sharp\\\\Commands\\\\',

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

        // Positional / named bool arguments were replaced by fluent modifiers
        $content = $this->upgradeHelperBoolArgument($content, 'info', 'reload', 'withReload');
        $content = $this->upgradeHelperBoolArgument($content, 'link', 'openInNewTab', 'inNewTab');

        foreach (array_unique($imports) as $class) {
            $content = $this->addImport($content, 'Code16\\Sharp\\Commands\\Returns\\'.$class);
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

    /**
     * Rewrite `$this->info('msg', true)` / `$this->info('msg', reload: $x)`
     * to `$this->info('msg')->withReload()` / `$this->info('msg')->withReload($x)`.
     */
    private function upgradeHelperBoolArgument(string $content, string $helper, string $argName, string $modifier): string
    {
        $search = '$this->'.$helper.'(';
        $offset = 0;

        while (($start = strpos($content, $search, $offset)) !== false) {
            $argsStart = $start + strlen($search);
            $argsEnd = $this->findClosingParenthesis($content, $argsStart);

            if ($argsEnd === null) {
                break;
            }

            $args = $this->splitArguments(substr($content, $argsStart, $argsEnd - $argsStart));
            $offset = $argsEnd;

            if (count($args) !== 2) {
                continue;
            }

            $boolIndex = preg_match('/^'.$argName.'\s*:/', $args[0]) ? 0 : 1;
            $boolArg = preg_replace('/^'.$argName.'\s*:\s*/', '', $args[$boolIndex]);
            $call = $search.$args[1 - $boolIndex].')'.match (strtolower($boolArg)) {
                'true' => "->{$modifier}()",
                'false' => '',
                default => "->{$modifier}({$boolArg})",
            };

            $content = substr($content, 0, $start).$call.substr($content, $argsEnd + 1);
            $offset = $start + strlen($call);
        }

        return $content;
    }

    /**
     * Return the position of the parenthesis closing the one opened just before $position,
     * skipping strings and nested brackets.
     */
    private function findClosingParenthesis(string $content, int $position): ?int
    {
        $depth = 0;
        $length = strlen($content);

        for ($i = $position; $i < $length; $i++) {
            $char = $content[$i];

            if ($char === '"' || $char === "'") {
                $i = $this->skipString($content, $i);
            } elseif (in_array($char, ['(', '[', '{'])) {
                $depth++;
            } elseif (in_array($char, [')', ']', '}'])) {
                if ($depth === 0) {
                    return $char === ')' ? $i : null;
                }
                $depth--;
            }
        }

        return null;
    }

    /**
     * Split a raw argument list on its top-level commas.
     */
    private function splitArguments(string $args): array
    {
        $parts = [];
        $depth = 0;
        $current = '';
        $length = strlen($args);

        for ($i = 0; $i < $length; $i++) {
            $char = $args[$i];

            if ($char === '"' || $char === "'") {
                $end = $this->skipString($args, $i);
                $current .= substr($args, $i, $end - $i + 1);
                $i = $end;

                continue;
            }

            if (in_array($char, ['(', '[', '{'])) {
                $depth++;
            } elseif (in_array($char, [')', ']', '}'])) {
                $depth--;
            } elseif ($char === ',' && $depth === 0) {
                $parts[] = trim($current);
                $current = '';

                continue;
            }

            $current .= $char;
        }

        $parts[] = trim($current);

        // Allow trailing comma
        return array_values(array_filter($parts, fn ($part) => $part !== ''));
    }

    private function skipString(string $content, int $position): int
    {
        $quote = $content[$position];
        $length = strlen($content);

        for ($i = $position + 1; $i < $length; $i++) {
            if ($content[$i] === '\\') {
                $i++;
            } elseif ($content[$i] === $quote) {
                return $i;
            }
        }

        return $length - 1;
    }
}
