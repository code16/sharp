<?php

namespace Tests\Browser;

use App\Spaceship;
use App\SpaceshipType;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FormTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function we_can_load_the_edit_form()
    {
        $user = factory(User::class)->create([
            'group' => 'sharp'
        ]);

        $spaceship = factory(Spaceship::class)->create();

        $this->browse(function (Browser $browser) use($user, $spaceship) {
            $browser->loginAs($user, config("sharp.auth.guard", config("auth.defaults.guard")))
                ->visit('/sharp/form/spaceship/' . $spaceship->id)
                ->assertPathIs('/sharp/form/spaceship/' . $spaceship->id)
                ->waitFor(".SharpForm")
                ->assertSeeIn(".SharpForm--label", "NAME");
        });
    }

    /** @test */
    public function we_can_load_the_create_form()
    {
        $user = factory(User::class)->create([
            'group' => 'sharp'
        ]);

        factory(SpaceshipType::class)->create();

        $this->browse(function (Browser $browser) use($user) {
            $browser->loginAs($user, config("sharp.auth.guard", config("auth.defaults.guard")))
                ->visit('/sharp/form/spaceship')
                ->assertPathIs('/sharp/form/spaceship')
                ->waitFor(".SharpForm")
                ->assertSeeIn(".SharpForm--label", "NAME");
        });
    }
}
