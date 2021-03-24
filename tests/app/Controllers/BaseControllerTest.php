<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTester;

class BaseControllerTest extends CIUnitTestCase
{
    use ControllerTester;

    public function testDisplayView()
    {
        $results = $this->withURI(base_url())
                        ->controller(\App\Controllers\BaseController::class)
                        ->execute('display_view');

        // Test if result is ok and verify that all view parts are present
        $this->assertTrue($results->isOK());
        $this->assertTrue($results->see('charset="utf-8"'));
        $this->assertTrue($results->see(lang('common_lang.app_title')));
        $this->assertTrue($results->dontSee('Welcome to CodeIgniter'));
    }
}