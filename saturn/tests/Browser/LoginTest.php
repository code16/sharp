<?php

namespace Tests\Browser;

use App\Spaceship;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function we_can_show_the_login_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/sharp/login')
                    ->assertPathIs('/sharp/login')
                    ->assertVisible("button.SharpButton--primary");
        });
    }

    /** @test */
    public function we_are_redirected_to_the_login_page_if_guest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/sharp')
                ->assertPathIs('/sharp/login');

            $browser->visit('/sharp/form/spaceship/1')
                ->assertPathIs('/sharp/login');
        });
    }

    /** @test */
    public function we_get_validation_error_on_incomplete_form()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/sharp/login')
                ->type('login', 'test')
                ->press('#submit')
                ->assertPathIs('/sharp/login')
                ->assertSee('Please enter a value for both fields');
        });
    }

    /** @test */
    public function we_get_validation_error_on_invalid_credentials()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/sharp/login')
                ->type('login', 'test')
                ->type('password', 'test')
                ->press('#submit')
                ->assertPathIs('/sharp/login')
                ->assertSee('We couldn’t find a user with these credentials');
        });
    }

    /** @test */
    public function we_cant_login_with_a_non_sharp_user()
    {
        factory(User::class)->create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret')
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/sharp/login')
                ->type('login', 'test@example.com')
                ->type('password', 'secret')
                ->press('#submit')
                ->assertPathIs('/sharp/login');
        });
    }

    /** @test */
    public function we_can_login_to_dashboard_with_a_sharp_user()
    {
        factory(User::class)->create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret'),
            'group' => 'sharp'
        ]);

        session()->forget('url.intended');

        $this->browse(function (Browser $browser) {
            $browser->visit('/sharp/login')
                ->type('login', 'test@example.com')
                ->type('password', 'secret')
                ->press('#submit')
                ->assertPathIs('/sharp');
        });
    }

    /** @test */
    public function we_can_login_to_intended_url()
    {
        factory(User::class)->create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret'),
            'group' => 'sharp'
        ]);

        $spaceship = factory(Spaceship::class)->create();

        $this->browse(function (Browser $browser) use($spaceship) {
            $browser->visit('/sharp/form/spaceship/' . $spaceship->id)
                ->assertPathIs('/sharp/login')
                ->type('login', 'test@example.com')
                ->type('password', 'secret')
                ->press('#submit')
                ->assertPathIs('/sharp/form/spaceship/' . $spaceship->id);
        });
    }
}
