<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use function Laravel\Prompts\confirm;

class InstallCommand extends Command
{
    protected $name = 'sharp:install';
    protected $description = 'An onboarding that helps you start with your sharp setup';

    public function handle()
    {
        $this->components->warn('By default, Sharp will be accessible to all users that can login with their email and password.');

        $shouldCreateCheckHandler = confirm(
            label: 'Should create a custom check handler to restrict Sharp access only to specific users (admins, etc) ?',
        );

        $this->publishConfig();
        $this->publishAssets();
        $this->initSharpMenu();
        if ($shouldCreateCheckHandler) {
            $this->createCheckHandler();
        }
        $this->components->info('Sharp has been installed! You can now generate your first sharp entity with the help of an artisan command. Use `artisan sharp:generator`');

        return Command::SUCCESS;
    }

    private function publishConfig()
    {
        Artisan::call('vendor:publish', [
            '--provider' => 'Code16\Sharp\SharpServiceProvider',
            '--tag' => 'config',
        ]);

        $this->components->twoColumnDetail('Config', 'config/sharp.php');
    }

    private function publishAssets()
    {
        Artisan::call('vendor:publish', [
            '--provider' => '"Code16\Sharp\SharpServiceProvider"',
            '--tag' => 'assets',
        ]);

        $this->components->twoColumnDetail('Assets', 'public/vendor/sharp');

        $this->replaceFileContent(
            base_path('composer.json'),
            'ComposerScripts::postAutoloadDump",'.PHP_EOL,
            'ComposerScripts::postAutoloadDump",'.PHP_EOL.'            "@php artisan vendor:publish --provider=Code16\\\\\\\\Sharp\\\\\\\\SharpServiceProvider --tag=assets --force",'.PHP_EOL,
        );

        $this->components->twoColumnDetail('Post autoload script', 'composer.json');

        $this->replaceFileContent(
            base_path('.gitignore'),
            PHP_EOL.'/vendor'.PHP_EOL,
            PHP_EOL.'/vendor'.PHP_EOL.'/public/vendor'.PHP_EOL,
        );

        $this->components->twoColumnDetail('Ignore assets in git', '.gitignore');
    }

    private function replaceFileContent($targetFilePath, $search, $replace)
    {
        $targetContent = file_get_contents($targetFilePath);

        file_put_contents($targetFilePath, str_replace(
            $search,
            $replace,
            $targetContent
        ));
    }

    private function initSharpMenu()
    {
        Artisan::call('sharp:make:menu', [
            'name' => 'SharpMenu',
        ]);

        $this->addMenuToSharpConfig();

        $this->components->twoColumnDetail('Menu', $this->getSharpRootNamespace() . "\\SharpMenu.php");
    }

    private function getSharpRootNamespace()
    {
        return $this->laravel->getNamespace().'Sharp';
    }

    private function addMenuToSharpConfig()
    {
        $this->replaceFileContent(
            config_path('sharp.php'),
            "'menu' => null, //\\App\\Sharp\\SharpMenu::class",
            "'menu' => " . $this->getSharpRootNamespace() . "\\SharpMenu::class,",
        );
    }

    private function createCheckHandler()
    {
        Artisan::call('sharp:make:check-handler', [
            'name' => 'SharpCheckHandler',
        ]);

        $this->addCheckHandlerToSharpConfig();

        $this->components->twoColumnDetail('Custom check handler', $this->getSharpRootNamespace() . "\\SharpCheckHandler.php");
    }

    private function addCheckHandlerToSharpConfig()
    {
        $this->replaceFileContent(
            config_path('sharp.php'),
            "'auth' => [".PHP_EOL,
            "'auth' => [".PHP_EOL."        'check_handler' => ".$this->getSharpRootNamespace()."\\SharpCheckHandler::class,".PHP_EOL,
        );
    }
}
