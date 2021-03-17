<?php
/**
 * User Authentication
 *
 * @author      Orif (ViDi)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
namespace User\Controllers;
use \User\Models as models;

class Auth extends \App\Controllers\BaseController {

    /**
     * Constructor
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        $this->user_model=new models\User_model();
        // Load form helper
        helper('form');

        // Load the validation service to validate the form
        $this->validation = \Config\Services::validation();

        // Accessibility for all users to let visitors have access to authentication
        $this->access_level = "*";
    }

    /**
     * Login user and create session variables
     *
     * @return void
     */
    public function login()
    {
        // If user already logged
        if(!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)) {

            // Store the redirection URL in a session variable
            // in codeigniter 4 $this->input is remplaced by $this->request->getVar()
            if (!is_null($this->request->getVar('after_login_redirect'))) {
                $_SESSION['after_login_redirect'] = $this->request->getVar('after_login_redirect');
            }
            // If no redirection URL is provided or the redirection URL is the
            // login form, redirect to site's root after login
            if (!isset($_SESSION['after_login_redirect'])
                    || $_SESSION['after_login_redirect'] == current_url()) {

                $_SESSION['after_login_redirect'] = base_url();
            }

            // Check if the form has been submitted, else just display the form
            if (!is_null($this->request->getVar('btn_login'))) {

                // Define fields validation rules
                //this is only available in codeigniter3
                /*$validation_rules = array(
                    array(
                        'field' => 'username',
                        'label' => 'lang:field_username',
                        'rules' => 'trim|required|'
                            . 'min_length['.$this->config->item('username_min_length').']|'
                            . 'max_length['.$this->config->item('username_max_length').']'
                    ),
                    array(
                        'field' => 'password',
                        'label' => 'lang:field_password',
                        'rules' => 'trim|required|'
                            . 'min_length['.$this->config->item('password_min_length').']|'
                            . 'max_length['.$this->config->item('password_max_length').']'
                    )
                );
                */
                $validation_rules=[
                    'username'=>[
                    'label' => 'My_user_lang.field_username',
                    'rules' => 'trim|required|'
                        . 'min_length['.config("\User\Config\UserConfig")->username_min_length.']|'
                        . 'max_length['.config("\User\Config\UserConfig")->username_max_length.']'],
                    'password'=>[
                        'label' => 'My_user_lang.field_password',
                        'rules' => 'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']'
                    ]
                    ];
                //set validation rules in codeigniter 4
                $this->validation->setRules($validation_rules);
                //$this->form_validation->set_rules($validation_rules);
                // Check fields validation rules

                if ($this->validation->withRequest($this->request)->run() == true) {
                    $input = $this->request->getVar('username');
                    $password = $this->request->getvar('password');
                    $ismail = $this->user_model->check_password_email($input, $password);
                    if ($ismail || $this->user_model->check_password_name($input, $password)) {
                        // Login success
                        $user = NULL;
                        // User is either logging in through an email or an username
                        // Even if an username is entered like an email, we're not grabbing it
                        if ($ismail) {
                            //$user = $user_model->with('user_type')
                            //                         ->get_by('email', $input);
                            $user = $this->user_model->getWhere(['email'=>$input])->getRow();
                        } else {
                            //$user = $user_model->with('user_type')
                            //                         ->get_by('username', $input);
                            $user = $this->user_model->getWhere(['username'=>$input])->getRow();
                        }

                        // Set session variables
                        $_SESSION['user_id'] = (int)$user->id;
                        $_SESSION['username'] = (string)$user->username;
                        $_SESSION['user_access'] = (int)$this->user_model->get_access_level($user);
                        $_SESSION['logged_in'] = (bool)true;

                        // Send the user to the redirection URL
                        return redirect()->to($_SESSION['after_login_redirect']);

                    } else {
                        // Login failed
                        $this->session->setFlashdata('message-danger', lang('My_user_lang.msg_err_invalid_password'));
                    }
                    $this->session->setFlashdata('message-danger', lang('My_user_lang.msg_err_invalid_password'));
                }
            }

            // Display login page
            $output = array('title' => lang('MY_user_lang.title_page_login'));
            $this->display_view('\User\auth\login', $output);
        } else {
            return redirect()->to(base_url());
        }
    }

    /**
     * Logout and destroy session
     *
     * @return void
     */
    public function logout()
    {
        // Restart session with empty parameters
        $_SESSION = [];
        session_reset();
        session_unset();

        return redirect()->to(base_url());
    }

    /**
     * Displays a form to let user change his password
     *
     * @return void
     */
    public function change_password()
    {
        // Check if access is allowed
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {

            // Check if the form has been submitted, else just display the form
            if (!is_null($this->request->getVar('btn_change_password'))) {
                $username = $_SESSION["username"];

                $validation_rules =[
                    'old_password'=>[
                        'label' => 'My_user_lang.field_old_password',
                        'rules' => 'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']|'
                            . 'old_password_check['.$username.']',
                        'errors' => ['old_password_check' => lang('MY_user_lang.msg_err_invalid_old_password')]
                    ],
                    'new_password'=>[
                        'label' =>'My_user_lang.field_new_password',
                        'rules' =>'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']'
                    ],
                    'confirm_password'=>[
                        'label' =>'My_user_lang.field_password_confirm',
                        'rules' =>'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']|'
                            . 'matches[new_password]'
                    ]
                ];
                $this->validation->setRules($validation_rules);

                // Check fields validation rules
                if ($this->validation->withRequest($this->request)->run() == true) {
                    $old_password = $this->request->getVar('old_password');
                    $new_password = $this->request->getVar('new_password');
                    $confirm_password = $this->request->getVar('confirm_password');

                    $this->user_model=new models\User_model();
                    $this->user_model->update($_SESSION['user_id'],
                            array("password" => password_hash($new_password, config("\User\Config\UserConfig")->password_hash_algorithm)));

                    // Send the user back to the site's root
                    return redirect()->to(base_url());
                }
            }

            // Display the password change form
            $output['title'] = lang('My_user_lang.page_my_password_change');
            $this->display_view('\User\auth\change_password', $output);
        } else {
            // Access is not allowed
            return redirect()->to('user/auth/login');
        }
    }
}
