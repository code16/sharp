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
