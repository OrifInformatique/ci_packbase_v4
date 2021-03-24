<?php

namespace Welcome\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTester;

class HomeTest extends CIUnitTestCase
{
    use ControllerTester;

    public function testIndexView()
    {
        $results = $this->withURI(base_url())
                        ->controller(\Welcome\Controllers\Home::class)
                        ->execute('index');

        // Test if result is ok and verify that all view parts are present
        $this->assertTrue($results->isOK());
        $this->assertTrue($results->see('charset="utf-8"'));
        $this->assertTrue($results->see(lang('common_lang.app_title')));
        $this->assertTrue($results->see('Welcome to CodeIgniter'));
    }
}