<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Utils\Links\LinkToDashboard;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Links\LinkToSingleShowPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class GeneratorCommand extends Command
{
    protected $name = 'sharp:generator';
    protected $description = 'Prompt that helps you create entities, commands, filters, etc';

    public function handle()
    {
        $wizardType = select(
            label: 'What do you need?',
            options: ['A complete entity (with list, form, dashboard, etc)', 'A command', 'A list filter', 'An entity state', 'A reorder handler'],
            default: 'A complete entity (with list, form, dashboard, etc)',
        );

        switch ($wizardType) {
            default:
            case 'A complete entity (with list, form, etc)':
                $this->entityPrompt();
                break;
            case 'A command':
                $this->commandPrompt();
                break;
            case 'A list filter':
                $this->filterPrompt();
                break;
            case 'An entity state':
                $this->entityStatePrompt();
                break;
            case 'A reorder handler':
                $this->reorderHandlerPrompt();
                break;
        }

        return 0;
    }

    public function entityStatePrompt(): void
    {
        $name = text(
            label: 'What is the name of your entity state?',
            placeholder: 'E.g. Shipping',
            required: true,
            hint: 'An "EntityState" suffix will be added automatically (E.g. ShippingEntityState.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = search(
            'Looking for the related Sharp Entity',
            fn (string $value) => strlen($value) > 0
                ? $this->getSharpEntitiesList($value)
                : []
        );
        $entityStatePath = Str::plural($entityName).'\\States';

        $hasModel = confirm(
            label: 'Should the Entity State update an instance of an Eloquent model?',
        );

        if ($hasModel) {
            $modelNamespace = text(
                label: 'What is the namespace of your models?',
                default: 'App\\Models',
                required: true,
            );

            $model = search(
                'Looking for the related model',
                fn (string $value) => strlen($value) > 0
                    ? $this->getModelsList(base_path($this->namespaceToPath($modelNamespace)), $value)
                    : []
            );
            $model = $modelNamespace.'\\'.$model;

            if (! class_exists($model)) {
                $this->components->error(sprintf('Sorry the model class [%s] cannot be found', $model));

                exit(1);
            }
        }

        Artisan::call('sharp:make:entity-state', [
            'name' => $entityStatePath.'\\'.$name.'EntityState',
            ...($hasModel ? ['--model' => $model] : []),
        ]);

        $this->components->twoColumnDetail('Entity state', $this->getSharpRootNamespace().'\\'.$entityStatePath.'\\'.$name.'EntityState.php');

        $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'EntityList';

        if (! class_exists($listClass)) {
            $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'List';
        }

        if (class_exists($listClass)) {
            $this->addNewEntityStateToListOrShowPage(
                'List',
                $name.'EntityState',
                $this->getSharpRootNamespace().'\\'.$entityStatePath.'\\',
                $listClass,
            );

            $this->components->info(sprintf('The Entity State was successfully added to the related Entity List (%s).', $entityName.'EntityList'));
        }

        $showClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'Show';

        if (class_exists($showClass)) {
            $this->addNewEntityStateToListOrShowPage(
                'Show',
                $name.'EntityState',
                $this->getSharpRootNamespace().'\\'.$entityStatePath.'\\',
                $showClass,
            );

            $this->components->info(sprintf('The Entity State was successfully added to the related Show Page (%s).', $entityName.'Show'));
        }

        $this->components->info('Your Entity State has been created.');
    }

    public function filterPrompt(): void
    {
        $filterType = select(
            label: 'What is the type of the new Filter?',
            options: ['Select', 'Date range', 'Check'],
            default: 'Select',
        );

        $isMultiple = false;

        if ($filterType === 'Select') {
            $isMultiple = confirm(
                label: 'Can the Filter accept multiple values?',
                default: false,
            );
        }

        $isRequired = false;

        if ($filterType === 'Date range' || ($filterType === 'Select' && ! $isMultiple)) {
            $allowEmptyValues = confirm(
                label: 'Can the Filter accept empty value?',
            );
            $isRequired = ! $allowEmptyValues;
        }

        $name = text(
            label: 'What is the name of your Filter?',
            placeholder: 'E.g. ShippingState',
            required: true,
            hint: 'A "Filter" suffix will be added automatically (E.g. ShippingStateFilter.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = search(
            'Looking for the related Sharp Entity',
            fn (string $value) => strlen($value) > 0
                ? $this->getSharpEntitiesList($value)
                : []
        );
        $filterPath = Str::plural($entityName).'\\Filters';

        Artisan::call('sharp:make:entity-list-filter', [
            'name' => $filterPath.'\\'.$name.'Filter',
            ...($isRequired ? ['--required' => ''] : []),
            ...($isMultiple ? ['--multiple' => ''] : []),
            ...($filterType === 'Check' ? ['--check' => ''] : []),
            ...($filterType === 'Date range' ? ['--date-range' => ''] : []),
        ]);

        $this->components->twoColumnDetail(sprintf('%s filter', $filterType), $this->getSharpRootNamespace().'\\'.$filterPath.'\\'.$name.'Filter.php');

        $this->components->info('Your Filter has been created.');

        $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'EntityList';

        if (! class_exists($listClass)) {
            $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'List';
        }

        if (class_exists($listClass)) {
            $this->addNewItemToAListOfFilters(
                $name.'Filter',
                $this->getSharpRootNamespace().'\\'.$filterPath.'\\',
                $listClass,
            );

            $this->components->info(sprintf('The Filter has been successfully added to the related Entity List (%s).', $entityName.'EntityList'));
        }
    }

    public function commandPrompt(): void
    {
        $commandType = select(
            label: 'What is the type of the new Command?',
            options: ['Instance', 'Entity'],
            default: 'Instance',
        );

        $needsForm = confirm(
            label: 'Do you need a Form in the Command?',
            default: false,
        );

        if ($needsForm) {
            $needsWizard = confirm(
                label: 'Do you need a Wizard?',
                default: false,
                hint: 'A Wizard is a multi-step form.',
            );
        }

        $name = text(
            label: 'What is the name of your Command?',
            placeholder: 'E.g. SendResetPasswordEmail',
            required: true,
            hint: 'A "Command" suffix will be added automatically (E.g. SendResetPasswordEmailCommand.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = search(
            'Looking for the related Sharp Entity',
            fn (string $value) => strlen($value) > 0
                ? $this->getSharpEntitiesList($value)
                : []
        );

        $needsWizard = $needsWizard ?? false;
        $commandPath = Str::plural($entityName).'\\Commands';

        Artisan::call(sprintf('sharp:make:%s-command', Str::lower($commandType)), [
            'name' => $commandPath.'\\'.$name.'Command',
            ...(! $needsWizard && $needsForm ? ['--form' => ''] : []),
            ...($needsWizard ? ['--wizard' => ''] : []),
        ]);

        $this->components->twoColumnDetail(sprintf('%s Command', $commandType), $this->getSharpRootNamespace().'\\'.$commandPath.'\\'.$name.'Command.php');

        $this->components->info('Your Command has been created.');

        $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'EntityList';

        if (! class_exists($listClass)) {
            $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'List';
        }

        if (class_exists($listClass)) {
            $this->addNewItemToAListOfCommands(
                $commandType,
                $name.'Command',
                $this->getSharpRootNamespace().'\\'.$commandPath.'\\',
                $listClass,
            );

            $this->components->info(sprintf('The Command has been successfully added to the related Entity List (%s).', $entityName.'EntityList'));
        }

        $showClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'Show';

        if ($commandType === 'Instance' && class_exists($showClass)) {
            $this->addNewItemToAListOfCommands(
                $commandType,
                $name.'Command',
                $this->getSharpRootNamespace().'\\'.$commandPath.'\\',
                $showClass,
            );

            $this->components->info(sprintf('The Command has been successfully added to the related Show Page (%s).', $entityName.'Show'));
        }
    }

    public function entityPrompt(): void
    {
        $entityType = select(
            label: 'What is the type of your Entity?',
            options: ['Regular', 'Single', 'Dashboard'],
            default: 'Regular',
        );

        switch ($entityType) {
            case 'Regular':
                [$entityPath, $entityKey, $entityConfigKey] = $this->generateRegularEntity();
                break;
            case 'Single':
                [$entityPath, $entityKey, $entityConfigKey] = $this->generateSingleEntity();
                break;
            case 'Dashboard':
                [$entityPath, $entityKey, $entityConfigKey] = $this->generateDashboardEntity();
                break;
        }

        $this->components->info('Your Entity and all related files have been created.');

        $this->addNewEntityToSharpConfig($entityPath, $entityKey, $entityConfigKey);

        $this->components->info(
            sprintf(
                'Your Entity has been successfully added to entities list in `sharp/config.php`. You can visit: %s',
                match ($entityType) {
                    'Classic' => LinkToEntityList::make($entityKey)->renderAsUrl(),
                    'Single' => LinkToSingleShowPage::make($entityKey)->renderAsUrl(),
                    'Dashboard' => LinkToDashboard::make($entityKey)->renderAsUrl(),
                    default => 'unknown url',
                },
            )
        );
    }

    protected function generateDashboardEntity(): array
    {
        $name = text(
            label: 'What is the name of your Dashboard?',
            placeholder: 'E.g. Activity',
            required: true,
            hint: 'A "DashboardEntity" suffix will be added automatically (E.g. ActivityDashboardEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $needsPolicy = confirm(
            label: 'Do you need a Policy?',
            default: false,
        );

        Artisan::call('sharp:make:dashboard', [
            'name' => 'Dashboards\\'.$name.'Dashboard',
        ]);

        $this->components->twoColumnDetail('Dashboard', $this->getSharpRootNamespace().'\\Dashboards\\'.$name.'Dashboard.php');

        if ($needsPolicy) {
            Artisan::call('sharp:make:policy', [
                'name' => 'Dashboards\\'.$name.'DashboardPolicy',
                '--entity-only' => '',
            ]);

            $this->components->twoColumnDetail('Policy', $this->getSharpRootNamespace().'\\Dashboards\\'.$name.'DashboardPolicy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\'.$name.'DashboardEntity',
            '--dashboard' => '',
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $this->getSharpRootNamespace().'\\Entities\\'.$name.'DashboardEntity.php');

        return [
            $this->getSharpRootNamespace().'\\Entities\\'.$name.'DashboardEntity',
            Str::snake($name),
            'dashboards',
        ];
    }

    protected function generateRegularEntity(): array
    {
        $name = text(
            label: 'What is the name of your Entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'An "Entity" suffix will be added automatically (E.g. UserEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));
        $pluralName = Str::plural($name);

        $modelNamespace = text(
            label: 'What is the namespace of your models?',
            default: 'App\\Models',
            required: true,
        );

        if (app()->runningUnitTests()) {
            $model = 'Code16\\Sharp\\Tests\\Fixtures\\ClosedPeriod';
        } else {
            $model = search(
                'Looking for the related model',
                fn (string $value) => strlen($value) > 0
                    ? $this->getModelsList(base_path($this->namespaceToPath($modelNamespace)), $value)
                    : []
            );
            $model = $modelNamespace.'\\'.$model;
        }

        if (! class_exists($model)) {
            $this->components->error(sprintf('Sorry the model class [%s] cannot be found', $model));
            exit(1);
        }

        $label = text(
            label: 'What is the label of your Entity?',
            placeholder: 'E.g. Administrators',
            required: true,
            hint: 'It will be displayed in the breadcrumb'
        );

        $type = select(
            label: 'What do you need with your Entity?',
            options: ['Entity List', 'Entity List & Form', 'Entity List & Show Page', 'Entity List, Form & Show Page'],
            default: 'Entity List, Form & Show Page',
        );

        $needsPolicy = confirm(
            label: 'Do you need a Policy?',
            default: false,
        );

        Artisan::call('sharp:make:entity-list', [
            'name' => $pluralName.'\\'.$name.'EntityList',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Entity List', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'EntityList.php');

        if (Str::contains($type, 'form')) {
            Artisan::call('sharp:make:form', [
                'name' => $pluralName.'\\'.$name.'Form',
                '--model' => $model,
            ]);

            $this->components->twoColumnDetail('Form', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Form.php');
        }

        if (Str::contains($type, 'show')) {
            Artisan::call('sharp:make:show-page', [
                'name' => $pluralName.'\\'.$name.'Show',
                '--model' => $model,
            ]);

            $this->components->twoColumnDetail('Show Page', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Show.php');
        }

        if ($needsPolicy) {
            Artisan::call('sharp:make:policy', [
                'name' => $pluralName.'\\'.$name.'Policy',
            ]);

            $this->components->twoColumnDetail('Policy', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Policy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\'.$name.'Entity',
            '--label' => $label,
            ...(Str::contains($type, 'form') ? ['--form' => ''] : []),
            ...(Str::contains($type, 'show') ? ['--show' => ''] : []),
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $this->getSharpRootNamespace().'\\Entities\\'.$name.'Entity.php');

        return [
            $this->getSharpRootNamespace().'\\Entities\\'.$name.'Entity',
            Str::snake($pluralName),
            'entities',
        ];
    }

    private function generateSingleEntity(): array
    {
        $name = text(
            label: 'What is the name of your Entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'An "Entity" suffix will be added automatically (E.g. UserEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $label = text(
            label: 'What is the label of your Entity?',
            placeholder: 'E.g. Administrators',
            required: true,
            hint: 'It will be displayed in the breadcrumb'
        );

        $needsPolicy = confirm(
            label: 'Do you need a Policy?',
            default: false,
        );

        Artisan::call('sharp:make:form', [
            'name' => $name.'\\'.$name.'SingleForm',
            '--single' => '',
        ]);

        $this->components->twoColumnDetail('Single form', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'SingleForm.php');

        Artisan::call('sharp:make:show-page', [
            'name' => $name.'\\'.$name.'SingleShow',
            '--single' => '',
        ]);

        $this->components->twoColumnDetail('Single Show Page', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'SingleShow.php');

        if ($needsPolicy) {
            Artisan::call('sharp:make:policy', [
                'name' => $name.'\\'.$name.'Policy',
            ]);

            $this->components->twoColumnDetail('Policy', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'Policy.php');
        }

        Artisan::call('sharp:make:entity', [
            'name' => 'Entities\\'.$name.'Entity',
            '--label' => $label,
            '--single' => '',
            ...($needsPolicy ? ['--policy' => ''] : []),
        ]);

        $this->components->twoColumnDetail('Entity', $this->getSharpRootNamespace().'\\Entities\\'.$name.'Entity.php');

        return [
            $this->getSharpRootNamespace().'\\Entities\\'.$name.'Entity',
            Str::snake($name),
            'entities',
        ];
    }

    private function getSharpRootNamespace(): string
    {
        return $this->laravel->getNamespace().'Sharp';
    }

    private function getModelsList(string $dir, ?string $search = null): array
    {
        return collect(File::allFiles($dir))
            ->map(fn ($class) => str_replace(
                [$dir, '/', '.php'],
                ['', '', ''],
                $class->getRealPath()
            ))
            ->filter(fn ($class) => !$search || Str::contains($class, $search))
            ->values()
            ->toArray();
    }

    private function getSharpEntitiesList(?string $search = null): array
    {
        return collect(config('sharp.entities'))
            ->map(fn ($class) => str_replace(
                ['App\Sharp\Entities\\', 'Entity'],
                ['', ''],
                $class,
            ))
            ->filter(fn ($class) => !$search || Str::contains($class, $search))
            ->values()
            ->toArray();
    }

    /** @deprecated  */
    private function addNewEntityToSharpConfig(string $entityPath, string $entityKey, string $entityConfigKey): void
    {
        $this->replaceFileContent(
            config_path('sharp.php'),
            "'$entityConfigKey' => [".PHP_EOL,
            "'$entityConfigKey' => [".PHP_EOL."        '$entityKey' => \\$entityPath::class,".PHP_EOL,
        );
    }

    private function addNewItemToAListOfCommands(string $commandType, string $commandClass, string $commandPath, string $targetClass): void
    {
        $classMethodName = sprintf('get%sCommands', $commandType);
        $reflector = new ReflectionClass($targetClass);

        $this->replaceFileContent(
            $reflector->getFileName(),
            PHP_EOL.'class ',
            'use '.$commandPath.$commandClass.';'.PHP_EOL.PHP_EOL.'class ',
        );

        $this->replaceFileContent(
            $reflector->getFileName(),
            "$classMethodName(): ?array".PHP_EOL.'    {'.PHP_EOL.'        return ['.PHP_EOL,
            "$classMethodName(): ?array".PHP_EOL.'    {'.PHP_EOL.'        return ['.PHP_EOL.'            '.$commandClass.'::class,'.PHP_EOL,
        );
    }

    private function addNewItemToAListOfFilters(string $filterClass, string $filterPath, string $targetClass): void
    {
        $reflector = new ReflectionClass($targetClass);

        $this->replaceFileContent(
            $reflector->getFileName(),
            PHP_EOL.'class ',
            'use '.$filterPath.$filterClass.';'.PHP_EOL.PHP_EOL.'class ',
        );

        $this->replaceFileContent(
            $reflector->getFileName(),
            'getFilters(): ?array'.PHP_EOL.'    {'.PHP_EOL.'        return ['.PHP_EOL,
            'getFilters(): ?array'.PHP_EOL.'    {'.PHP_EOL.'        return ['.PHP_EOL.'            '.$filterClass.'::class,'.PHP_EOL,
        );
    }

    private function addNewEntityStateToListOrShowPage(string $targetType, string $entityStateClass, string $entityStatePath, string $targetClass): void
    {
        $classMethodName = sprintf('build%sConfig', $targetType);
        $reflector = new ReflectionClass($targetClass);

        $this->replaceFileContent(
            $reflector->getFileName(),
            PHP_EOL.'class ',
            'use '.$entityStatePath.$entityStateClass.';'.PHP_EOL.PHP_EOL.'class ',
        );

        $this->replaceFileContent(
            $reflector->getFileName(),
            "$classMethodName(): void".PHP_EOL.'    {'.PHP_EOL.'        $this'.PHP_EOL,
            "$classMethodName(): void".PHP_EOL.'    {'.PHP_EOL.'        $this'.PHP_EOL."            ->configureEntityState('state',".$entityStateClass.'::class)'.PHP_EOL,
        );
    }

    private function addNewReorderHandlerToList(string $reorderHandlerClass, string $reorderHandlerPath, string $targetClass): void
    {
        $reflector = new ReflectionClass($targetClass);

        $this->replaceFileContent(
            $reflector->getFileName(),
            PHP_EOL.'class ',
            'use '.$reorderHandlerPath.$reorderHandlerClass.';'.PHP_EOL.PHP_EOL.'class ',
        );

        $this->replaceFileContent(
            $reflector->getFileName(),
            'buildListConfig(): void'.PHP_EOL.'    {'.PHP_EOL.'        $this'.PHP_EOL,
            'buildListConfig(): void'.PHP_EOL.'    {'.PHP_EOL.'        $this'.PHP_EOL.'            ->configureReorderable('.$reorderHandlerClass.'::class)'.PHP_EOL,
        );
    }

    private function addNewSimpleEloquentReorderHandlerToList(string $reorderAttribute, string $modelClass, string $modelPath, string $targetClass): void
    {
        $reflector = new ReflectionClass($targetClass);

        $this->replaceFileContent(
            $reflector->getFileName(),
            PHP_EOL.'class ',
            'use '.$modelPath.$modelClass.';'.PHP_EOL.'use Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler;'.PHP_EOL.PHP_EOL.'class ',
        );

        $this->replaceFileContent(
            $reflector->getFileName(),
            'buildListConfig(): void'.PHP_EOL.'    {'.PHP_EOL.'        $this'.PHP_EOL,
            'buildListConfig(): void'.PHP_EOL.'    {'.PHP_EOL.'        $this'.PHP_EOL.'            ->configureReorderable('.PHP_EOL.'                (new SimpleEloquentReorderHandler('.$modelClass.'::class))'.PHP_EOL."                    ->setOrderAttribute('".$reorderAttribute."')".PHP_EOL.'            )'.PHP_EOL,
        );
    }

    private function reorderHandlerPrompt(): void
    {
        $entityName = search(
            'Looking for the related Sharp Entity',
            fn (string $value) => strlen($value) > 0
                ? $this->getSharpEntitiesList($value)
                : []
        );
        $reorderPath = Str::plural($entityName).'\\ReorderHandlers';

        $modelNamespace = text(
            label: 'What is the namespace of your models?',
            default: 'App\\Models',
            required: true,
        );

        $modelName = search(
            'Search for the related model',
            fn (string $value) => strlen($value) > 0
                ? $this->getModelsList(base_path($this->namespaceToPath($modelNamespace)), $value)
                : []
        );
        $model = $modelNamespace.'\\'.$modelName;

        $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'EntityList';

        if (! class_exists($listClass)) {
            $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'List';
        }

        $isSimple = confirm(
            label: 'Use the simple Eloquent implementation based on a reorder attribute?',
        );

        if ($isSimple) {
            $reorderAttribute = text(
                label: 'What is the name of your reorder attribute?',
                default: 'order',
                required: true,
            );

            if (class_exists($listClass)) {
                $this->addNewSimpleEloquentReorderHandlerToList(
                    $reorderAttribute,
                    $modelName,
                    $modelNamespace.'\\',
                    $listClass,
                );

                $this->components->info(sprintf('The simple eloquent reorder handler has been successfully added to the related entity list (%s).', $entityName.'EntityList'));
            }

            return;
        }

        $name = text(
            label: 'What is the name of your reorder handler?',
            placeholder: 'E.g. ProductVisual',
            required: true,
            hint: 'A "Reorder" suffix will be added automatically (E.g. ProductVisualReorder.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        Artisan::call('sharp:make:reorder-handler', [
            'name' => $reorderPath.'\\'.$name.'Reorder',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Reorder handler', $this->getSharpRootNamespace().'\\'.$reorderPath.'\\'.$name.'Reorder.php');

        $this->components->info('Your Reorder Handler has been created.');

        if (class_exists($listClass)) {
            $this->addNewReorderHandlerToList(
                $name.'Reorder',
                $this->getSharpRootNamespace().'\\'.$reorderPath.'\\',
                $listClass,
            );

            $this->components->info(sprintf('The Reorder Handler has been successfully added to the related Entity List (%s).', $entityName.'EntityList'));
        }
    }

    private function replaceFileContent($targetFilePath, $search, $replace): void
    {
        $targetContent = file_get_contents($targetFilePath);

        file_put_contents(
            $targetFilePath,
            str_replace($search, $replace, $targetContent)
        );
    }

    private function namespaceToPath(string $namespace): string
    {
        return Str::lcfirst(str_replace('\\', '/', $namespace));
    }
}
