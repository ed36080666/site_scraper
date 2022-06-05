<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('https://www.porntrex.com/video/146608/anya-ivy-rub-my-tennis-jock');
            $flash = $browser->script("return window.flashvars;"); // this returns array
            dd($flash);
//                    ->assertSee('Scraper');
        });
    }
}
