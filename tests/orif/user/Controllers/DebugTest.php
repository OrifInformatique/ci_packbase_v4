<?php
/**
 * Unit tests AuthTest
 *
 * @author      Orif (CaLa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */

namespace User\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;

/**
*/
// class DebugTest extends CIUnitTestCase {
//   use ControllerTestTrait;
// 
//   public function test_debug(): void
//   {
//     // d(headers_list());
//     // dd(headers_sent());
//     if (!getenv('CLIENT_ID')) {
//       d($this->get_cannot_github_action_message());
//       return;
//     }
//     headers_sent($file,$line);
//     // d($file,$line);
//     // d(headers_list());
//     $result = $this->controller(Auth::class)
//     ->execute('azure_login');
// 
//     d('test debug');
//     //$userAgent = $_SERVER;
//     //dd($userAgent);
//     $html = file_get_contents($redirectUrl, false); // parameter false ?
//     $this->assertEquals(1, preg_match('/.*login.*/', $html));
//     # do not work on github action with secret
//     # $this->assertEquals(1, preg_match('/.*signup.*/', $html));
//     # $this->assert_azure_page($html);
//   }
// }
