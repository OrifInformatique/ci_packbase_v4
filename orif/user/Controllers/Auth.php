<?php
/**
 * User Authentication
 *
 * @author      Orif (ViDi,HeMa,MoDa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
namespace User\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use User\Models\User_model;
use User\Models\User_type_model;
use CodeIgniter\Email\Email;
use CodeIgniter\HTTP\Response;
use Config\UserConfig;
use User\Controllers\Profile;

    class Auth extends BaseController {

    /**
     * Constructor
     */
    
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger): void
    {
        // Set Access level before calling parent constructor
        // Accessibility for all users to let visitors have access to authentication
        $this->access_level = "*";
        parent::initController($request, $response, $logger);
        
        // Load required helpers
        helper('form');

        // Load required services
        $this->validation = \Config\Services::validation();

        // Load required models
        $this->user_model = new User_model();

        $this->db = \Config\Database::connect();
    }

    /**
     * Login user and create session variables
     *
     * @return void
     */

    public function login(): string|Response {
        // If user is not already logged
        if(!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)) {

            // Store the redirection URL in a session variable
            if (!is_null($this->request->getVar('after_login_redirect'))) {
                $_SESSION['after_login_redirect'] = $this->request->getVar('after_login_redirect');
            }
            // If no redirection URL is provided or the redirection URL is the
            // login form, redirect to site's root after login
            if (!isset($_SESSION['after_login_redirect'])
                    || $_SESSION['after_login_redirect'] == current_url()) {

                $_SESSION['after_login_redirect'] = base_url();
            }

            // Check if the form has been submitted, else check if Microsoft button submitted
            if (!is_null($this->request->getVar('btn_login'))) {

                // Define fields validation rules
                $validation_rules=[
                    'username'=>[
                    'label' => 'user_lang.field_username',
                    'rules' => 'trim|required|'
                        . 'min_length['.config("\User\Config\UserConfig")->username_min_length.']|'
                        . 'max_length['.config("\User\Config\UserConfig")->username_max_length.']'],
                    'password'=>[
                        'label' => 'user_lang.field_password',
                        'rules' => 'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']'
                    ]
                ];
                $this->validation->setRules($validation_rules);

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
                            $user = $this->user_model->getWhere(['email'=>$input])->getRow();
                        } else {
                            $user = $this->user_model->getWhere(['username'=>$input])->getRow();
                        }
                        
                        // return redirect()->to('/user/auth/login');
                        $_SESSION['user_id'] = (int)$user->id;
                        // $profile_controller = new Profile(); 
                        // $profile_controller -> checkForceChangePassword();

                        $_SESSION['username'] = (string)$user->username;
                        $_SESSION['user_access'] = (int)$this->user_model->get_access_level($user);
                        $_SESSION['logged_in'] = (bool)true;
                        
                        // force user to change password if told so
                        if  ($user->reset_password = 1) {
                            return redirect()->to(base_url("user/auth/change_password"));
                        }

                        // Send the user to the redirection URL
                        return redirect()->to($_SESSION['after_login_redirect']);

                    } else {
                        // Login failed
                        $this->session->setFlashdata('message-danger', lang('user_lang.msg_err_invalid_password'));
                    }
                    $this->session->setFlashdata('message-danger', lang('user_lang.msg_err_invalid_password'));
                }

            // Check if microsoft login button submitted, else, display login page
            } else if (!is_null($this->request->getPost('btn_login_microsoft'))) {
                $this->azure_login();
                exit();
            }
            //Display login page
            $output = array('title' => lang('user_lang.title_page_login'));
            return $this->display_view('\User\auth\login', $output);
        } else {
            return redirect()->to(base_url());
        }
    }

    /**
     * Initiate the communication with Microsoft Azure for the oAuth2.0 login system
     *
     * @return ???
     */
    public function azure_login() {

        $client_id = getenv('CLIENT_ID');
        $client_secret = getenv('CLIENT_SECRET');
        $ad_tenant = getenv('TENANT_ID');
        $graphUserScopes = getenv('GRAPH_USER_SCOPES');
        $redirect_uri = getenv('REDIRECT_URI');
        
        // Authentication part begins
        if (!isset($_GET["code"]) and !isset($_GET["error"])) {
            
            // First stage of the authentication process
            $url = "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/authorize?";
            $url .= "state=" . session_id();
            $url .= "&scope=" . $graphUserScopes;
            $url .= "&response_type=code";
            $url .= "&approval_prompt=auto";
            $url .= "&client_id=" . $client_id;
            $url .= "&redirect_uri=" . urlencode($redirect_uri);
            header("Location: " . $url);  // Redirection to Microsoft's login page

        // Second stage of the authentication process
        } elseif (isset($_GET["error"])) {

            $data['Exception'] = null;
            $this->errorhandler($data);

        //Checking that the session_id matches to the state for security reasons
        } elseif (strcmp(session_id(), $_GET["state"]) == 0) {
            
            //Verifying the received tokens with Azure and finalizing the authentication part
            $content = "grant_type=authorization_code";
            $content .= "&client_id=" . $client_id;
            $content .= "&redirect_uri=" . urlencode($redirect_uri);
            $content .= "&code=" . $_GET["code"];
            $content .= "&client_secret=" . urlencode($client_secret);
            $options = array(
                "http" => array(  //Use "http" even if you send the request with https
                "method"  => "POST",
                "header"  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen($content) . "\r\n",
                "content" => $content
                )
            );
            $context  = stream_context_create($options);

            // Special error handler to verify if "client secret" is still valid
            try {
                $json = file_get_contents("https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/token", false, $context);
            } catch (\Exception $e) {
                $data['title'] = 'Azure error';
                $data['Exception'] = $e;
                return $this->display_view('\User\errors\401error', $data);
            };

            if ($json === false){
                //Error received during Bearer token fetch
                $data['Exception'] = lang('user_lang.msg_err_azure_no_token').'.';
                $this->errorhandler($data);
            };
            $authdata = json_decode($json, true);
            if (isset($authdata["error"])){
                //Bearer token fetch contained an error
                $data['Exception'] = null;
                $this->errorhandler($data);
            };
            
            //Fetching user information
            $options = array(
                "http" => array(  //Use "http" even if you send the request with https
                "method" => "GET",
                "header" => "Accept: application/json\r\n" .
                "Authorization: Bearer " . $authdata["access_token"] . "\r\n"
                )
            );
            $context = stream_context_create($options);
            $json = file_get_contents("https://graph.microsoft.com/v1.0/me", false, $context);
            if ($json === false) {
                // Error received during user data fetch.
                $data['Exception'] = null;
                $this->errorhandler($data);
            };

            $userdata = json_decode($json, true);

            if (isset($userdata["error"])) {
                // User data fetch contained an error.
                $data['Exception'] = null;
                $this->errorhandler($data);
            };

            // Setting up the session
            $_SESSION['azure_identification'] = (bool)true;

            // Mail correspondances

            // Definition of ci_user_azure
            $user_azure_mail = $userdata["mail"];
            $ci_user_azure = $this->user_model->where('azure_mail', $user_azure_mail)->first();

            // Azure mail not found in DB
            if (empty($ci_user_azure)){

                $_SESSION['user_id'] = NULL;
                $_SESSION['username'] = $userdata['displayName'];
                $_SESSION['user_access'] = config("\User\Config\UserConfig")->azure_default_access_lvl; // guest access
                $_SESSION['azure_mail'] = $user_azure_mail;
                $_SESSION['form_email'] = NULL;
                
                return redirect()->to(base_url("user/auth/prepare_mail_form"));
            
            // Azure mail found
            } else {
                $_SESSION['user_id'] = $ci_user_azure['id'];
                $_SESSION['username'] = $ci_user_azure['username'];
                $_SESSION['user_access'] = (int)$this->user_model->get_access_level($ci_user_azure);
                $_SESSION['logged_in'] = (bool)true;

                return redirect()->to($_SESSION['after_login_redirect']);
            };

        } else {
            // Returned states mismatch and no $_GET["error"] received.
            $data['Exception'] = lang('user_lang.msg_err_azure_mismatch').'.';
            $this->errorhandler($data);
        }
    }

    /**
     * Prepares the mail form and checks if the Azure mail already registered in the DB
     *
     * @return ???
     */
    public function prepare_mail_form(): string {
        // Seperate name and lastname from email for mail correspondances
        $nameAndLastname = strstr($_SESSION['azure_mail'], '@', true); // True = before '@' and without '@'

        $correspondingUser = $this->user_model->where('email LIKE', $nameAndLastname . '%')->first(); // Orif mail

        // Check if orif mail already in DB
        if ($correspondingUser == NULL){
            $correspondingEmail = '';
        } else { // No correspondance
            $correspondingEmail = $correspondingUser['email'];
        }

        // send correspondance if found for the auto complete
        $output = array(
            'title' => lang('user_lang.title_page_login'),
            'correspondingEmail' => $correspondingEmail,
            'azure_mail' => $_SESSION['azure_mail']);

        // Ask user for his orif mail
        return $this->display_view('\User\auth\mail_form', $output);
    }

    /**
     * Prepares the mail form and checks if the Azure mail already registered in the DB
     *
     * @return html Display the form view to verify the expiration code.
     */
    public function handle_mail_form(): string {
        
        // Get user email from mail form
        $_SESSION['form_email'] = $this->request->getPost('user_email');

        // check if the orif mail input from mail form is already in DB
        $ci_user = $this->user_model->where('email', $_SESSION['form_email'])->first();
        if (isset($ci_user['email']) && !empty($ci_user['email'])) {
            $_SESSION['new_user'] = false;
        } else {
            $_SESSION['new_user'] = true;
        }

        // Set the number of attempts before sending the code via mail
        $_SESSION['verification_attempts'] = 3;

        return $this->generate_send_verification_code($_SESSION['form_email']);
    }

    /**
     * Generates verification code, starts the expiration time and sends the verification code 
     * via SMTP (mail) to the user. 
     *
     * @return html Display the form view to verify the expiration code.
     */
    public function generate_send_verification_code($form_email): string { // generate code and send mail

        // Random code generator
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $verification_code = '';

        for ($i =0; $i < 6; $i++) {
            $verification_code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // setup SMTP
        $email = \Config\Services::email();
                
        $emailConfig = [
            'protocol' => getenv('PROTOCOL'),
            'SMTPHost' => getenv('SMTP_HOST'),
            'SMTPUser' => getenv('SMTP_ID'),
            'SMTPPass' => getenv('SMTP_PASSWORD'),
            'SMTPPort' => getenv('SMTP_PORT'),
        ];

        $appTitle = lang('common_lang.app_title');

        $email->initialize($emailConfig);

        // Sending code to user's orif  mail
        $email->setFrom('smtp@sectioninformatique.ch', 'ORIF: Vérification du mail'); // 2nd paramater hard coded since variable not interpreted in SetFrom
        $email->setTo($form_email);
        $email->setSubject('Code de vérification');
        $email->setMessage('Voici votre code de vérification: '.$verification_code);
        $email->send();

        // Set code's expiration timer
        $_SESSION['timer_start'] = time();
        $_SESSION['timer_limit'] = 300; // Written in seconds. 300 = 5 minutes
        $_SESSION['timer_end'] = $_SESSION['timer_start'] + $_SESSION['timer_limit'];

        $_SESSION['verification_code'] = $verification_code;

        $data = array(
            'title' => lang('user_lang.field_verification_code'),
            'timer_start' => $_SESSION['timer_start'],
            'timer_limit' => $_SESSION['timer_limit'],
            'timer_end'   => $_SESSION['timer_end'],
        );
        
        return $this->display_view('\User\auth\verification_code_form', $data);
    }

    public function verify_verification_code() {

        $user_verification_code = $this->request->getPost('user_verification_code');
        
        if ($user_verification_code == $_SESSION['verification_code'] && time() < $_SESSION['timer_end']){
            $is_code_valid = true; // The code if valid
        } else {
            $is_code_valid = false;
        }; // Code is not valid (bad code or expired)

        if ($is_code_valid == true) { // Code valid, check is 

            if ($_SESSION['new_user'] == true)  {

                // A new user needs to be created in the db 
                // Receive array $user from register_user()
                $new_user = $this->register_user();
               
                // insert this new user
                $this->user_model->insert($new_user);

                $_SESSION['logged_in'] = (bool)true;

            } else {

                // User already in DB => Update azure_mail in DB
               
                $ci_user = $this->user_model->where('email', $_SESSION['form_email'])->first();
               
                // Verification code matches
                $_SESSION['user_access'] = (int)$this->user_model->get_access_level($ci_user);
                $_SESSION['user_id'] = (int)$ci_user['id'];
                $_SESSION['username'] = $ci_user['username'];
   
                $data = [
                    'azure_mail' => $_SESSION['azure_mail']
                ];
               
                $this->user_model->update($ci_user['id'], $data);
                
                $_SESSION['logged_in'] = (bool)true;
            }

        } else { // Code is not valid for any reason (false and/or expired)

            $_SESSION['verification_attempts'] -= 1;
  
            if ($_SESSION['verification_attempts'] <= 0) {
                // No more attempts, keep default user access, reset some session variables and redirect to after_login_redirect
            } else {
                $output = array(
                    'title' => lang('user_lang.title_validation_code'),
                    'errorMsg' => lang('user_lang.msg_err_validation_code'),
                    'attemptsLeft' => $_SESSION['verification_attempts'],
                    'msg_attemptsLeft' => lang('user_lang.msg_err_attempts') 
                    . ' ' . $_SESSION['verification_attempts'],
                );
   
                return $this->display_view(
                    '\User\auth\verification_code_form',
                    $output
                );
            }
        }

        // todo redirect to reset sessions method
        return $this->reset_session();

    }

    public function register_user() {
        
        $user_type_model = new User_type_model();
        $user_config = config('\User\Config\UserConfig');

        // Setting up default azure access level
        $default_access_level = $user_config->azure_default_access_lvl;
        $new_user_type =  $user_type_model
            ->where("access_level = ".$default_access_level)
            ->first();

        // Generating username
        $username_max_length = $user_config->username_max_length;
        $new_username = explode('@', $_SESSION['azure_mail']);
        $new_username = substr($new_username[0], 0, $username_max_length);

        // Generating a random password
        $password_max_lenght = $user_config->password_max_length;
        $new_password = '';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz'
            .'ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-={}[]|:;"<>,.?/~`';

        for ($i = 0; $i < $password_max_lenght; $i++) {
            $new_password .= $characters[rand(0, strlen($characters) - 1)];
        }
        $reset_password = True;
        $new_user = array(
            'fk_user_type'      => $new_user_type['id'],
            'username'          => $new_username,
            'password'          => $new_password,
            'password_confirm'  => $new_password,
            'reset_password'    => $reset_password,
            'email'             => $_SESSION['form_email'],
            'azure_mail'        => $_SESSION['azure_mail'],
        );
        
        return $new_user;
    }

    public function reset_session() {
        // Reset session variables either on success or on complete failure
        $_SESSION['form_email'] = null;
        $_SESSION['new_user'] = null;
        $_SESSION['azure_mail'] = null; 
        $_SESSION['verification_attempts'] = null;
        $_SESSION['verification_code'] = null;
        $_SESSION['timer_end'] = null;
        $_SESSION['timer_limit'] = null;
        $_SESSION['test'] = null;
        
        // Send the user to the redirection URL
        return redirect()->to($_SESSION['after_login_redirect']);
    }

    function errorhandler($data) {
        $data['title'] = 'Azure error';
        echo $this->display_view('\User\errors\azureErrors', $data);
        exit();
    }

    /**
     * Displays a form to let user change his password
     *
     * @return void
     */
    public function change_password(): Response|string {

        // Get user from DB, redirect if user is not defined or doesn't exist
        if(isset($_SESSION['user_id'])) {
            $user = $this->user_model->withDeleted()->find($_SESSION['user_id']);
            if (is_null($user)) return redirect()->to('/user/auth/login');
        } else {
            return redirect()->to('/user/auth/login');
        }

        // Empty errors message in output
        $output['errors'] = [];
        // Check if the form has been submitted, else just display the form
        if (!is_null($this->request->getVar('btn_change_password'))) {
            $old_password = $this->request->getVar('old_password');

            if($this->user_model->check_password_name($user['username'], $old_password)) {
                $user['password'] = $this->request->getVar('new_password');
                $user['password_confirm'] = $this->request->getVar('confirm_password');

                $this->user_model->update($user['id'], $user);

                if ($this->user_model->errors()==null) {
                    // No error happened, redirect
                    $user['reset_password'] = 0; // false
                    $this->user_model->update($user['id'], $user);
                    return redirect()->to(base_url());
                } else {
                    // Display error messages
                    $output['errors'] = $this->user_model->errors();
                }

            } else {
                // Old password error
                $output['errors'][] = lang('user_lang.msg_err_invalid_old_password');
            }
        }

        // Display the password change form
        $output['title'] = lang('user_lang.page_my_password_change');
        return $this->display_view('\User\auth\change_password', $output);

    }
    /**
     * Logout and destroy session
     *
     * @return void
     */
    public function logout(): Response
    {
        // Restart session with empty parameters
        $_SESSION = [];
        session_reset();
        session_unset();

        return redirect()->to(base_url());
    }

}
