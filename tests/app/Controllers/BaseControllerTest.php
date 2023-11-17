<?php
namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\TestResponse;

function bool_to_string(bool $b): string
{
    return $b ? 'true': 'false';
}

class Test extends BaseController
{
    public function test_all_user_access_level(): string
    {
        $this->access_level = "*";
        return bool_to_string($this->check_permission());
    }
    public function test_logged_user_access_level(): string
    {
        $this->access_level = "@";
        return bool_to_string($this->check_permission());
    }
    public function test_admin_access_level(): string
    {
        $this->access_level = Config('\User\Config\UserConfig')
             ->access_lvl_admin;
        return bool_to_string($this->check_permission());
    }
}

class BaseControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    public function test_all_user_access_level_without_account(): void
    {
        $this->test_access_level('test_all_user_access_level')(true);
    }

    public function test_all_user_access_level_with_registered(): void
    {
        $this->test_access_level('test_all_user_access_level',
            $this->get_registered_data())(true);
    }

    public function test_all_user_access_level_with_admin(): void
    {
        $this->test_access_level('test_all_user_access_level',
            $this->get_admin_data())(true);
    }

    public function test_logged_user_access_level_without_account(): void
    {
        $this->test_access_level('test_logged_user_access_level')(false);
    }

    public function test_logged_user_access_level_with_registered(): void
    {
        $this->test_access_level('test_logged_user_access_level',
            $this->get_registered_data())(true);
    }

    public function test_logged_user_access_level_with_admin(): void
    {
        $this->test_access_level('test_logged_user_access_level',
            $this->get_admin_data())(true);
    }

    public function test_admin_access_level_without_account(): void
    {
        $this->test_access_level('test_admin_access_level')(false);
    }

    public function test_admin_access_level_with_registered(): void
    {
        $this->test_access_level('test_admin_access_level',
            $this->get_registered_data())(false);
    }

    public function test_admin_access_level_with_admin(): void
    {
        $this->test_access_level('test_admin_access_level',
            $this->get_admin_data())(true);
    }

    private function test_access_level(string $method_name,
        ?array $sessionData=null): callable
    {
        # $test_level = fn() => $this->execute_methode(
        #     $method_name, $sessionData);
        # $result = $test_level();
        $result = $this->execute_methode($method_name, $sessionData);
        return fn ($expect) => $this->assertEquals(bool_to_string($expect),
            $result->response()->getBody());
    }

    private function execute_methode(string $method, ?array $sessionData):
        TestResponse
    {
        if (isset($sessionData)) {
            $_SESSION = array_merge($_SESSION, $sessionData);
        }
        return $this->controller(Test::class)->execute($method);
    }

    private function get_registered_data(): array
    {
        $data['logged_in'] = true;
        $data['user_access'] = Config('\User\Config\UserConfig')
           ->access_lvl_registered;
        return $data;
    }

    private function get_admin_data(): array
    {
        $data['logged_in'] = true;
        $data['user_access'] = Config('\User\Config\UserConfig')
           ->access_lvl_admin;
        return $data;
    }
    
}
