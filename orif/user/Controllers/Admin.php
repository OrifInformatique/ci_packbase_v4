<?php
/*
 * Admin controller
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
use User\Models\User_type_model;
use CodeIgniter\HTTP\Response;

class Admin extends BaseController
{
    /**
     * Constructor
     */
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger): void
    {
        // Set Access level before calling parent constructor
        // Accessibility reserved to admin users
        $this->access_level=config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request,$response,$logger);

        // Load required helpers
        helper('form');

        // Load required services
        $this->validation = \Config\Services::validation();

        // Load required models
        $this->user_model = new User_model();
        $this->user_type_model = new User_type_model();
        //get db instance
        $this->db = \CodeIgniter\Database\Config::connect();
    }

    /**
     * Displays the list of users
     *
     * @param boolean $with_deleted : Display archived users or not
     * @return void
     */
    public function list_user(?bool $with_deleted = FALSE): string
    {
        if ($with_deleted) {
            $users = $this->user_model->orderBy('username')->withDeleted()
                   ->findAll();
        } else {
            $users = $this->user_model->orderBy('username')->findAll();
        }

        //usertiarray is an array contained all usertype name and id
        $usertiarray=$this->db->table('user_type')->select(['id','name'],)->get()->getResultArray();
        $usertypes=[];
        foreach ($usertiarray as $row){
            $usertypes[$row['id']]=$row['name'];
        }
        $output = array(
            'title' => lang('user_lang.title_administration'),
            'users' => $users,
            'user_types' => $usertypes,
            'with_deleted' => $with_deleted
        );
        return $this->display_view('\User\admin\list_user', $output);
    }

    /**
     * Adds or modify a user
     *
     * @param integer $user_id = The id of the user to modify, leave blank to create a new one
     * @return void
     */
    public function save_user(?int $user_id = 0): string|Response
    {
        //store the user name and user type to display them again in the form
        $oldName = NULL;
        $oldUsertype = NULL;
        //added user in current scope to manage its datas
        $user=null;
        if (count($_POST) > 0) {
            $user_id = $this->request->getPost('id');
            $oldName = $this->request->getPost('user_name');
            if($_SESSION['user_id'] != $user_id) {
                $oldUsertype = $this->request->getPost('user_usertype');
            }
            $user = array(
                'id'    => $user_id,
                'fk_user_type' => intval($this->request->getPost('user_usertype')),
                'username' => $this->request->getPost('user_name'),
                'email' => $this->request->getPost('user_email') ?: NULL
            );
            if($this->request->getPost('user_password_again') !== null) {
                $user['password_confirm'] = $this->request->getPost('user_password_again');
            }
            if ($user_id > 0) {
                $this->user_model->update($user_id, $user);
            }
            else {
                $user['password'] = $this->request->getPost('user_password');
                $user['password_confirm'] = $this->request->getPost('user_password_again');

                $this->user_model->insert($user);
            }
            //In the case of errors
            if ($this->user_model->errors()==null){
                return redirect()->to('/user/admin/list_user');
            }
        }

        //usertiarray is an array that containes all usertype name and id
        $usertiarray=$this->db->table('user_type')->select(['id','name'],)->get()->getResultArray();
        $usertypes=[];

        foreach ($usertiarray as $row){
            $usertypes[$row['id']]=$row['name'];
        }

        $output = array(
            'title'                 => lang('user_lang.title_user_'.((bool)$user_id ? 'update' : 'new')),
            'user'                  => $this->user_model->withDeleted()->find($user_id),
            'user_types'            => $usertypes,
            'user_name'             => $oldName,
            'user_usertype'         => $oldUsertype,
            'email'                 => $user['email']??null,
            'errors'                => $this->user_model->errors()==null?[]:$this->user_model->errors(),
            'force_password_change' => ''
        );

        return $this->display_view('\User\admin\form_user', $output);
    }

    /**
     * Delete or deactivate a user depending on $action
     *
     * @param integer $user_id ID of the user to affect
     * @param integer $action Action to apply on the user
     *      - 0 for displaying the confirmation
     *      - 1 for deactivating (soft delete)
     *      - 2 for deleting (hard delete)
     *
     * @return void
     *
     */
    public function delete_user(int $user_id, ?int $action = 0): string|Response
    {
        $user = $this->user_model->withDeleted()->find($user_id);

        if(is_null($user))
            return redirect()->to('/user/admin/list_user');

        switch($action)
        {
            // Display confirmation
            case 0:
                $output = array
                (
                    'entry' =>
                    [
                        'type' => lang('user_lang.user'),
                        'name' => $user['username'],
                    ],
                    'cancel_btn_url' => base_url('/user/admin/list_user')
                );

                if($_SESSION['user_id'] == $user_id)
                {
                    // Prevents altering the user himself
                    $output['entry']['message'] =
                    [
                        'type' => 'danger',
                        'text' => lang('user_lang.user_delete_himself')
                    ];
                }

                else
                {
                    $output['entry']['message'] =
                    [
                        'type' => 'info',
                        'text' => lang('user_lang.user_delete_explanation')
                    ];

                    $output['primary_action'] =
                    [
                        'name' => lang('common_lang.btn_delete'),
                        'url' => base_url(uri_string().'/2')
                    ];

                    if($user['archive'])
                    {
                        $output['secondary_action'] =
                        [
                            'name' => lang('common_lang.btn_reactivate'),
                            'url' => base_url('/user/admin/reactivate_user/'.$user_id)
                        ];
                    }

                    else
                    {
                        $output['secondary_action'] =
                        [
                            'name' => lang('common_lang.btn_disable'),
                            'url' => base_url(uri_string().'/1')
                        ];
                    }
                }

                return $this->display_view('\Common/manage_entry', $output);

            // Deactivate (soft delete) user
            case 1:
                if($_SESSION['user_id'] != $user['id'])
                    $this->user_model->delete($user_id, FALSE);
                break;

            // Delete user
            case 2:
                if ($_SESSION['user_id'] != $user['id'])
                    $this->user_model->delete($user_id, TRUE);
                break;
        }

        return redirect()->to('/user/admin/list_user');
    }

    /**
     * Reactivate a disabled user.
     *
     * @param integer $user_id = ID of the user to affect
     * @return void
     */
    public function reactivate_user(int $user_id): Response
    {
        $user = $this->user_model->withDeleted()->find($user_id);
        if (is_null($user)) {
            return redirect()->to('/user/admin/list_user');
        } else {
            $this->user_model->withDeleted()->update($user_id,['archive'=>null]);
            return redirect()->to('/user/admin/save_user/'.$user_id);
        }
    }

    /**
     * Displays a form to change a user's password
     *
     * @param integer $user_id = ID of the user to update
     * @return void
     */
    public function password_change_user(int $user_id): Response|string
    {
        // Get user from DB, redirect if user doesn't exist
        $user = $this->user_model->withDeleted()->find($user_id);
        if (is_null($user)) return redirect()->to('/user/admin/list_user');

        if ($this->request->getPost('password_new') !== null) {
            // Save new password
            $user['password'] = $this->request->getPost('password_new');
            $user['password_confirm'] = $this->request->getPost('password_confirm');

            $force_password_change = $this->request->getPost('force_password_change');
            // if force_password_change not checked in the view, the value is null.
            if ($force_password_change){
                $user['reset_password'] = 1;
            } else {
                $user['reset_password'] = 0;
            }

            $this->user_model->update($user_id, $user);

            // If no error happened, redirect
            if ($this->user_model->errors()==null) {
                return redirect()->to('/user/admin/list_user');
            }
        }

        // get reset_password from DB
        if ($user['reset_password'] = 1){
            $force_password_change = 'checked';
        } else {
            $force_password_change = '';
        }
        // Display password change form
        $output = array(
            'user' => $user,
            'title' => lang('user_lang.title_user_password_reset'),
            'errors' => $this->user_model->errors()==null?[]:$this->user_model->errors(),
            'force_password_change' => $force_password_change
        );
        return $this->display_view('\User\admin\password_change_user', $output);
    }
}
