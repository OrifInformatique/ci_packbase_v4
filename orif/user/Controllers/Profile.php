<?php
/**
 * User profile
 *
 * @author      Orif (ViDi,MoDa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */

// The goal:
// redirect to user account settings' view (User/admin/form_user) with all info prefilled after
// User/auth/reset_session 
namespace User\Controllers;
use App\Controllers\BaseController;
use User\Models\User_model;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\HTTP\RedirectResponse;

class Profile extends BaseController {


    /**
     * Constructor
     */
    
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger): void
    {
        // Set Access level before calling parent constructor
        // Accessibility for all connected users
        $this->access_level = "@";
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
     * Displays a form to let user change his password
     *
     * @return void
     */
    public function change_password(): Response|string|RedirectResponse {

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
                    $user['reset_password'] = 0; // false
                    $_SESSION['reset_password'] = null;
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
        $_SESSION['reset_password'] = $user['reset_password'];
        $output['title'] = lang('user_lang.page_my_password_change');
        return $this->display_view('\User\auth\change_password', $output);

    }
}
?>
