<?php

namespace Code16\Sharp\Console\Dev;

use Code16\Sharp\Console\Dev\TypeScriptTransformer\DataTypeScriptCollector;
use Code16\Sharp\Console\Dev\TypeScriptTransformer\DataTypeScriptTransformer;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\TypeScriptTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Based on https://github.com/spatie/laravel-typescript-transformer/blob/main/src/Commands/TypeScriptTransformCommand.php
 */
class TypeScriptTransformCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = __DIR__ . '/../../..';

        $config = \Spatie\TypeScriptTransformer\TypeScriptTransformerConfig::create()
            ->autoDiscoverTypes($basePath . '/src/Data')
            ->collectors([
                DataTypeScriptCollector::class,
                \Spatie\TypeScriptTransformer\Collectors\EnumCollector::class,
            ])
            ->transformers([
                \Spatie\TypeScriptTransformer\Transformers\EnumTransformer::class,
            ])
            ->outputFile($basePath . '/resources/types/generated.d.ts');

        $transformer = new TypeScriptTransformer($config);

        try {
            $collection = $transformer->transform();
        } catch (Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");

            return 1;
        }

        $table = new Table($output);

        $table->setHeaders(['PHP class', 'TypeScript entity']);
        $table->setRows(
            Collection::make($collection)->map(fn (TransformedType $type, string $class) => [
                $class, $type->getTypeScriptName(),
            ])->toArray()
        );
        $table->render();

        $output->writeln("<info>Transformed {$collection->count()} PHP types to TypeScript</info>");

        return 0;
    }
}
