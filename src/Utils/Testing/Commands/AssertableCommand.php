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

    public function assertViewIs($value)
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

    public function assertReturnsInfo(string $message = ''): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'info')
            ->when($message)->where('message', $message)
            ->etc()
        );

        return $this;
    }

    public function assertReturnsLink(string $url = ''): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'link')
            ->when($url)->where('link', $url)
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

    public function assertReturnsRefresh(array $ids): static
    {
        $this->response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->where('action', 'refresh')
            ->etc()
        );

        PHPUnit::assertEqualsCanonicalizing(
            $ids,
            collect($this->response->json('items'))->pluck($this->commandContainer->getInstanceIdAttribute())->all()
        );

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

    public function assertReturnsDownload(?string $filename = null): static
    {
        $this->response->assertOk()->assertStreamed();

        if ($filename) {
            preg_match('/filename="?([^";]+)"?/', $this->response->headers->get('Content-Disposition'), $matches);
            PHPUnit::assertEquals($filename, $matches[1] ?? null);
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
        Facades\View::creator('*', function (View $view) {
            $this->createdView = $view;
        });

        return tap(($this->postCommand)($this->data, $this->step), function () {
            Facades\Event::forget('creating: *');
        });
    }
}
