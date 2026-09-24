<?php

namespace Code16\Sharp\Utils\Testing\Commands;

use Closure;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Illuminate\Support\Facades;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Illuminate\View\View;
use PHPUnit\Framework\Assert as PHPUnit;

class AssertableCommand
{
    use DelegatesToResponse;

    public ?View $createdView = null;

    public function __construct(
        /** @var Closure(array,string): TestResponse */
        protected Closure $postCommand,
        /** @var Closure(?string): TestResponse */
        protected Closure $getForm,
        protected SharpEntityList|SharpShow|SharpDashboard $commandContainer,
        protected array $data = [],
        protected ?string $step = null,
    ) {
        $this->response = $this->post();
    }

    public function assertViewHas(mixed $key, mixed $value = null): static
    {
        $this->response->original = $this->createdView;
        $this->response->assertViewHas($key, $value);

        return $this;
    }

    public function assertViewHasAll(mixed $bindings): static
    {
        $this->response->original = $this->createdView;
        $this->response->assertViewHas($bindings);

        return $this;
    }

    public function assertViewIs($value): static
    {
        $this->response->original = $this->createdView;
        $this->response->assertViewIs($value);

        return $this;
    }

    public function assertReturnsView(?string $view = null, ?array $data = null): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'view')
            ->etc()
        );

        if ($view) {
            $this->assertViewIs($view);
        }

        if ($data) {
            $this->assertViewHasAll($data);
        }

        return $this;
    }

    public function assertReturnsInfo(?string $message = null, ?bool $reload = null): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'info')
            ->when($message !== null, fn (AssertableJson $json) => $json->where('message', $message))
            ->when($reload !== null, fn (AssertableJson $json) => $json->where('reload', $reload))
            ->etc()
        );

        return $this;
    }

    public function assertReturnsLink(?string $url = null, ?bool $newTab = null): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'link')
            ->when($url !== null, fn (AssertableJson $json) => $json->where('link', $url))
            ->when($newTab !== null, fn (AssertableJson $json) => $json->where('openInNewTab', $newTab))
            ->etc()
        );

        return $this;
    }

    public function assertReturnsReload(): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'reload')
            ->etc()
        );

        return $this;
    }

    public function assertReturnsRefresh(?array $ids = null): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'refresh')
            ->etc()
        );

        if ($ids !== null) {
            PHPUnit::assertEqualsCanonicalizing(
                $ids,
                $this->commandContainer instanceof SharpEntityList
                    // In an Entity List, refreshed items are returned fully built
                    ? collect($this->response->json('items'))->pluck($this->commandContainer->getInstanceIdAttribute())->all()
                    : $this->response->json('items'),
            );
        }

        return $this;
    }

    public function assertReturnsStep(?string $step = null): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'step')
            ->etc()
        );

        if ($step) {
            PHPUnit::assertEquals($step, Str::before($this->response->json('step'), ':'));
        }

        return $this;
    }

    public function assertReturnsDownload(?string $filename = null, ?string $content = null): static
    {
        $this->response->assertOk()->assertStreamed();

        if ($filename !== null) {
            preg_match('/filename="?([^";]+)"?/', $this->response->headers->get('Content-Disposition'), $matches);
            PHPUnit::assertEquals($filename, $matches[1] ?? null);
        }

        if ($content !== null) {
            PHPUnit::assertEquals($content, $this->response->streamedContent());
        }

        return $this;
    }

    public function getNextStepForm(): AssertableCommandForm
    {
        $this->assertReturnsStep();

        return new AssertableCommandForm(
            post: $this->postCommand,
            getForm: $this->getForm,
            commandContainer: $this->commandContainer,
            step: $this->response->json('step'),
        );
    }

    protected function post(): TestResponse
    {
        $this->createdView = null;

        // Keep the first created view: the one returned by the command, not its partials / components
        Facades\View::creator('*', function (View $view) {
            $this->createdView ??= $view;
        });

        return tap(($this->postCommand)($this->data, $this->step), function () {
            Facades\Event::forget('creating: *');
        });
    }
}
