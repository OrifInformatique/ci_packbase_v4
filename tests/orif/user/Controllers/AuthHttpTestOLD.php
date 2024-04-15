<?php

namespace User\Controllers;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\CIUnitTestCase;

use User\Models\User_model;

class AuthHttpTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    private function test_azure_login_begin(): void
    {
        $url = substr(url_to('Auth::azure_login_begin'), strlen(base_url()));
        $result = $this->call('get', $url);
        $redirectUrl = $result->getRedirectUrl();
        $html = file_get_contents($redirectUrl, false);
        dd($html);
    }
    
    /**
    * Asserts that when the user correctly inserts the validation code,
    * a new user is successfully created if the user is completly missing from
    * the DB 
    */
    //public function test_azure_mail_with_correct_code_new_user(): void
    //{
    //    $firstName = 'Firstname';
    //    $lastName = 'Lastname';
    //    $userName = "$firstName.$lastName";
    //    $_POST['user_verification_code'] = 'correct';
    //    $_SESSION['verification_code'] = 'correct';
    //    $_SESSION['verification_attempts'] = 3;
    //    $_SESSION['timer_end'] = time() + 300; // force timer_end to be greater than time()
    //    // $is_code_valid should be set to true at this point in the method
    //    $_SESSION['new_user'] = true;
    //    $_SESSION['azure_mail'] = "$userName@azurefake.fake";
    //    $_SESSION['form_email'] = "fake@azurefake.fake";
    //    $url = substr(url_to('azure_login'), strlen(site_url()));
    //    d($url);
    //    $result = $this->withSession()->call('get', $url);
    //    $userModel = model(User_model::class);
    //    $name = $userModel->select('username')->where('username=', $userName)
    //                                          ->findAll()[0]['username'];
    //    $this->assertEquals($userName, $name);
    //}

    /**
    * Asserts that the validation code is successfuly sent when the user
    * is already in the DB but doesn't have an azure_email. (Only an email)
    */

    // public function test_azure_mail_existed_user_variable_created(): void
    // {
    //     $userId = 2;
    //     $noAzureMail = 'fake@fake.fake';
    //     $userModel = model(User_model::class);
    //     $userModel->update($userId, ['email' => $noAzureMail]);
    //     d($userModel->find($userId));
    //     $_POST['user_verification_code'] = null;
    //     $_SESSION['verification_code'] = null;
    //     # $_SESSION['verification_attempts'] = 3;
    //     $_SESSION['after_login_redirect'] = base_url();
    //     $_POST['user_email'] = $noAzureMail;
    //     #$_SESSION['azure_mail'] = "$userName@azurefake.fake";
    //     $azureMail = 'fake@azurefake.fake';
    //     $_SESSION['azure_mail'] = $azureMail;
    //     $url = substr(url_to('processMailForm'), strlen(site_url()));
    //     $result = $this->withSession()->post($url);
    //     # d($result->response()->getBody());
    //     $result->assertSee(lang('user_lang.user_validation_code'));
    // }

    //public function test_azure_mail_with_correct_code_existing_user(): void
    //{
    //    $userId = 2;
    //    $noAzureMail = 'fake@fake.fake';
    //    $userModel = model(User_model::class);
    //    $userModel->update($userId, ['email' => $noAzureMail]);
    //    d($userModel->findAll());
    //    $_POST['user_verification_code'] = 'correct';
    //    $_SESSION['verification_code'] = 'correct';
    //    # $_SESSION['verification_attempts'] = 3;
    //    $_SESSION['after_login_redirect'] = base_url();
    //    $_SESSION['new_user'] = false;
    //    $_SESSION['form_email'] = $noAzureMail;
    //    #$_SESSION['azure_mail'] = "$userName@azurefake.fake";
    //    $azureMail = 'fake@azurefake.fake';
    //    $_SESSION['azure_mail'] = $azureMail;
    //    $url = substr(url_to('processMailForm'), strlen(site_url()));
    //    $result = $this->withSession()->call('get', $url);
    //    # d($result->response()->getBody());
    //    $azureMailInDb = $userModel->select('azure_mail')
    //                           ->find($userId)['azure_mail'];
    //    $this->assertEquals($azureMail, $azureMailInDb);
    //}
}