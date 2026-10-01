<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    // 1. Define a relative path we can pass to the command's path argument
    $this->testPath = 'storage/framework/testing/fake_sharp_app';

    // 2. Resolve the absolute path to create our test files
    $this->absoluteTestPath = base_path($this->testPath);
    File::ensureDirectoryExists($this->absoluteTestPath);

    // 3. Create a dummy file containing legacy Sharp syntax
    $this->dummyFilePath = $this->absoluteTestPath.'/AuthorFilter.php';

    $this->oldContent = <<<PHP
<?php

namespace App\Sharp\Utils\Filters;

use App\Models\User;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Filters\DateRangeFilter;

class AuthorFilter extends EntityListSelectFilter
{
    public function buildFilterConfig(): void
    {
        SharpFormUploadField::make()
            ->setCropRatio('16:9')
            ->shouldOptimizeImage();

        new FormLayoutColumn()
            ->withSingleField('test');
    }
}
PHP;

    File::put($this->dummyFilePath, $this->oldContent);
});

afterEach(function () {
    // Clean up the temporary directory after each test
    File::deleteDirectory($this->absoluteTestPath);
});

it('updates sharp configuration syntax correctly', function () {
    $this->artisan('sharp:upgrade '.$this->testPath)
        ->expectsOutputToContain('Updated: AuthorFilter.php')
        ->expectsOutputToContain('1 files were successfully updated.')
        ->assertSuccessful();

    $newContent = File::get($this->dummyFilePath);

    // 1. Assert namespaces were updated correctly
    expect($newContent)
        ->toContain('use Code16\Sharp\Filters\SelectFilter;')
        ->toContain('use Code16\Sharp\Filters\DateRangeFilter;')
        ->not->toContain('Code16\Sharp\EntityList\Filters\EntityListSelectFilter')
        ->not->toContain('Code16\Sharp\Utils\Filters\DateRangeFilter');

    // 2. Assert bare class extension was updated
    expect($newContent)
        ->toContain('class AuthorFilter extends SelectFilter')
        ->not->toContain('extends EntityListSelectFilter');

    // 3. Assert method calls were updated
    expect($newContent)
        ->toContain('->withField(\'test\');')
        ->toContain('->setImageCropRatio(\'16:9\')')
        ->toContain('->setImageOptimize();')
        ->not->toContain('withSingleField')
        ->not->toContain('setCropRatio')
        ->not->toContain('shouldOptimizeImage');
});

it('ignores files that do not need updating', function () {
    // Overwrite the dummy file with already-updated syntax
    $modernContent = <<<PHP
<?php
namespace App\Sharp\Utils\Filters;
use Code16\Sharp\Filters\SelectFilter;
class AuthorFilter extends SelectFilter {}
PHP;

    File::put($this->dummyFilePath, $modernContent);

    // Command should report 0 files updated
    $this->artisan('sharp:upgrade '.$this->testPath)
        ->expectsOutputToContain('0 files were successfully updated.')
        ->assertSuccessful();

    expect(File::get($this->dummyFilePath))->toBe($modernContent);
});

it('upgrades command return types to CommandReturn', function () {
    File::put($this->absoluteTestPath.'/MyCommand.php', <<<'PHP'
<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class MyCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'My command';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->reload();
    }

    protected function helper(): array
    {
        return [];
    }
}
PHP);

    File::put($this->absoluteTestPath.'/MyWizard.php', <<<'PHP'
<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;

class MyWizard extends EntityWizardCommand
{
    protected function executeFirstStep(array $data): array
    {
        return $this->toStep('second');
    }

    public function executeStepSecond(array $data = []): array
    {
        return $this->reload();
    }
}
PHP);

    File::put($this->absoluteTestPath.'/MyState.php', <<<'PHP'
<?php

namespace App\Sharp\States;

use Code16\Sharp\EntityList\Commands\EntityState;

class MyState extends EntityState
{
    protected function buildStates(): void {}

    protected function updateState($instanceId, string $stateId): ?array
    {
        return null;
    }
}
PHP);

    File::put($this->absoluteTestPath.'/MySingleState.php', <<<'PHP'
<?php

namespace App\Sharp\States;

use Code16\Sharp\EntityList\Commands\SingleEntityState;

class MySingleState extends SingleEntityState
{
    protected function buildStates(): void {}

    protected function updateSingleState(string $stateId): array
    {
        return $this->reload();
    }
}
PHP);

    File::put($this->absoluteTestPath.'/NotACommand.php', <<<'PHP'
<?php

namespace App\Services;

class NotACommand
{
    public function execute(array $data = []): array
    {
        return [];
    }
}
PHP);

    $this->artisan('sharp:upgrade '.$this->testPath)
        ->expectsOutputToContain('Updated: MyCommand.php')
        ->expectsOutputToContain('Updated: MyWizard.php')
        ->expectsOutputToContain('Updated: MyState.php')
        ->expectsOutputToContain('Updated: MySingleState.php')
        ->assertSuccessful();

    expect(File::get($this->absoluteTestPath.'/MyCommand.php'))
        ->toContain("use Code16\\Sharp\\Commands\\InstanceCommand;\nuse Code16\\Sharp\\Commands\\Returns\\CommandReturn;\n")
        ->toContain('public function execute(mixed $instanceId, array $data = []): CommandReturn')
        ->toContain('protected function helper(): array');

    expect(File::get($this->absoluteTestPath.'/MyWizard.php'))
        ->toContain('protected function executeFirstStep(array $data): CommandReturn')
        ->toContain('public function executeStepSecond(array $data = []): CommandReturn')
        ->and(substr_count(File::get($this->absoluteTestPath.'/MyWizard.php'), 'use Code16\\Sharp\\Commands\\Returns\\CommandReturn;'))
        ->toBe(1);

    expect(File::get($this->absoluteTestPath.'/MyState.php'))
        ->toContain('protected function updateState($instanceId, string $stateId): CommandReloadReturn|CommandRefreshReturn|null')
        ->toContain('use Code16\\Sharp\\Commands\\Returns\\CommandRefreshReturn;')
        ->toContain('use Code16\\Sharp\\Commands\\Returns\\CommandReloadReturn;');

    expect(File::get($this->absoluteTestPath.'/MySingleState.php'))
        ->toContain('protected function updateSingleState(string $stateId): CommandReloadReturn')
        ->toContain('use Code16\\Sharp\\Commands\\Returns\\CommandReloadReturn;');

    expect(File::get($this->absoluteTestPath.'/NotACommand.php'))
        ->toContain('public function execute(array $data = []): array');
});

it('upgrades info and link bool arguments to fluent modifiers', function () {
    File::put($this->absoluteTestPath.'/MyCommand.php', <<<'PHP'
<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;
use Code16\Sharp\EntityList\Commands\EntityCommand;

class MyCommand extends EntityCommand
{
    public function execute(array $data = []): CommandReturn
    {
        match ($data['case']) {
            1 => $this->info('Done', true),
            2 => $this->info(sprintf('%s, %s', $data['a'], 'b'), reload: true),
            3 => $this->info('Done', $data['reload']),
            4 => $this->info('Done, really', false),
            5 => $this->info(reload: true, message: 'Done'),
            6 => $this->info('Done'),
            7 => $this->link('https://example.org', openInNewTab: true),
            8 => $this->link(route('home', ['a' => 1]), true),
            9 => $this->link('https://example.org'),
        };
    }
}
PHP);

    $this->artisan('sharp:upgrade '.$this->testPath)
        ->expectsOutputToContain('Updated: MyCommand.php')
        ->assertSuccessful();

    expect(File::get($this->absoluteTestPath.'/MyCommand.php'))
        ->toContain("1 => \$this->info('Done')->withReload(),")
        ->toContain("2 => \$this->info(sprintf('%s, %s', \$data['a'], 'b'))->withReload(),")
        ->toContain("3 => \$this->info('Done')->withReload(\$data['reload']),")
        ->toContain("4 => \$this->info('Done, really'),")
        ->toContain("5 => \$this->info(message: 'Done')->withReload(),")
        ->toContain("6 => \$this->info('Done'),")
        ->toContain("7 => \$this->link('https://example.org')->inNewTab(),")
        ->toContain("8 => \$this->link(route('home', ['a' => 1]))->inNewTab(),")
        ->toContain("9 => \$this->link('https://example.org'),");
});

it('does not rewrite info() calls outside of Sharp commands', function () {
    $content = <<<'PHP'
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MyArtisanCommand extends Command
{
    public function handle()
    {
        $this->info('Done', 'v');
    }
}
PHP;

    File::put($this->absoluteTestPath.'/MyArtisanCommand.php', $content);

    $this->artisan('sharp:upgrade '.$this->testPath)->assertSuccessful();

    expect(File::get($this->absoluteTestPath.'/MyArtisanCommand.php'))->toBe($content);
});

it('moves commands out of the EntityList namespace, except QuickCreate', function () {
    File::put($this->absoluteTestPath.'/MyList.php', <<<'PHP'
<?php

namespace App\Sharp;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\QuickCreate\QuickCreationCommand;
use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;
use Code16\Sharp\EntityList\Commands\Wizards\InstanceWizardCommand;
use Code16\Sharp\EntityList\SharpEntityList;

class MyList extends SharpEntityList
{
    public function getReorderHandler(): ?\Code16\Sharp\EntityList\Commands\ReorderHandler
    {
        return null;
    }
}
PHP);

    $this->artisan('sharp:upgrade '.$this->testPath)
        ->expectsOutputToContain('Updated: MyList.php')
        ->assertSuccessful();

    expect(File::get($this->absoluteTestPath.'/MyList.php'))
        ->toContain('use Code16\\Sharp\\Commands\\EntityCommand;')
        ->toContain('use Code16\\Sharp\\EntityList\\Commands\\QuickCreate\\QuickCreationCommand;')
        ->toContain('use Code16\\Sharp\\EntityList\\ReorderHandler;')
        ->toContain('use Code16\\Sharp\\Commands\\Returns\\CommandReturn;')
        ->toContain('use Code16\\Sharp\\Commands\\Wizards\\InstanceWizardCommand;')
        ->toContain('use Code16\\Sharp\\EntityList\\SharpEntityList;')
        ->toContain('?\\Code16\\Sharp\\EntityList\\ReorderHandler');
});
