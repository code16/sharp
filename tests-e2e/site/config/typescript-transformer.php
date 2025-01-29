<?php

return [
    /*
     * The paths where typescript-transformer will look for PHP classes
     * to transform, this will be the `app` path by default.
     */

    'auto_discover_types' => [
        app_path('Data'),
    ],

    /*
     * Collectors will search for classes in the `auto_discover_types` paths and choose the correct
     * transformer to transform them. By default, we include a DefaultCollector which will search
     * for @typescript annotated and #[TypeScript] attributed classes to transform.
     */

    'collectors' => [
        Spatie\TypeScriptTransformer\Collectors\DefaultCollector::class,
        Spatie\TypeScriptTransformer\Collectors\EnumCollector::class,
    ],

    /*
     * Transformers take PHP classes(e.g., enums) as an input and will output
     * a TypeScript representation of the PHP class.
     */

    'transformers' => [
        Spatie\TypeScriptTransformer\Transformers\EnumTransformer::class,
        Spatie\LaravelTypeScriptTransformer\Transformers\DtoTransformer::class,
    ],

    /*
     * In your classes, you sometimes have types that should always be replaced
     * by the same TypeScript representations. For example, you can replace a
     * Datetime always with a string. You define these replacements here.
     */

    'default_type_replacements' => [
        DateTime::class => 'string',
        DateTimeImmutable::class => 'string',
        Carbon\CarbonInterface::class => 'string',
        Carbon\CarbonImmutable::class => 'string',
        Carbon\Carbon::class => 'string',
    ],

    /*
     * The package will write the generated TypeScript to this file.
     */

    'output_file' => resource_path('types/generated.d.ts'),

    /*
     * When the package is writing types to the output file, a writer is used to
     * determine the format. By default, this is the `TypeDefinitionWriter`.
     * But you can also use the `ModuleWriter` or implement your own.
     */

    'writer' => Spatie\TypeScriptTransformer\Writers\ModuleWriter::class,

    /*
     * The generated TypeScript file can be formatted. We ship a Prettier formatter
     * out of the box: `PrettierFormatter` but you can also implement your own one.
     * The generated TypeScript will not be formatted when no formatter was set.
     */

    'formatter' => \Spatie\TypeScriptTransformer\Formatters\PrettierFormatter::class,

    /*
     * Enums can be transformed into types or native TypeScript enums, by default
     * the package will transform them to types.
     */

    'transform_to_native_enums' => false,

    /*
     * By default, this package will convert PHP nullable properties to TypeScript
     * types using a `null` type union. Setting `transform_null_to_optional` will
     * make them optional instead.
     */

    'transform_null_to_optional' => false,
];
