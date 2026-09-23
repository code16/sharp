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
        ->toContain("use Code16\\Sharp\\EntityList\\Commands\\InstanceCommand;\nuse Code16\\Sharp\\EntityList\\Commands\\Returns\\CommandReturn;\n")
        ->toContain('public function execute(mixed $instanceId, array $data = []): CommandReturn')
        ->toContain('protected function helper(): array');

    expect(File::get($this->absoluteTestPath.'/MyWizard.php'))
        ->toContain('protected function executeFirstStep(array $data): CommandReturn')
        ->toContain('public function executeStepSecond(array $data = []): CommandReturn')
        ->and(substr_count(File::get($this->absoluteTestPath.'/MyWizard.php'), 'use Code16\\Sharp\\EntityList\\Commands\\Returns\\CommandReturn;'))
        ->toBe(1);

    expect(File::get($this->absoluteTestPath.'/MyState.php'))
        ->toContain('protected function updateState($instanceId, string $stateId): CommandReloadReturn|CommandRefreshReturn|null')
        ->toContain('use Code16\\Sharp\\EntityList\\Commands\\Returns\\CommandRefreshReturn;')
        ->toContain('use Code16\\Sharp\\EntityList\\Commands\\Returns\\CommandReloadReturn;');

    expect(File::get($this->absoluteTestPath.'/MySingleState.php'))
        ->toContain('protected function updateSingleState(string $stateId): CommandReloadReturn')
        ->toContain('use Code16\\Sharp\\EntityList\\Commands\\Returns\\CommandReloadReturn;');

    expect(File::get($this->absoluteTestPath.'/NotACommand.php'))
        ->toContain('public function execute(array $data = []): array');
});
