<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    protected $name = 'sharp:install';
    protected $description = 'Sharp onboarding command';

    public function handle()
    {
        $this->createSharpServiceProvider();
        $this->publishAssets();
        $this->initSharpMenu();

        $this->components->info('Sharp has been installed!');
        $this->components->info('You can now generate your first Sharp Entity with [php artisan sharp:generator]');

        return Command::SUCCESS;
    }

    private function createSharpServiceProvider(): void
    {
        Artisan::call('sharp:make:provider', [
            'name' => 'SharpServiceProvider',
        ]);

        $this->registerSharpProvider();

        $this->components->twoColumnDetail('Service provider', app_path('Providers/SharpServiceProvider.php'));
    }

    private function publishAssets(): void
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

    private function initSharpMenu(): void
    {
        Artisan::call('sharp:make:menu', [
            'name' => 'SharpMenu',
        ]);

        $this->addSharpMenuToServiceProvider();

        $this->components->twoColumnDetail('Menu', $this->getSharpRootNamespace() . '\\SharpMenu.php');
    }

    private function getSharpRootNamespace(): string
    {
        return $this->laravel->getNamespace().'Sharp';
    }

    private function addSharpMenuToServiceProvider(): void
    {
        $this->replaceFileContent(
            app_path('Providers/SharpServiceProvider.php'),
            'use Code16\Sharp\SharpAppServiceProvider;'.PHP_EOL,
            'use Code16\Sharp\SharpAppServiceProvider;'.PHP_EOL.'use '.$this->getSharpRootNamespace().'\\SharpMenu;'.PHP_EOL,
        );

        $this->replaceFileContent(
            app_path('Providers/SharpServiceProvider.php'),
            '->setName(\'My new project\');'.PHP_EOL,
            '->setName(\'My new project\')'.PHP_EOL.'            ->setSharpMenu(SharpMenu::class);'.PHP_EOL
        );
    }

    private function replaceFileContent(string $targetFilePath, string $search, string $replace): void
    {
        $targetContent = file_get_contents($targetFilePath);

        file_put_contents(
            $targetFilePath,
            str_replace($search, $replace, $targetContent)
        );
    }

    private function registerSharpProvider(): void
    {
        $this->replaceFileContent(
            app_path('Providers/AppServiceProvider.php'),
            'public function boot(): void'.PHP_EOL.'    {'.PHP_EOL,
            'public function boot(): void'.PHP_EOL.'    {'.PHP_EOL.'        $this->app->register(SharpServiceProvider::class);'.PHP_EOL,
        );
    }
}