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
            $browser->visit('/cvexa/find');
            $this->assertNotNull($browser->waitForText('Search in files'));
            $this->assertNotNull($browser->element('#search'));
            $this->assertNotNull($browser->element('#extensions'));
            $this->assertNotNull($browser->element('#path'));
            $this->assertNotNull($browser->element('#location'));
            $this->assertNotNull($browser->element('#name'));
            $this->assertNotNull($browser->element('#content'));
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->element('.alert-danger'));
            $browser->type('#search', 'Controller')
            ->pause(500);
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('No Results'));
            $browser->clear('#search');
            $browser->type('#search', 'index')
            ->pause(500);
            $browser->type('#extensions', '.php')
            ->pause(500);
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->element('.alert-success'));
            $this->assertNotNull($browser->waitForText('index.php'));
            $browser->click('#reset-btn');
            $this->assertNotNull($browser->waitForText('Search in files'));
            $browser->type('#search', '$request = Illuminate\Http\Request::capture()');
            $browser->radio('#content', '0');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('index.php'));
            $browser->click('#reset-btn');
            $browser->type('#search', 'robots');
            $browser->type('#extensions', '.pdf,xml');
            $browser->radio('#name', '1');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('No Results'));
            $browser->click('#reset-btn');
            $browser->type('#search', 'app');
            $browser->type('#extensions', '.css,.js');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('app.css'));
            $this->assertNotNull($browser->waitForText('app.js'));
            $browser->click('#reset-btn');
            $browser->type('#search', 'web');
            $browser->type('#path', 'routes');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('web.php'));
            $browser->click('#reset-btn');
            $browser->type('#search', "Route::middleware('auth:api')");
            $browser->type('#extensions', '.php');
            $browser->type('#path', 'routes');
            $browser->radio('#content', '0');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('api.php'));
            $browser->click('#reset-btn');
            $browser->type('#search', "Controller");
            $browser->type('#path', '/app/Http/Controllers/');
            $browser->radio('#name', '1');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->element('.alert-success'));
            $browser->click('#reset-btn');
            $browser->type('#search', "Controller");
            $browser->type('#path', '/app/Http/Controllers/');
            $browser->radio('#content', '0');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->element('.alert-success'));
            $browser->click('#reset-btn');
            $browser->type('#search', "something");
            $browser->type('#path', '/');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('The path must be at least 2 characters.'));
            $browser->click('#reset-btn');
            $browser->type('#search', "something");
            $browser->type('#path', '/vendor');
            $browser->click('#search-btn')
                ->pause(500);
            $this->assertNotNull($browser->waitForText('This route is denied!'));
        });
    }
}
