<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Tests\Fixtures\PersonSharpEntityList;

class CommandControllerTest extends BaseApiTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->login();
    }

    /** @test */
    public function we_can_call_an_info_entity_command()
    {
        $this->buildTheWorld();
        $this->disableExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_info')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
            ]);
    }

    /** @test */
    public function we_can_call_an_info_instance_command()
    {
        $this->buildTheWorld();
        $this->disableExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/instance_info/1')
            ->assertStatus(200)
            ->assertJson([
                "action" => "info",
                "message" => "ok",
            ]);
    }

    /** @test */
    public function we_can_call_an_reload_entity_command()
    {
        $this->buildTheWorld();
        $this->disableExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/entity_reload')
            ->assertStatus(200)
            ->assertJson([
                "action" => "reload"
            ]);
    }

    /** @test */
    public function we_can_call_an_reload_instance_command()
    {
        $this->buildTheWorld();
        $this->disableExceptionHandling();

        $this->json('post', '/sharp/api/list/person/command/instance_reload/1')
            ->assertStatus(200)
            ->assertJson([
                "action" => "reload"
            ]);
    }

    /** @test */
    public function applicative_exception_returns_a_417_as_always()
    {
        $this->buildTheWorld();

        $this->json('post', '/sharp/api/list/person/command/entity_exception')
            ->assertStatus(417)
            ->assertJson([
                "message" => "error"
            ]);
    }

    protected function buildTheWorld()
    {
        parent::buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.list',
            EntityCommandPersonSharpEntityList::class
        );
    }
}

class EntityCommandPersonSharpEntityList extends PersonSharpEntityList {

    function buildListConfig()
    {
        $this->addEntityCommand("entity_info", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute() {
                return $this->info("ok");
            }

        })->addInstanceCommand("instance_info", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function execute($instanceId) {
                return $this->info("ok");
            }
        })->addEntityCommand("entity_reload", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute() {
                return $this->reload();
            }

        })->addInstanceCommand("instance_reload", new class() extends InstanceCommand {
            public function label(): string { return "label"; }
            public function execute($instanceId) {
                return $this->reload();
            }
        })->addInstanceCommand("entity_exception", new class() extends EntityCommand {
            public function label(): string { return "label"; }
            public function execute() {
                throw new SharpApplicativeException("error");
            }
        });
    }
}