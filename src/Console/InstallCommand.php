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
        $shouldCreateCheckHandler = confirm(
            label: 'Should create a check handler to restrict Sharp access only to some users (admins, etc) ?',
        );

        $this->publishConfig();
        $this->publishAssets();
        $this->initSharpMenu();
        if ($shouldCreateCheckHandler) {
            $this->createCheckHandler();
        }

        return 0;
    }

    private function publishConfig()
    {
        Artisan::call('vendor:publish', [
            '--provider' => 'Code16\Sharp\SharpServiceProvider',
            '--tag' => 'config',
        ]);

        $this->components->info('Sharp config has been published successfully to [config/sharp.php].');
    }

    private function publishAssets()
    {
        Artisan::call('vendor:publish', [
            '--provider' => '"Code16\Sharp\SharpServiceProvider"',
            '--tag' => 'assets',
        ]);

        $this->components->info('Sharp assets have been published successfully to [public/vendor/sharp].');

        $this->replaceFileContent(
            base_path('composer.json'),
            'ComposerScripts::postAutoloadDump",'.PHP_EOL,
            'ComposerScripts::postAutoloadDump",'.PHP_EOL.'            "@php artisan vendor:publish --provider=Code16\\\\\\\\Sharp\\\\\\\\SharpServiceProvider --tag=assets --force",'.PHP_EOL,
        );

        $this->components->info('Refresh assets has been added successfully to the `scripts.post-autoload-dump` section in [composer.json].');
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

        $this->components->info('A default menu has been added successfully');
    }

    private function addMenuToSharpConfig()
    {
        $this->replaceFileContent(
            config_path('sharp.php'),
            "'menu' => null, //\\App\\Sharp\\SharpMenu::class",
            "'menu' => \\App\\Sharp\\SharpMenu::class,",
        );
    }

    private function createCheckHandler()
    {
        Artisan::call('sharp:make:check-handler', [
            'name' => 'SharpCheckHandler',
        ]);

        $this->addCheckHandlerToSharpConfig();

        $this->components->info('A default check handler has been added successfully');
    }

    private function addCheckHandlerToSharpConfig()
    {
        $this->replaceFileContent(
            config_path('sharp.php'),
            "'auth' => [".PHP_EOL,
            "'auth' => [".PHP_EOL."        'check_handler' => \\App\\Sharp\\SharpCheckHandler::class,".PHP_EOL,
        );
    }
}
