<?php
/**
 * User Authentication
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
namespace User\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use User\Models\User_model;

class Auth extends BaseController {

    /**
     * Constructor
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
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

    function errorhandler($input, $email)
    {
    $output = "PHP Session ID:    " . session_id() . PHP_EOL;
    $output .= "Client IP Address: " . getenv("REMOTE_ADDR") . PHP_EOL;
    $output .= "Client Browser:    " . $_SERVER["HTTP_USER_AGENT"] . PHP_EOL;
    $output .= PHP_EOL;
    ob_start();  //Start capturing the output buffer
    var_dump($input);  //This is not for debug print, this is to collect the data for the email
    $output .= ob_get_contents();  //Storing the output buffer content to $output
    ob_end_clean();  //While testing, you probably want to comment the next row out
    mb_send_mail($email, "Your Azure AD Oauth2 script faced an error!", $output, "X-Priority: 1\nContent-Transfer-Encoding: 8bit\nX-Mailer: PHP/" . phpversion());
    exit;
    }

    /**
     * Login user and create session variables
     *
     * @return void
     */
        
    public function azure_login() {

        // $tokenClient = new Client();
        $client_id = getenv('CLIENT_ID');
        $client_secret = getenv('CLIENT_SECRET'); // This is a custom variable I inserted in .env
        $ad_tenant = getenv('TENANT_ID');
        $graphUserScopes = getenv('GRAPH_USER_SCOPES');
        $redirect_uri = getenv('REDIRECT_URI');
        
        if (isset($_GET["code"])) echo "<pre>";  //This is just for easier and better looking var_dumps for debug purposes
        
        if (!isset($_GET["code"]) and !isset($_GET["error"])) {  //Real authentication part begins
            
            //First stage of the authentication process; This is just a simple redirect (first load of this page)
            $url = "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/authorize?";
            $url .= "state=" . session_id();  //This at least semi-random string is likely good enough as state identifier
            $url .= "&scope=" . $graphUserScopes;  //This scope seems to be enough, but you can try "&scope=profile+openid+email+offline_access+User.Read" if you like
            $url .= "&response_type=code";
            $url .= "&approval_prompt=auto";
            $url .= "&client_id=" . $client_id;
            $url .= "&redirect_uri=" . urlencode($redirect_uri);
            header("Location: " . $url);  //So off you go my dear browser and welcome back for round two after some redirects at Azure end

        } elseif (isset($_GET["error"])) {  //Second load of this page begins, but hopefully we end up to the next elseif section...
            errorhandler(array("Description" => "Error received at the beginning of second stage.", "\$_GET[]" => $_GET, "\$_SESSION[]" => $_SESSION), $error_email);
        } elseif (strcmp(session_id(), $_GET["state"]) == 0) {
             //Checking that the session_id matches to the state for security reasons
            //And now the browser has returned from its various redirects at Azure side and carrying some gifts inside $_GET
            //var_dump($_GET);  //Debug print
            
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
            $json = file_get_contents("https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/token", false, $context);
            if ($json === false) errorhandler(array("Description" => "Error received during Bearer token fetch.", "PHP_Error" => error_get_last(), "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
            $authdata = json_decode($json, true);
            if (isset($authdata["error"])) errorhandler(array("Description" => "Bearer token fetch contained an error.", "\$authdata[]" => $authdata, "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
        
            // echo $authdata["access_token"];  //Debug print
            
            //Fetching the basic user information that is likely needed by your application
            $options = array(
                "http" => array(  //Use "http" even if you send the request with https
                "method" => "GET",
                "header" => "Accept: application/json\r\n" .
                "Authorization: Bearer " . $authdata["access_token"] . "\r\n"
                )
            );
            $context = stream_context_create($options);
            $json = file_get_contents("https://graph.microsoft.com/v1.0/me", false, $context);
            if ($json === false) errorhandler(array("Description" => "Error received during user data fetch.", "PHP_Error" => error_get_last(), "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
            $userdata = json_decode($json, true);  //This should now contain your logged on user information
            if (isset($userdata["error"])) errorhandler(array("Description" => "User data fetch contained an error.", "\$userdata[]" => $userdata, "\$authdata[]" => $authdata, "\$_GET[]" => $_GET, "HTTP_msg" => $options), $error_email);
            
            $_SESSION['username'] = $userdata["displayName"];
            $user_email = $userdata["mail"];

            $_SESSION['logged_in'] = (bool)true;
            $_SESSION['azure_identification'] = (bool)true;

            
            $ci_user = $this->user_model->where('azure_mail', $user_email)->first(); // This is like calling CodeIgniter\Database\BaseConnection::query()
                
            // if email is registered in DB
            if (isset($ci_user['azure_mail'])) {
                // give default azure access to user
                $_SESSION['user_access'] = (int)$this->user_model->get_access_level($ci_user);
            } else {
                $_SESSION['user_access'] = config("\User\Config\UserConfig")->azure_default_access_lvl;
            }
            // Send the user to the redirection URL
            return redirect()->to($_SESSION['after_login_redirect']);

        } else {
            //If we end up here, something has obviously gone wrong... Likely a hacking attempt since sent and returned state aren't matching and no $_GET["error"] received.
            dd("Hey, please don't try to hack us!\n\n");
            echo "PHP Session ID used as state: " . session_id() . "\n";  //And for production version you likely don't want to show these for the potential hacker
            var_dump($_GET);  //But this being a test script having the var_dumps might be useful
            errorhandler(array("Description" => "Likely a hacking attempt, due state mismatch.", "\$_GET[]" => $_GET, "\$_SESSION[]" => $_SESSION), $error_email);
        }
    }

    public function login(){   
        
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

            // Check if the form has been submitted, else just display the form
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
                
                        $_SESSION['user_id'] = (int)$user->id;
                        $_SESSION['username'] = (string)$user->username;
                        $_SESSION['user_access'] = (int)$this->user_model->get_access_level($user);
                        $_SESSION['logged_in'] = (bool)true;

                        // Send the user to the redirection URL
                        return redirect()->to($_SESSION['after_login_redirect']);

                    } else {
                        // Login failed
                        $this->session->setFlashdata('message-danger', lang('user_lang.msg_err_invalid_password'));
                    }
                    $this->session->setFlashdata('message-danger', lang('user_lang.msg_err_invalid_password'));
                }
            } else if (!is_null($this->request->getPost('btn_login_microsoft'))) {
                $this->azure_login();// This'll redirect to redirect uri if successful
                exit();
            }
            //Display login page
            $output = array('title' => lang('user_lang.title_page_login'));
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

            // Get user from DB, redirect if user doesn't exist
            $user = $this->user_model->withDeleted()->find($_SESSION['user_id']);
            if (is_null($user)) return redirect()->to('/user/auth/login');

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
            $this->display_view('\User\auth\change_password', $output);

        } else {
            // Access is not allowed
            return redirect()->to('/user/auth/login');
        }
    }
}