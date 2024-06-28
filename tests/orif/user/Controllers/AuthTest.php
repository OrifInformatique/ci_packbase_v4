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

use CodeIgniter\Test\TestResponse;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\RedirectResponse;


use User\Models\User_model;
 
class AuthTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    private function get_guest_user_type()
    {
        return Config('\User\Config\UserConfig')->access_lvl_guest;
    }

    /**
     * Custom assertions
     */
    private function get_response_and_assert(TestResponse $result): Response
    {
        $result->assertOK();
        $result->assertHeader('Content-Type', 'text/html; charset=UTF-8');
        return $result->response();
    }

    private function assert_reponse(TestResponse $result): void
    {
        $response = $this->get_response_and_assert($result);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertNotEmpty($response->getBody());
    }

    private function assert_redirect(TestResponse $result): void
    {
        $response = $this->get_response_and_assert($result);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEmpty($response->getBody());
    }

    /**
     * Asserts that the login page is loaded correctly (no session)
     */
    public function testloginPageWithoutSession()
    {
        // Execute login method of Auth class
        $result = $this->controller(Auth::class)
            ->execute('login');
        // Assertions
        $this->assert_reponse($result);
        $result->assertSeeElement('#username');
        $result->assertSeeElement('#password');
        $result->assertSeeElement('#btn_cancel');
        $result->assertSeeElement('#btn_login');
        $result->assertDontSeeElement('#fake_element');
        $result->assertSeeInField('username', '');
        $result->assertSeeInField('password', '');
        $result->assertSeeLink(lang('common_lang.btn_login'));
    }

    /**

     * when posting the login page
     */
    public function testloginPagePostedAfterLoginRedirectWithoutSession()
    {
        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['after_login_redirect'] = 'test';
        $_REQUEST['after_login_redirect'] = 'test';

        // Execute login method of Auth class
        $result = $this->controller(Auth::class)
            ->execute('login');

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_reponse($result);
        $this->assertEquals($_SESSION['after_login_redirect'], 'test');
    }
    
    /**
     * Asserts that the session variable is correctly set when posting the
     * login page (simulates a click on button login)
     * Username and incorrect password are specified (meaning that a warning
     * message is displayed)
     */
    public function
        testloginPagePostedWithoutSessionWithUsernameAndIncorrectPassword()
    {
        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['btn_login'] = 'true';
        $_REQUEST['btn_login'] = 'true';
        $_POST['username'] = 'admin';
        $_REQUEST['username'] = 'admin';
        $_POST['password'] = 'adminPwd';
        $_REQUEST['password'] = 'adminPwd';

        // Execute login method of Auth class
        $result = $this->controller(Auth::class)
            ->execute('login');

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_reponse($result);
        $this->assertEquals($_SESSION['message-danger'],
            lang('user_lang.msg_err_invalid_password'));
    }

    /**
     * Asserts that the session variables are correctly set when posting the
     * login page (simulates a click on button login)
     * Username and password are specified (meaning that the login works)
     */
    public function testloginPagePostedWithoutSessionWithUsernameAndPassword()
    {
        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = $this->get_guest_user_type();
        $username = 'UserUnitTest';
        $userEmail = 'userunittest@test.com';
        $userPassword = 'UserUnitTestPassword';
        $userId = self::insertUser($userType, $username, $userEmail,
            $userPassword);

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['btn_login'] = 'true';
        $_REQUEST['btn_login'] = 'true';
        $_POST['username'] = $username;
        $_REQUEST['username'] = $username;
        $_POST['password'] = $userPassword;
        $_REQUEST['password'] = $userPassword;

        // Execute login method of Auth class
        $result = $this->controller(Auth::class)
            ->execute('login');

        // Deletes inserted user 
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assertEquals($_SESSION['user_id'], $userId);
        $this->assertEquals($_SESSION['username'], $username);
        $this->assertTrue($_SESSION['logged_in']);
    }

    /**
     * Asserts that the session variables are correctly set when posting the
     * login page (simulates a click on button login)
     * User email and password are specified (meaning that the login works)
     */
    public function testloginPagePostedWithoutSessionWithUserEmailAndPassword()
    {
        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = $this->get_guest_user_type();
        $username = 'UserEmailUnitTest';
        $userEmail = 'useremailunittest@unittest.com';
        $userPassword = 'UserEmailUnitTestPassword';
        $userId = self::insertUser($userType, $username, $userEmail,
            $userPassword);

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['btn_login'] = 'true';
        $_REQUEST['btn_login'] = 'true';
        $_POST['username'] = $userEmail;
        $_REQUEST['username'] = $userEmail;
        $_POST['password'] = $userPassword;
        $_REQUEST['password'] = $userPassword;

        // Execute login method of Auth class
        $result = $this->controller(Auth::class)
            ->execute('login');

        // Deletes inserted user 
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();     

        // Assertions
        $this->assertEquals($_SESSION['user_id'], $userId);
        $this->assertEquals($_SESSION['username'], $username);
        $this->assertTrue($_SESSION['logged_in']);
    }

    /**
     * Asserts that the login page is redirected 
     */
    public function testloginPageWithSession()
    {
        // Initialize session
        $_SESSION['logged_in'] = true;

        // Execute login method of Auth class
        $result = $this->controller(Auth::class)
        ->execute('login');

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url());
    }

    /**
     * Asserts that the change_password page is redirected when the user
     * is not found in the database but is logged-in and has the correct
     * access level
     */
    
    public function testchange_passwordPageWithoutSession()
    {
        // Give proper access level
        $_SESSION['user_id'] = -1;
        $_SESSION['logged_in'] = true;
        $_SESSION['user_access'] = config("\User\Config\UserConfig")
          ->access_lvl_registered; // guest access
        // Execute change_password method of Auth class
        $result = $this->controller(Profile::class)
          ->execute('change_password');

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('user/auth/login'));
    }

    /**
     * Asserts that the change_password page is loaded correctly (with session)
     */
    public function testchange_passwordPageWithSession()
    {
        // Initialize session
        // Give proper access level
        $_SESSION['logged_in'] = true;
        $_SESSION['user_access'] = config("\User\Config\UserConfig")
          ->access_lvl_registered; // guest access
        $_SESSION['user_id'] = 1;

        // Execute change_password method of Profile class
        $result = $this->controller(Profile::class)
        ->execute('change_password');

        // Assertions
        $this->assert_reponse($result);
        $result->assertSeeElement('#old_password');
        $result->assertSeeElement('#new_password');
        $result->assertSeeElement('#confirm_password');
        $result->assertSeeElement('#btn_change_password');
        $result->assertDontSeeElement('#fake_element');
        $result->assertSeeInField('old_password', '');
        $result->assertSeeInField('new_password', '');
        $result->assertSeeLink(lang('common_lang.btn_cancel'));
    }
    
    /**
     * Asserts that the change_password page redirects to the base url when the
     * password is changed successfully
     */
    public function
        testchange_passwordPagePostedWithSessionWithOldAndNewPasswords()
    {
        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = $this->get_guest_user_type();
        $username = 'UserUnitTest';
        $userEmail = 'userunittest@test.com';
        $userPassword = 'UserUnitTestPassword';
        $userId = self::insertUser($userType, $username, $userEmail,
            $userPassword);

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['btn_change_password'] = 'true';
        $_REQUEST['btn_change_password'] = 'true';
        $_POST['old_password'] = $userPassword;
        $_REQUEST['old_password'] = $userPassword;
        $_POST['new_password'] = 'PasswordChanged';
        $_REQUEST['new_password'] = 'PasswordChanged';
        $_POST['confirm_password'] = 'PasswordChanged';
        $_REQUEST['confirm_password'] = 'PasswordChanged';

        // Initialize the session
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $userId;

        // Execute change_password method of Profile class
        $result = $this->controller(Profile::class)
            ->execute('change_password');

        // Deletes inserted user
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url());
    }

    /**
     * Asserts that the change_password page redirects to the base url when the
     * old password is invalid
     */
    public function
        testchange_passwordPagePostedWithSessionWithInvalidOldPassword()
    {
        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = $this->get_guest_user_type();
        $username = 'UserUnitTest';
        $userEmail = 'userunittest@test.com';
        $userPassword = 'UserUnitTestPassword';
        $userId = self::insertUser($userType, $username, $userEmail,
            $userPassword);

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['btn_change_password'] = 'true';
        $_REQUEST['btn_change_password'] = 'true';
        $_POST['old_password'] = 'UserUnitTestWrongPassword';
        $_REQUEST['old_password'] = 'UserUnitTestWrongPassword';
        $_POST['new_password'] = 'PasswordChanged';
        $_REQUEST['new_password'] = 'PasswordChanged';
        $_POST['confirm_password'] = 'PasswordChanged';
        $_REQUEST['confirm_password'] = 'PasswordChanged';

        // Initialize the session
        $_SESSION['logged_in'] = true;
        $_SESSION["username"] = $username;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_access'] = config('\User\Config\UserConfig')
            ->access_lvl_guest;

        // Execute change_password method of Profile class
        $result = $this->controller(Profile::class)
            ->execute('change_password');

        // Deletes inserted user
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee(lang('user_lang.msg_err_invalid_old_password'),
            'div');
    }

    /**
     * Asserts that the change_password page redirects to the base url when the
     * confirmed password is invalid
     */
    public function
        testchange_passwordPagePostedWithSessionWithInvalidConfirmedPassword()
    {
        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = $this->get_guest_user_type();
        $username = 'UserUnitTest';
        $userEmail = 'userunittest@test.com';
        $userPassword = 'UserUnitTestPassword';
        $userId = self::insertUser($userType, $username, $userEmail,
            $userPassword);

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['btn_change_password'] = 'true';
        $_REQUEST['btn_change_password'] = 'true';
        $_POST['old_password'] = $userPassword;
        $_REQUEST['old_password'] = $userPassword;
        $_POST['new_password'] = 'PasswordChanged';
        $_REQUEST['new_password'] = 'PasswordChanged';
        $_POST['confirm_password'] = 'WrongPasswordChanged';
        $_REQUEST['confirm_password'] = 'WrongPasswordChanged';

        // Initialize the session
        $_SESSION['logged_in'] = true;
        $_SESSION["username"] = $username;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_access'] = config('\User\Config\UserConfig')
            ->access_lvl_guest;

        // Execute change_password method of Profile class
        $result = $this->controller(Profile::class)
            ->execute('change_password');

        // Deletes inserted user
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee(lang('user_lang.msg_err_password_not_matches'),
            'div');
    }

    /**
     * Asserts that logout resets the session
     */
    public function testlogout()
    {
        // Initialize session
        $_SESSION['logged_in'] = true;

        // Assertion
        $this->assertNotEmpty($_SESSION);

        // Execute logout method of Auth class
        $result = $this->controller(Auth::class)
        ->execute('logout');

        // Assertion
        $this->assertEmpty($_SESSION);
    }

    private function assert_azure_page(string $html, ?array $azureData=null,
        string $which_false='') :void
    {
        $azureData = $azureData ?? $this->get_azure_data();
        $is_with_client_id = $which_false === 'CLIENT_ID' ? 0 : 1;
        $this->assertEquals($is_with_client_id,
            preg_match('/.*'.$azureData['CLIENT_ID'].'.*/', $html));

        $is_with_tenant_id = $which_false === 'TENANT_ID' ? 0 : 1;
        $this->assertEquals($is_with_tenant_id,
            preg_match('/.*'.$azureData['TENANT_ID'].'.*/', $html));
        $is_with_graph = $which_false === 'GRAPH_USER_SCOPES' ? 0 : 1;
        $this->assertEquals($is_with_graph,
            preg_match('/.*'.$azureData['GRAPH_USER_SCOPES'].'.*/', $html));
        $is_with_redirect = $which_false === 'REDIRECT_URI' ? 0 : 1;
        $this->assertEquals($is_with_redirect,
            preg_match('/.*'.preg_quote($azureData['REDIRECT_URI'], '/').'.*/',
            $html));

    }

    /**
    * Asserts that user is logging in with azure
    */
    public function test_login_begin_with_azure_account(): void
    {
      if (!getenv('CLIENT_ID')) {
        d($this->get_cannot_github_action_message());
        return;
      }
      $_POST['btn_login_microsoft'] = true;

      $result = $this->controller(Auth::class)
        ->execute('login');
      
      $this->assert_reponse($result);

      $redirectUrl = $result->getRedirectUrl();
      $html = file_get_contents($redirectUrl, false);
      $this->assertEquals(1, preg_match('/.*login.*/', $html));

      // do not work on github action with secret
      $this->assertEquals(1, preg_match('/.*signup.*/', $html));
      $this->assert_azure_page($html);
    }

    private function get_cannot_github_action_message(): string
    {
        # https://docs.github.com/en/actions/security-guides/using-secrets-in-github-actions
        return 'This test cannot be performed with github action without use '
            . 'secrets in github actions.';
    }

    /**
    * Asserts that the user is redirected to microsoft login page even
    * in the case of a fake CLIENT ID
    */
    public function test_azure_login_begin_client_id_fake(): void
    {
        if (!getenv('CLIENT_ID')) {
            d($this->get_cannot_github_action_message());
            return;
        }
        putenv('CLIENT_ID=fake');
        $_POST['btn_login_microsoft'] = true;
        $result = $this->controller(Auth::class)->execute('login');
        $this->assert_reponse($result);
        $redirectUrl = $result->getRedirectUrl();
        $html = file_get_contents($redirectUrl, false);
        $this->assertEquals(1, preg_match('/.*login.*/', $html));
    }

    public function test_azure_begin_tenant_id_fake(): void
    {
        if (!getenv('CLIENT_ID')) {
            d($this->get_cannot_github_action_message());
            return;
        }
        putenv('TENANT_ID=fake');
        $_POST['btn_login_microsoft'] = true;
        $result = $this->controller(Auth::class)->execute('login');
        $this->assert_reponse($result);
        $redirectUrl = $result->getRedirectUrl();
        $html = file_get_contents($redirectUrl, false);
        $this->assertEquals(1, preg_match('/.*"iHttpErrorCode":400.*/',
            $html));
    }

    public function test_azure_begin_graph_user_scopes_fake(): void
    {
        if (!getenv('CLIENT_ID')) {
            d($this->get_cannot_github_action_message());
            return;
        }
        putenv('GRAPH_USER_SCOPES=fake');
        $_POST['btn_login_microsoft'] = true;
        $result = $this->controller(Auth::class)->execute('login');
        $this->assert_reponse($result);
        $redirectUrl = $result->getRedirectUrl();
        $html = file_get_contents($redirectUrl, false);
        $this->assertEquals(1, preg_match('/.*Sign in to your account.*/',
            $html));
    }

    public function test_azure_begin_redirect_uri_fake(): void
    {
        if (!getenv('CLIENT_ID')) {
            d($this->get_cannot_github_action_message());
            return;
        }
        putenv('REDIRECT_URI=fake');
        $_POST['btn_login_microsoft'] = true;
        $result = $this->controller(Auth::class)->execute('login');
        $this->assert_reponse($result);
        $redirectUrl = $result->getRedirectUrl();
        $html = file_get_contents($redirectUrl, false);
        $this->assertEquals(1, preg_match('/.*"iHttpErrorCode":400.*/',
            $html));
    }

    public function test_azure_login_code_fake(): void
    {
        if (!getenv('CLIENT_ID')) {
            d($this->get_cannot_github_action_message());
            return;
        }
        $_GET["state"] = session_id(); 
        $_GET["code"] = 'fake'; 
        $result = $this->controller(Auth::class)->execute('azure_login');
        $result->assertSee(lang('user_lang.msg_err_azure_unauthorized'));
    }

    private function get_azure_data(): array
    {
        $azureData['CLIENT_ID'] = getenv('CLIENT_ID');
        $azureData['TENANT_ID'] = getenv('TENANT_ID');
        $azureData['GRAPH_USER_SCOPES'] = getenv('GRAPH_USER_SCOPES');
        $azureData['REDIRECT_URI'] = getenv('REDIRECT_URI');
        return $azureData;
    }

    /**
     * Assert that we can use the validation code form
     * before any actual code is sent by the user
     */

    public function test_azure_mail_without_code(): void
    {
        $_POST['user_verification_code'] = null;
        $_SESSION['verification_code'] = null;
        $_SESSION['verification_attempts'] = 3;
        $_SESSION['timer_end'] = time() + 300;
        $_SESSION['new_user'] = true;
        $_SESSION['azure_mail'] = "fake@azurefake.fake";
        $_SESSION['form_email'] = "fake@fake.fake";
        $_SESSION['after_login_redirect'] = base_url();
        $result = $this->controller(Auth::class)->execute('verify_verification_code');
        $result->assertSee(lang('user_lang.user_validation_code'));
    }
  
    /**
     * Assert that inserting an invalid validation code while 
     * the timer hasn't yet expired generates
     * an error message
     */

    public function test_azure_mail_with_fake_code(): void
    {
        $_POST['user_verification_code'] = 'fake1';
        $_SESSION['verification_code'] = 'fake2';
        $_SESSION['verification_attempts'] = 3;
        $_SESSION['timer_end'] = time() + 300;
        $_SESSION['new_user'] = true;
        $_SESSION['azure_mail'] = "fake@azurefake.fake";
        $_SESSION['form_email'] = "fake@fake.fake";
        $_SESSION['after_login_redirect'] = base_url();
        $result = $this->controller(Auth::class)->execute('verify_verification_code');
        $result->assertSee(lang('user_lang.msg_err_validation_code'));
    }

    public function test_azure_mail_with_fake_code_all_attemps_done(): void
    {
        $_POST['user_verification_code'] = 'fake1';
        $_SESSION['verification_code'] = 'fake2';
        $_SESSION['verification_attempts'] = 1;
        $_SESSION['timer_end'] = time() + 300;
        $_SESSION['new_user'] = true;
        $_SESSION['azure_mail'] = "fake@azurefake.fake";
        $_SESSION['form_email'] = "fake@fake.fake";
        $_SESSION['after_login_redirect'] = base_url();
        $result = $this->controller(Auth::class)->execute('verify_verification_code');
        $this->assert_redirect($result);
    }

    /**
    * Asserts that when the user correctly inserts the validation code,
    * a new user is successfully created if the user is completly missing from
    * the DB 
    */
    public function test_azure_mail_with_correct_code_new_user(): void
    {
        $firstName = 'Firstname';
        $lastName = 'Lastname';
        $userName = "$firstName.$lastName";
        $_POST['user_verification_code'] = 'correct';
        $_SESSION['verification_code'] = 'correct';
        $_SESSION['verification_attempts'] = 3;
        $_SESSION['timer_end'] = time() + 300; // force timer_end to be greater than time()
        $_SESSION['after_login_redirect'] = base_url();
        // $is_code_valid should be set to true at this point in the method
        $_SESSION['new_user'] = true;
        $_SESSION['azure_mail'] = "$userName@azurefake.fake";
        $_SESSION['form_email'] = "fake@azurefake.fake";
    
        $form_email = 'fake@fake.fake';
        $result = $this->controller(Auth::class)
          ->execute('verify_verification_code', $form_email);
        $userModel = model(User_model::class);
        $name = $userModel->select('username')->where('username=', $userName)
                                              ->findAll()[0]['username'];

        $this->assertEquals($userName, $name);
    }

    /**
    * Asserts that the validation code is successfuly sent when the user
    * is already in the DB but doesn't have an azure_email. (Only an email)
    */

    public function test_azure_mail_existed_user_variable_created(): void
    {
        $userId = 2;
        $noAzureMail = 'fake@fake.fake';
        $userModel = model(User_model::class);
        $userModel->update($userId, ['email' => $noAzureMail]);
        $form_email = 'fake@fake.fake';
        $_POST['user_email'] = $form_email; // no azure_mail in DB
        // $_SESSION['new_user'] should be set to false by now

        $result = $this->controller(Auth::class)
          ->execute('handle_mail_form', $form_email);
        
        $result->assertSee(lang('user_lang.user_validation_code'));
    }

    /**
    * Asserts that the azure_mail is successfully updated with the email
    * when the user is not a new one (already registered in the DB)
    * and correctly inputs the validation code
    */
    public function test_azure_mail_with_correct_code_existing_user(): void
    {
        $userId = 2;
        $userModel = model(User_model::class);
        $_POST['user_verification_code'] = 'correct';
        $_SESSION['verification_code'] = 'correct';
        $_SESSION['verification_attempts'] = 3;
        $_SESSION['timer_end'] = time() + 300; // force timer_end to be greater than time()
        $_SESSION['after_login_redirect'] = base_url();
        $_SESSION['new_user'] = false;
        $_SESSION['azure_mail'] = "fake@azurefake.fake";
        $_SESSION['form_email'] = "fake@fake.fake";
        $redirect_url = $_SESSION['after_login_redirect'];

        $result = $this->controller(Auth::class)
          ->execute('verify_verification_code');
        
        /* Check that user's azure_mail has been updated in the DB */
        $azureMailInDb = $userModel->select('azure_mail')
                               ->find($userId)['azure_mail'];
        $this->assertEquals($_SESSION['azure_mail'], $azureMailInDb);

        /* Check that the user is logged in */
        $result->assertSessionHas('logged_in', true);

        /* Check if user has been redirected correctly */
        $result->assertRedirect();
        $result->assertRedirectTo($redirect_url);
    }

    /**
     * Insert a new user into database
     */

    private static function insertUser($userType, $username, $userEmail,
        $userPassword) {
        $user = array(
            'id' => 0,
            'fk_user_type' => $userType,
            'username' => $username,
            'email' => $userEmail,
            'password' => $userPassword,
            'password_confirm' => $userPassword,
        );

        $userModel = model(User_model::class);

        return $userModel->insert($user);
    }

    # Create a test for the secret_client ?
}
