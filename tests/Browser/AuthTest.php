<?php

namespace Orchestra\Testbench\Dusk\Tests\Browser;

use Laravel\Dusk\Browser;
use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Dusk\TestCase;

class AuthTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../factories');
        $this->loadLaravelMigrations(config('database.default'));
    }

    /** @test */
    public function can_authenticate_user()
    {
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user, 'web')
                ->assertAuthenticatedAs($user, 'web');
        });
    }
}
