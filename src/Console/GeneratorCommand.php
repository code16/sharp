<?php

namespace Code16\Sharp\Console;

use Archetype\Facades\LaravelFile;
use Code16\Sharp\Utils\Links\LinkToDashboard;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Links\LinkToSingleShowPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
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

    public function entityStatePrompt()
    {
        $name = text(
            label: 'What is the name of your entity state?',
            placeholder: 'E.g. Shipping',
            required: true,
            hint: 'An "EntityState" suffix will be added automatically (E.g. ShippingEntityState.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = search(
            'Search for the related sharp entity',
            fn (string $value) => strlen($value) > 0
                ? $this->getSharpEntitiesList($value)
                : []
        );
        $entityStatePath = Str::plural($entityName).'\\States';

        $modelNamespace = text(
            label: 'What is the namespace of your models?',
            default: 'App\\Models',
            required: true,
        );

        $model = search(
            'Search for the related model',
            fn (string $value) => strlen($value) > 0
                ? $this->getModelsList(base_path($this->namespaceToPath($modelNamespace)), $value)
                : []
        );
        $model = $modelNamespace.'\\'.$model;

        if (! class_exists($model)) {
            $this->components->error(sprintf('Sorry the model class [%s] cannot be found', $model));

            exit(1);
        }

        Artisan::call('sharp:make:entity-state', [
            'name' => $entityStatePath.'\\'.$name.'EntityState',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Entity state', $this->getSharpRootNamespace().'\\'.$entityStatePath.'\\'.$name.'EntityState.php');

        $this->components->info('Your entity state has been created successfully.');
    }

    public function filterPrompt()
    {
        $filterType = select(
            label: 'What is the type of the new filter?',
            options: ['Select', 'Date range', 'Check'],
            default: 'Select',
        );

        $isMultiple = false;

        if ($filterType === 'Select') {
            $isMultiple = confirm(
                label: 'Can the filter accept multiple values?',
                default: false,
            );
        }

        $isRequired = false;

        if ($filterType === 'Date range' || ($filterType === 'Select' && ! $isMultiple)) {
            $allowEmptyValues = confirm(
                label: 'Can the filter accept empty value?',
            );
            $isRequired = ! $allowEmptyValues;
        }

        $name = text(
            label: 'What is the name of your filter?',
            placeholder: 'E.g. ShippingState',
            required: true,
            hint: 'A "Filter" suffix will be added automatically (E.g. ShippingStateFilter.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = search(
            'Search for the related sharp entity',
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

        $this->components->info('Your filter has been created successfully.');
    }

    public function commandPrompt()
    {
        $commandType = select(
            label: 'What is the type of the new command?',
            options: ['Instance', 'Entity'],
            default: 'Instance',
        );

        $needsForm = confirm(
            label: 'Do you need a form inside the command?',
            default: false,
        );

        if ($needsForm) {
            $needsWizard = confirm(
                label: 'Do you need a wizard?',
                default: false,
                hint: 'A wizard is a multi-step form.',
            );
        }

        $name = text(
            label: 'What is the name of your command?',
            placeholder: 'E.g. SendResetPasswordEmail',
            required: true,
            hint: 'A "Command" suffix will be added automatically (E.g. SendResetPasswordEmailCommand.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = search(
            'Search for the related sharp entity',
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

        $this->components->twoColumnDetail(sprintf('%s command', $commandType), $this->getSharpRootNamespace().'\\'.$commandPath.'\\'.$name.'Command.php');

        $this->components->info('Your command has been created successfully.');

        $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'EntityList';

        if (!class_exists($listClass)) {
            $listClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'List';
        }

        if (class_exists($listClass)) {
            $this->addNewItemToAListOfCommands(
                $commandType,
                $name.'Command',
                $this->getSharpRootNamespace().'\\'.$commandPath.'\\',
                $listClass,
            );

            $this->components->info(sprintf('The command has been successfully added to the related entity list (%s).', $entityName.'EntityList'));
        }

        $showClass = $this->getSharpRootNamespace().'\\'.Str::plural($entityName).'\\'.$entityName.'Show';

        if ($commandType === 'Instance' && class_exists($showClass)) {
            $this->addNewItemToAListOfCommands(
                $commandType,
                $name.'Command',
                $this->getSharpRootNamespace().'\\'.$commandPath.'\\',
                $showClass,
            );

            $this->components->info(sprintf('The command has been successfully added to the related show page (%s).', $entityName.'Show'));
        }
    }

    public function entityPrompt()
    {
        $entityType = select(
            label: 'What is the type of your entity?',
            options: ['Classic', 'Single', 'Dashboard'],
            default: 'Classic',
        );

        switch ($entityType) {
            case 'Classic':
                [$entityPath, $entityKey, $entityConfigKey] = $this->generateClassicEntity();
                break;
            case 'Single':
                [$entityPath, $entityKey, $entityConfigKey] = $this->generateSingleEntity();
                break;
            case 'Dashboard':
                [$entityPath, $entityKey, $entityConfigKey] = $this->generateDashboardEntity();
                break;
        }

        $this->components->info('Your entity and all related files have been created successfully.');

        $this->addNewEntityToSharpConfig($entityPath, $entityKey, $entityConfigKey);

        $this->components->info(
            sprintf(
                'Your entity has been successfully added to entities list in `sharp/config.php`. You can visit: %s',
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
            label: 'What is the name of your dashboard?',
            placeholder: 'E.g. Activity',
            required: true,
            hint: 'A "DashboardEntity" suffix will be added automatically (E.g. ActivityDashboardEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $needsPolicy = confirm(
            label: 'Do you need a policy?',
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

    protected function generateClassicEntity(): array
    {
        $name = text(
            label: 'What is the name of your entity?',
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
                'Search for the related model',
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
            label: 'What is the label of your entity?',
            placeholder: 'E.g. Administrators',
            required: true,
            hint: 'It will be displayed in the breadcrumb'
        );

        $type = select(
            label: 'What do you need with your entity?',
            options: ['List', 'List & form', 'List & show', 'List, form & show page'],
            default: 'List, form & show page',
        );

        $needsPolicy = confirm(
            label: 'Do you need a policy?',
            default: false,
        );

        Artisan::call('sharp:make:entity-list', [
            'name' => $pluralName.'\\'.$name.'EntityList',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Entity list', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'EntityList.php');

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

            $this->components->twoColumnDetail('Show page', $this->getSharpRootNamespace().'\\'.$pluralName.'\\'.$name.'Show.php');
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
            label: 'What is the name of your entity?',
            placeholder: 'E.g. User',
            required: true,
            hint: 'An "Entity" suffix will be added automatically (E.g. UserEntity.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $label = text(
            label: 'What is the label of your entity?',
            placeholder: 'E.g. Administrators',
            required: true,
            hint: 'It will be displayed in the breadcrumb'
        );

        $needsPolicy = confirm(
            label: 'Do you need a policy?',
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

        $this->components->twoColumnDetail('Single show page', $this->getSharpRootNamespace().'\\'.$name.'\\'.$name.'SingleShow.php');

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

    private function getSharpRootNamespace()
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
            ->filter(fn ($class) => $search ? Str::contains($class, $search) : true)
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
            ->filter(fn ($class) => $search ? Str::contains($class, $search) : true)
            ->values()
            ->toArray();
    }

    private function addNewEntityToSharpConfig(string $entityPath, string $entityKey, string $entityConfigKey)
    {
        $sharpConfig = file_get_contents(config_path('sharp.php'));

        file_put_contents(config_path('sharp.php'), str_replace(
            "'$entityConfigKey' => [".PHP_EOL,
            "'$entityConfigKey' => [".PHP_EOL."        '$entityKey' => \\$entityPath::class,".PHP_EOL,
            $sharpConfig
        ));
    }

    private function addNewItemToAListOfCommands(string $commandType, string $commandClass, string $commandPath, string $targetClass)
    {
        $classMethodName = sprintf('get%sCommands', $commandType);

        $reflector = new ReflectionClass($targetClass);
        $targetContent = file_get_contents($reflector->getFileName());

        file_put_contents($reflector->getFileName(), str_replace(
            PHP_EOL."class ",
            "use ".$commandPath.$commandClass.";".PHP_EOL.PHP_EOL."class ",
            $targetContent
        ));

        $targetContent = file_get_contents($reflector->getFileName());

        file_put_contents($reflector->getFileName(), str_replace(
            "$classMethodName(): ?array".PHP_EOL."    {".PHP_EOL."        return [".PHP_EOL,
            "$classMethodName(): ?array".PHP_EOL."    {".PHP_EOL."        return [".PHP_EOL."            ".$commandClass."::class,".PHP_EOL,
            $targetContent
        ));
    }

    private function namespaceToPath($namespace)
    {
        return Str::lcfirst(str_replace('\\', '/', $namespace));
    }

    private function reorderHandlerPrompt()
    {
        $name = text(
            label: 'What is the name of your reorder handler?',
            placeholder: 'E.g. ProductVisual',
            required: true,
            hint: 'A "Reorder" suffix will be added automatically (E.g. ProductVisualReorder.php).',
        );
        $name = Str::ucfirst(Str::camel($name));

        $entityName = search(
            'Search for the related sharp entity',
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

        $model = search(
            'Search for the related model',
            fn (string $value) => strlen($value) > 0
                ? $this->getModelsList(base_path($this->namespaceToPath($modelNamespace)), $value)
                : []
        );
        $model = $modelNamespace.'\\'.$model;

        Artisan::call('sharp:make:reorder-handler', [
            'name' => $reorderPath.'\\'.$name.'Reorder',
            '--model' => $model,
        ]);

        $this->components->twoColumnDetail('Reorder handler', $this->getSharpRootNamespace().'\\'.$reorderPath.'\\'.$name.'Reorder.php');

        $this->components->info('Your reorder handler has been created successfully.');
    }
}
