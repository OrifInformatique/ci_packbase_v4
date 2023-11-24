<?php
/**
 * Unit tests AdminTest
 *
 * @author      Orif (CaLa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */

namespace User\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;

use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Test\TestResponse;
use User\Models\User_model;

class AdminTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    const ADMINISTRATOR_USER_TYPE = 1;
    const REGISTERED_USER_TYPE = 2;
    const GUEST_USER_TYPE = 3;

    private function get_session_data()
    {
        $data['logged_in'] = true;
        $data['user_access'] = Config('\User\Config\UserConfig')
            ->access_lvl_admin;
        $data['_ci_previous_url'] = 'url';
        return $data;
    }

    private function get_response_and_assert(TestResponse $result)
        : Response
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
     * Asserts that the list_user page is loaded correctly with an administrator session
     */
    public function testlist_userWithAdministratorSession() 
    {
        $_SESSION = $this->get_session_data();

        // Execute list_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('list_user');
        // Assertions
        $this->assert_reponse($result);
        $result->assertSeeLink('Nouveau');
        $result->assertSeeElement('#userslist');
        $result->assertSee(lang('user_lang.field_username'), 'th');
        $result->assertSee('Type d\'utilisateur', 'th');
        $result->assertSee('Activé', 'th');
        $result->assertDontSee('Fake User', 'th');
        $result->assertSeeLink('admin');
        $result->assertSeeLink('utilisateur');
    }

    /**
     * Asserts that the list_user page is loaded correctly with disabled users
     */
    public function testlist_userWithDisabledUsers() 
    {
        $_SESSION = $this->get_session_data();
        $userModel = model(User_model::class);
        $user_id = 1;
        // Disable user id 1
        $userModel->update($user_id, ['archive' => '2023-03-30 10:32:00']);
        // Execute list_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('list_user', true);
        // Enable user id 1
        $userModel->update($user_id, ['archive' => NULL]);
        // Assertions
        $this->assert_reponse($result);
        $result->assertSeeLink('Nouveau');
        $result->assertSeeElement('#userslist');
        $result->assertSee(lang('user_lang.field_username'), 'th');
        $result->assertSee('Type d\'utilisateur', 'th');
        $result->assertSee('Activé', 'th');
        $result->assertDontSee('Fake User', 'th');
        $result->assertSeeLink('admin');
        $result->assertSeeLink('utilisateur');
    }

    /**
     * Asserts that the list_user page is loaded correctly without disabled users (after disabling user id 1)
     */
    public function testlist_userWithoutDisabledUsers() 
    {
        $_SESSION = $this->get_session_data();
        $userModel = model(User_model::class);
        $user_id = 1;
        // Disable user id 1
        $userModel->update($user_id, ['archive' => '2023-03-30 10:32:00']);
        // Execute list_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('list_user');
        // Enable user id 1
        $userModel->update($user_id, ['archive' => NULL]);
        // Assertions
        $this->assert_reponse($result);
        $result->assertSeeLink('Nouveau');
        $result->assertSeeElement('#userslist');
        $result->assertSee(lang('user_lang.field_username'), 'th');
        $result->assertSee('Type d\'utilisateur', 'th');
        $result->assertSee('Activé', 'th');
        $result->assertDontSee('Fake User', 'th');
        $result->assertDontSeeLink('admin');
    }

    /**
     * Asserts that the password_change_user page is loaded correctly for the user id 1
     */
    public function testpassword_change_user() 
    {
        $_SESSION = $this->get_session_data();
        // Execute password_change_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('password_change_user', 1);
        // Assertions
        $this->assert_reponse($result);
        $result->assertSee('Réinitialiser le mot de passe', 'h1');
        $result->assertSee('admin', 'h4');
        $result->assertDontSee('Fake Reset', 'h1');
        $result->assertSeeElement('#password_new');
        $result->assertSeeElement('#password_confirm');
        $result->assertSeeElement('.btn btn-secondary');
        $result->assertSeeElement('.btn btn-primary');
    }

    /**
     * Asserts that the password_change_user page redirects to the list_user view for a non existing user
     */
    public function testpassword_change_userWithNonExistingUser() 
    {
        $_SESSION = $this->get_session_data();
        // Execute password_change_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('password_change_user', 999999);
        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('/user/admin/list_user'));
    }

    /**
     * Asserts that the delete_user page displays a warning message for the user id 1 (no session)
     */
    public function testdelete_userWithoutSession() 
    {
        $_SESSION = $this->get_session_data();
        // Execute delete_user method of Admin class (no action parameter is passed to avoid deleting)
        $result = $this->controller(Admin::class)
            ->execute('delete_user', 1);
        // Assertions
        $this->assert_reponse($result);
        $result->assertSee('Vous ne pouvez pas désactiver ou supprimer votre propre compte. Cette opération doit être faite par un autre administrateur.', 'div');
    }

    /**
     * Asserts that the delete_user page is loaded correctly for the user id 1 (with a session)
     */
    public function testdelete_userWithSessionAndDefaultAction() 
    {
        // Initialize session 
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 2;

        // Execute delete_user method of Admin class (no action parameter is passed to avoid deleting)
        $result = $this->controller(Admin::class)
            ->execute('delete_user', 1);
        // Assertions
        $this->assert_reponse($result);
        $result->assertSee('Que souhaitez-vous faire ?', 'h4');
        $result->assertSeeLink('Annuler');
        $result->assertSeeLink('Désactiver cet utilisateur');
        $result->assertSeeLink('Supprimer cet utilisateur');
    }

    /**
     * Asserts that the delete_user page is loaded correctly with a warning message
     */
    public function testdelete_userWithSessionAndDefaultActionForADisabledUser()
    {
        // Initialize the session
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 2;

        // Instantiate a new user model
        $userModel = model(User_model::class);

        $user_id = 1;

        // Disable user id 1
        $userModel->update($user_id, ['archive' => '2023-04-25']);

        // Execute delete_user method of Admin class (disable action parameter is passed)
        $result = $this->controller(Admin::class)
            ->execute('delete_user', $user_id);

        // Enable user id 1
        $userModel->update($user_id, ['archive' => NULL]);

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee('Cet utilisateur est déjà désactivé. Voulez-vous le supprimer définitivement ?', 'div');
    }

    /**
     * Asserts that the delete_user page redirects to the list_user view when a non existing user is given
     */
    public function testdelete_userWithNonExistingUser()
    {
        $_SESSION = $this->get_session_data();
        // Execute delete_user method of Admin class (no action parameter is passed to avoid deleting)
        $result = $this->controller(Admin::class)
            ->execute('delete_user', 999999);

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('user/admin/list_user'));
    }

    /**
     * Asserts that the delete_user page redirects to the list_user view when a fake action is given
     */
    public function testdelete_userWitFakeAction()
    {
        $_SESSION = $this->get_session_data();

        // Execute delete_user method of Admin class (fake action parameter is passed)
        $result = $this->controller(Admin::class)
            ->execute('delete_user', 1, 9);

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('user/admin/list_user'));
    }

    /**
     * Asserts that the delete_user page redirects to the list_user view when a disable action is given (session user id has to be different than user id to delete)
     */
    public function testdelete_userWitDisableAction()
    {
        // Initialize the session
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 2;

        // Instantiate a new user model
        $userModel = model(User_model::class);

        $user_id = 1;

        // Execute delete_user method of Admin class (disable action parameter is passed)
        $result = $this->controller(Admin::class)
            ->execute('delete_user', $user_id, 1);

        // Enable user id 1
        $userModel->update($user_id, ['archive' => NULL]);

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('user/admin/list_user'));
    }

    /**
     * Asserts that the delete_user page redirects to the list_user view when a delete action is given
     */
    public function testdelete_userWitDeleteAction()
    {
        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Initialize the session
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 1;

        // Inserts user into database
        $userType = self::GUEST_USER_TYPE;
        $username = 'UserUnitTest';
        $userEmail = 'userunittest@test.com';
        $userPassword = 'UsereUnitTestPassword';
        
        $userId = self::insertUser($userType, $username, $userEmail, $userPassword);

        // Execute delete_user method of Admin class (delete action parameter is passed)
        $result = $this->controller(Admin::class)
            ->execute('delete_user', $userId, 2);

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('user/admin/list_user'));
        $this->assertNull($userModel->where("id", $userId)->first());
    }

    /**
     * Asserts that the reactivate_user page redirects to the list_user view when a non existing user is given
     */
    public function testreactivate_userWithNonExistingUser()
    {
        $_SESSION = $this->get_session_data();

        // Execute reactivate_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('reactivate_user', 999999);

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('user/admin/list_user'));
    }

    /**
     * Asserts that the reactivate_user page redirects to the save_user view when an existing user is given
     */
    public function testreactivate_userWithExistingUser()
    {
        $_SESSION = $this->get_session_data();

        // Instantiate a new user model
        $userModel = model(User_model::class);

        $user_id = 1;

        // Disable user id 1
        $userModel->update($user_id, ['archive' => '2023-03-30 10:32:00']);

        // Execute reactivate_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('reactivate_user', $user_id);

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('/user/admin/save_user/1'));
    }

    /**
     * Asserts that the form_user page is loaded correctly for the user id 1 
     */
    public function testsave_userWithUserId() 
    {
        $_SESSION = $this->get_session_data();

        // Execute save_user method of Admin class 
        $result = $this->controller(Admin::class)
            ->execute('save_user', 1);

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee(lang('user_lang.title_user_update'), 'h1');
        $result->assertSeeElement('#user_form');
        $result->assertSee(lang('user_lang.field_username'), 'label');
        $result->assertSeeInField('user_name', 'admin');
        $result->assertSee(lang('user_lang.field_email'), 'label');
        $result->assertSeeInField('user_email', '');
        $result->assertSee('Type d\'utilisateur', 'label');
        $result->assertSeeElement('#user_usertype');
        $result->assertSee('Administrateur', 'option');
        $result->assertSee('Enregistré', 'option');
        $result->assertSee('Invité', 'option');
        $result->assertDontSee('Mot de passe', 'label');
        $result->assertDontSeeElement('#user_password');
        $result->assertDontSee('Confirmer le mot de passe', 'label');
        $result->assertDontSeeElement('#user_password_again');
        $result->assertSeeLink('Réinitialiser le mot de passe');
        $result->assertSeeLink('Désactiver ou supprimer cet utilisateur');
        $result->assertSeeElement('.btn btn-secondary');
        $result->assertSeeElement('.btn btn-primary');
        $result->assertSeeLink('Annuler');
        $result->assertSeeInField('save', 'Enregistrer');
    }

    /**
     * Asserts that the form_user page is loaded correctly for a new user (no user id)
     */
    public function testsave_userWithoutUserId() 
    {
        $_SESSION = $this->get_session_data();
        // Execute save_user method of Admin class 
        $result = $this->controller(Admin::class)
            ->execute('save_user');

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee('Ajouter un utilisateur', 'h1');
        $result->assertSeeElement('#user_form');
        $result->assertSee(lang('user_lang.field_username'), 'label');
        $result->assertSeeInField('user_name', '');
        $result->assertSee(lang('user_lang.field_email'), 'label');
        $result->assertSeeInField('user_email', '');
        $result->assertSee('Type d\'utilisateur', 'label');
        $result->assertSeeElement('#user_usertype');
        $result->assertSee('Administrateur', 'option');
        $result->assertSee('Enregistré', 'option');
        $result->assertSee('Invité', 'option');
        $result->assertSee('Mot de passe', 'label');
        $result->assertSeeElement('#user_password');
        $result->assertSee('Confirmer le mot de passe', 'label');
        $result->assertSeeElement('#user_password_again');
        $result->assertSeeElement('.btn btn-secondary');
        $result->assertSeeElement('.btn btn-primary');
        $result->assertSeeLink('Annuler');
        $result->assertSeeInField('save', 'Enregistrer');
    }

    /**
     * Asserts that the form_user page is loaded correctly for the user id 1 with the session user id 1
     */
    public function testsave_userWithUserIdWithSameSessionUserId() 
    {
        // Initialize the session
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 1;

        // Execute save_user method of Admin class 
        $result = $this->controller(Admin::class)
            ->execute('save_user', 1);

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee(lang('user_lang.title_user_update'), 'h1');
        $result->assertSeeElement('#user_form');
        $result->assertSee(lang('user_lang.field_username'), 'label');
        $result->assertSeeInField('user_name', 'admin');
        $result->assertSee(lang('user_lang.field_email'), 'label');
        $result->assertSeeInField('user_email', '');
        $result->assertSee(lang('user_lang.user_update_usertype_himself'), 'div');
        $result->assertSeeElement('#user_usertype');
        $result->assertSee('Administrateur', 'option');
        $result->assertSee('Enregistré', 'option');
        $result->assertSee('Invité', 'option');
        $result->assertDontSee('Mot de passe', 'label');
        $result->assertDontSeeElement('#user_password');
        $result->assertDontSee('Confirmer le mot de passe', 'label');
        $result->assertDontSeeElement('#user_password_again');
        $result->assertSeeLink('Réinitialiser le mot de passe');
        $result->assertSeeLink('Désactiver ou supprimer cet utilisateur');
        $result->assertSeeElement('.btn btn-secondary');
        $result->assertSeeElement('.btn btn-primary');
        $result->assertSeeLink('Annuler');
        $result->assertSeeInField('save', 'Enregistrer');
    }

    /**
     * Asserts that the form_user page is loaded correctly for a disabled user id
     */
    public function testsave_userWithDisabledUserId()
    {
        $_SESSION = $this->get_session_data();

        // Instantiate a new user model
        $userModel = model(User_model::class);

        $user_id = 1;

        // Disable user id 1
        $userModel->update($user_id, ['archive' => '2023-03-30 10:32:00']);

        // Execute save_user method of Admin class 
        $result = $this->controller(Admin::class)
            ->execute('save_user', 1);

        // Enable user id 1
        $userModel->update($user_id, ['archive' => NULL]);

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee(lang('user_lang.title_user_update'), 'h1');
        $result->assertSee(lang('user_lang.user_disabled_info'), 'div');
        $result->assertSeeElement('#user_form');
        $result->assertSee(lang('user_lang.field_username'), 'label');
        $result->assertSeeInField('user_name', 'admin');
        $result->assertSee(lang('user_lang.field_email'), 'label');
        $result->assertSeeInField('user_email', '');
        $result->assertSee('Type d\'utilisateur', 'label');
        $result->assertSeeElement('#user_usertype');
        $result->assertSee('Administrateur', 'option');
        $result->assertSee('Enregistré', 'option');
        $result->assertSee('Invité', 'option');
        $result->assertDontSee('Mot de passe', 'label');
        $result->assertDontSeeElement('#user_password');
        $result->assertDontSee('Confirmer le mot de passe', 'label');
        $result->assertDontSeeElement('#user_password_again');
        $result->assertSeeLink('Réinitialiser le mot de passe');
        $result->assertSeeLink('Réactiver cet utilisateur');
        $result->assertSeeLink('Supprimer cet utilisateur');
        $result->assertSeeElement('.btn btn-secondary');
        $result->assertSeeElement('.btn btn-primary');
        $result->assertSeeLink('Annuler');
        $result->assertSeeInField('save', 'Enregistrer');
    }

    /**
     * Asserts that the password_change_user page redirects to the list_user view after updating the password (POST)
     */
    public function testpassword_change_userPostedWhenChangingPassword()
    {
        $_SESSION = $this->get_session_data();

        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = self::GUEST_USER_TYPE;
        $username = 'UserChangePasswordUnitTest';
        $userEmail = 'userunittest@test.com';
        $userPassword = 'UserUnitTestPassword';
        $userNewPassword = 'UserUnitTestNewPassword';
        $userId = self::insertUser($userType, $username, $userEmail, $userPassword);

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['id'] = $userId;
        $_REQUEST['id'] = $userId;
        $_POST['password_new'] = $userNewPassword;
        $_REQUEST['password_new'] = $userNewPassword;
        $_POST['password_confirm'] = $userNewPassword;
        $_REQUEST['password_confirm'] = $userNewPassword;

        // Execute password_change_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('password_change_user', $userId);

        // Deletes inserted user
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_redirect($result);
        $result->assertRedirectTo(base_url('/user/admin/list_user'));
    }

    /**
     * Asserts that the password_change_user page displays an error message
     */
    public function testpassword_change_userPostedWhenChangingPasswordWithError()
    {
        $_SESSION = $this->get_session_data();

        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = self::GUEST_USER_TYPE;
        $username = 'UserChangePasswordUnitTest';
        $userEmail = 'userunittest@test.com';
        $userPassword = 'UserUnitTestPassword';
        $userNewPassword = 'UserUnitTestNewPassword';
        $userId = self::insertUser($userType, $username, $userEmail, $userPassword);

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['id'] = $userId;
        $_REQUEST['id'] = $userId;
        $_POST['password_new'] = $userNewPassword;
        $_REQUEST['password_new'] = $userNewPassword;
        $_POST['password_confirm'] = $userPassword;
        $_REQUEST['password_confirm'] = $userPassword;

        // Execute password_change_user method of Admin class
        $result = $this->controller(Admin::class)
            ->execute('password_change_user', $userId);

        // Deletes inserted user
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee('Le mot de passe ne coïncide pas avec la confirmation du mot de passe.', 'div');        
    }

    /**
     * Asserts that the save_user page redirects to the list_user view after inserting a new user (POST)
     */
    public function testsave_userPostedForANewUser()
    {
        // Initialize session
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 1;

        // Instantiate a new user model
        $userModel = model(User_model::class);

        $username = 'UserSaveUserUnitTest';

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['id'] = 0;
        $_REQUEST['id'] = 0;
        $_POST['user_name'] = $username;
        $_REQUEST['user_name'] = $username;
        $_POST['user_email'] = 'usersaveuserunittest@test.com';
        $_REQUEST['user_email'] = 'usersaveuserunittest@test.com';
        $_POST['user_usertype'] = self::GUEST_USER_TYPE;
        $_REQUEST['user_usertype'] = self::GUEST_USER_TYPE;
        $_POST['user_password'] = 'UserUnitTestPassword';
        $_REQUEST['user_password'] = 'UserUnitTestPassword';
        $_POST['user_password_again'] = 'UserUnitTestPassword';
        $_REQUEST['user_password_again'] = 'UserUnitTestPassword';

        // Execute save_user method of Admin class 
        $result = $this->controller(Admin::class)
            ->execute('save_user');

        // Get user from database
        $userDb = $userModel->where("username", $username)->first();

        // Deletes inserted user
        $userModel->delete($userDb['id'], TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_redirect($result);
        $this->assertNotNull($userDb);
        $result->assertRedirectTo(base_url('/user/admin/list_user'));
    }

    /**
     * Asserts that the save_user page is loaded correctly displaying an error message
     */
    public function testsave_userPostedForANewUserWithError()
    {
        $username = 'UserSaveUserUnitTest';

        // Initialize session
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 1;

        // Prepare the POST request
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['id'] = 0;
        $_REQUEST['id'] = 0;
        $_POST['user_name'] = $username;
        $_REQUEST['user_name'] = $username;
        $_POST['user_email'] = 'usersaveuserunittest@test.com';
        $_REQUEST['user_email'] = 'usersaveuserunittest@test.com';
        $_POST['user_usertype'] = self::GUEST_USER_TYPE;
        $_REQUEST['user_usertype'] = self::GUEST_USER_TYPE;
        $_POST['user_password'] = 'UserUnitTestPassword';
        $_REQUEST['user_password'] = 'UserUnitTestPassword';
        $_POST['user_password_again'] = 'UserUnitTestPasswordError';
        $_REQUEST['user_password_again'] = 'UserUnitTestPasswordError';

        // Execute save_user method of Admin class 
        $result = $this->controller(Admin::class)
            ->execute('save_user');

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_reponse($result);
        $result->assertSee('Le mot de passe ne coïncide pas avec la confirmation du mot de passe.', 'div');
    }

    /**
     * Asserts that the save_user page redirects to the list_user view after updating an existing user (POST)
     */
    public function testsave_userPostedForAnExistingUser()
    {
        // Initialize session
        $_SESSION = $this->get_session_data();
        $_SESSION['user_id'] = 1;

        // Instantiate a new user model
        $userModel = model(User_model::class);

        // Inserts user into database
        $userType = self::GUEST_USER_TYPE;
        $username = 'SaveUserUnitTest';
        $userEmail = 'usersaveuserunittest@test.com';
        $userPassword = 'UnitTestPassword';        
        $userId = self::insertUser($userType, $username, $userEmail, $userPassword);
        
        // Prepare the POST request to update this user
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_POST['id'] = $userId;
        $_REQUEST['id'] = $userId;
        $_POST['user_name'] = $username;
        $_REQUEST['user_name'] = $username;
        $_POST['user_email'] = $userEmail;
        $_REQUEST['user_email'] = $userEmail;
        $_POST['user_usertype'] = self::REGISTERED_USER_TYPE;
        $_REQUEST['user_usertype'] = self::REGISTERED_USER_TYPE;

        // Execute save_user method of Admin class 
        $result = $this->controller(Admin::class)
            ->execute('save_user', $userId);

        // Get user from database after update 
        $userDbUpdate = $userModel->where("username", $username)->first();

        // Deletes inserted user
        $userModel->delete($userId, TRUE);

        // Reset $_POST and $_REQUEST variables
        $_POST = array();
        $_REQUEST = array();

        // Assertions
        $this->assert_redirect($result);
        $this->assertEquals($userDbUpdate['fk_user_type'], self::REGISTERED_USER_TYPE);
        $this->assertEquals($userDbUpdate['email'], $userEmail);
        $result->assertRedirectTo(base_url('/user/admin/list_user'));
    }

    /**
     * Insert a new user into database
     */
    private static function insertUser($userType, $username, $userEmail, $userPassword) {
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
}
