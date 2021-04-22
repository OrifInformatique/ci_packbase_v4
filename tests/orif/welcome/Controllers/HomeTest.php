<?php

namespace Welcome\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTester;

class HomeTest extends CIUnitTestCase
{
    use ControllerTester;

    public function testIndexView()
    {
        $resultsofhome = $this->withURI(base_url())
                        ->controller(\Welcome\Controllers\Home::class)
                        ->execute('index');

        // Test if result is ok and verify that all view parts are present
        $this->assertTrue($resultsofhome->isOK());
        $this->assertTrue($resultsofhome->see('charset="utf-8"'));
        $this->assertTrue($resultsofhome->see(lang('common_lang.app_title')));
        $this->assertTrue($resultsofhome->see('Welcome to CodeIgniter'));
    }
    public function testLinksCDN(){
        $client =\Config\Services::curlrequest();
        //teste jquery
        $this->assertTrue($client->request('GET','https://code.jquery.com/jquery-3.4.1.min.js')->getStatusCode()==200);
        //teste popper
        $this->assertTrue($client->request('GET','https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js')->getStatusCode()==200);
        //teste bootstrapjs
        $this->assertTrue($client->request('GET','https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js')->getStatusCode()==200);
    }
    public function testTags(){
        $client =\Config\Services::curlrequest();
        $response=$client->request('GET',base_url())->getBody();
        $this->assertTrue(\strpos($response,'<head>')&&strpos($response,'</head>'));
        $this->assertTrue(\strpos($response,'<body>')&&strpos($response,'</body>'));
        $this->assertTrue(\strpos($response,'<html>')&&strpos($response,'</html>'));
    } 
    public function testUser(){
        $client =\Config\Services::curlrequest();
        
    }

}