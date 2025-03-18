<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $name = 'sharp:install';
    protected $description = 'Sharp onboarding command';

    public function handle()
    {
        $this->createSharpServiceProvider();
        $this->publishAssets();
        $this->initSharpMenu();

        $this->components->info('Sharp has been installed! You can now generate your first Sharp Entity with [php artisan sharp:generator]');

        return Command::SUCCESS;
    }

    private function createSharpServiceProvider(): void
    {
        $this->call('sharp:make:provider', [
            'name' => 'SharpServiceProvider',
        ]);

        $this->registerSharpProvider();
    }

    private function publishAssets(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'sharp-assets',
        ]);

        $this->components->info('Sharp assets published');

        $this->replaceFileContent(
            base_path('composer.json'),
            'ComposerScripts::postAutoloadDump",'.PHP_EOL,
            'ComposerScripts::postAutoloadDump",'.PHP_EOL.'            "@php artisan vendor:publish --tag=sharp-assets --force",'.PHP_EOL,
        );

        $this->components->info('Sharp assets autoload script added to composer.json');

        $this->replaceFileContent(
            base_path('.gitignore'),
            PHP_EOL.'/vendor'.PHP_EOL,
            PHP_EOL.'/vendor'.PHP_EOL.'/public/vendor'.PHP_EOL,
        );

        $this->components->info('Sharp assets added to .gitignore');
    }

    private function initSharpMenu(): void
    {
        $this->call('sharp:make:menu', [
            'name' => 'SharpMenu',
        ]);

        $this->addSharpMenuToServiceProvider();
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
            '->setName(\'My new project\')'.PHP_EOL,
            '->setName(\'My new project\')'.PHP_EOL.'            ->setSharpMenu(SharpMenu::class);'.PHP_EOL
        );
    }

    private function replaceFileContent(string $targetFilePath, string $search, string $replace): void
    {
        if (! file_exists($targetFilePath)) {
            $this->error("File not found: [$targetFilePath]");

            return;
        }

        $targetContent = str(file_get_contents($targetFilePath));
        if (! $targetContent->contains($search)) {
            $this->warn("Can’t find string [$search] in file [$targetFilePath]");
        }

        file_put_contents($targetFilePath, $targetContent->replace($search, $replace));
    }

    private function registerSharpProvider(): void
    {
        $this->replaceFileContent(
            app_path('Providers/AppServiceProvider.php'),
            'public function register(): void'.PHP_EOL.'    {'.PHP_EOL,
            'public function register(): void'.PHP_EOL.'    {'.PHP_EOL.'        $this->app->register(SharpServiceProvider::class);'.PHP_EOL,
        );
    }
}
