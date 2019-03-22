<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Tests\Browser\Components\InputCases;

class searchTest extends DuskTestCase
{
    use WithFaker;
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testRegister()
    {
        $this->faker = \Faker\Factory::create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/find');
            $browser->waitForText('Search keyword in files');
        });
    }
}
