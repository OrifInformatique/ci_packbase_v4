<?php

namespace User\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use User\Models\User_model;
use User\Models\User_type_model;

class Admin extends BaseController
{
    protected $access_level;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level=config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request,$response,$logger);
        helper('form');
        $this->user_model=new User_model();
        $this->user_type_model=new User_type_model();
        $this->validation = \Config\Services::validation();

    }

    public function index(){
        $this->display_view("\Welcome\welcome_message",['title'=>'admin']);
    }
    /**
     * Displays the list of users
     *
     * @param boolean $with_deleted = Display archived users or not
     * @return void
     */
    public function list_user($with_deleted = FALSE)
    {
        if ($with_deleted) {
            $users = $this->user_model->withDeleted()->findAll();
        } else {
            $users = $this->user_model->withDeleted(false)->findAll();
        }

        $output = array(
            'users' => $users,
            'user_types' => $this->user_type_model->findColumn('name'),
            'with_deleted' => $with_deleted
        );
        $this->display_view('\User\admin\list_user', $output);
    }
    /**
     * Adds or modify a user
     *
     * @param integer $user_id = The id of the user to modify, leave blank to create a new one
     * @return void
     */
    public function save_user($user_id = 0)
    {
        $this->validation->reset();
        $oldName = NULL;
        $oldUsertype = NULL;
        if (count($_POST) > 0) {
            $user_id = $this->request->getPost('id');
            $oldName = $this->request->getPost('user_name');
            if($_SESSION['user_id'] != $user_id) {
                $oldUsertype = $this->request->getPost('user_usertype');
            }

            /*$this->validation->set_rules(
                'id', 'id',
                'callback_cb_not_null_user',
                ['cb_not_null_user' => $this->lang->line('msg_err_user_not_exist')]
            );
            */
            $validationRules=['id'        =>['label'=>'Id','rules'=>'cb_not_null_user'],
                              'user_name' =>['label'=>lang('MY_user_lang.field_username'),'rules'=>'required|trim|'.
                              'min_length['.config('\User\Config\UserConfig')->username_min_length.']|'.
                              'max_length['.config('\User\Config\UserConfig')->username_max_length.']|'.
                              'cb_unique_user['.$user_id.']'],
                              'user_usertype'=>['label'=>lang('My_user_lang.field_user_usertype'),'rules'=>'required|cb_not_null_user_type']];
            $validationErrors=['id'=>['cb_not_null_user' => lang('My_user_lang.msg_err_user_not_exist')],
                'user_name'=>['cb_unique_user' => lang('My_user_lang.msg_err_user_not_unique')],
                'user_usertype'=>['cb_not_null_user_type' => lang('My_user_lang.msg_err_user_type_not_exist')]];
            if ($this->request->getPost('user_email')) {
            $validationRules['user_email']=['label'=>lang('MY_user_lang.field_email'),'rules'=>'required|valid_email|max_length['.config("\User\Config\UserConfig")->email_max_length.']'];
            }
            if ($user_id==0){
            $validationRules['user_password']=['label'=>lang('MY_user_lang.field_password'),'rules'=>'required|trim|'.
                'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'.
                'max_length['.config("\User\Config\UserConfig")->password_max_length.']'];
            $validationRules['user_password_again']=['label'=>lang('MY_user_lang.field_password_confirm'),'rules'=>'required|trim|matches[user_password]|'.
                'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'.
                'max_length['.config("\User\Config\UserConfig")->password_max_length.']'];
            }
            $this->validation->setRules($validationRules,$validationErrors);
            if ($this->validation->withRequest($this->request)->run()) {
                $user = array(
                    'fk_user_type' => intval($this->request->getPost('user_usertype')),
                    'username' => $this->request->getPost('user_name'),
                    'email' => $this->request->getPost('user_email') ?: NULL
                );
                if ($user_id > 0) {
                    $this->user_model->update($user_id, $user);
                } else {
                    $password = $this->request->getPost('user_password');
                    $user['password'] = password_hash($password, config('\User\Config\UserConfig')->password_hash_algorithm);
                    $this->user_model->insert($user);
                }
                return redirect()->to('/user/admin/list_user');
            }
        }
        //to make admin 1 saved 2 invited 3
        $usertypes=$this->user_type_model->findColumn('name');
        array_unshift($usertypes,'');
        unset($usertypes[0]);
        $output = array(
            'title' => lang('My_user_lang.title_user_'.((bool)$user_id ? 'update' : 'new')),
            'user' => $this->user_model->withDeleted()->find($user_id),
            'user_types' => $usertypes,
            'user_name' => $oldName,
            'user_usertype' => $oldUsertype
        );

        $this->display_view('\User\admin\save_user', $output);
    }
    /**
     * Deletes or deactivate a user depending on $action
     *
     * @param integer $user_id = ID of the user to affect
     * @param integer $action = Action to apply on the user:
     *  - 0 for displaying the confirmation
     *  - 1 for deactivating (soft delete)
     *  - 2 for deleting (hard delete)
     * @return void
     */
    public function delete_user($user_id, $action = 0)
    {
        $user = $this->user_model->withDeleted()->find($user_id);
        if (is_null($user)) {
            return redirect()->to('/user/admin/list_user');
        }

        switch($action) {
            case 0: // Display confirmation
                $output = array(
                    'user' => $user,
                    'title' => lang('title_user_delete')
                );
                $this->display_view('\User\admin\delete_user', $output);
                break;
            case 1: // Deactivate (soft delete) user
                if ($_SESSION['user_id'] != $user['id']) {
                    $this->user_model->delete($user_id, FALSE);
                }
                return redirect()->to('/user/admin/list_user');
            case 2: // Delete user
                if ($_SESSION['user_id'] != $user['id']) {
                    $this->user_model->delete($user_id, TRUE);
                }
                return redirect()->to('/user/admin/list_user');
            default: // Do nothing
                return redirect()->to('/user/admin/list_user');
        }
    }
    /**
     * Reactivate a disabled user.
     *
     * @param integer $user_id = ID of the user to affect
     * @return void
     */
    public function reactivate_user($user_id)
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
    public function password_change_user($user_id)
    {
        if (count($_POST) > 0) {
            $this->validation->setRules([
                'id'=>['label'=>'id',
                    'rules'=>'cb_not_null_user'
                ],
                'user_password_new'=>['label'=>lang('MY_user_lang.field_new_password'),
                    'rules'=>'required|trim|'.
                'min_length['.config('\User\Config\UserConfig')->password_min_length.']|'.
                'max_length['.config('\User\Config\UserConfig')->password_max_length.']'
                ],
                'user_password_again'=>['label'=>lang('MY_user_lang.field_password_confirm'),
                    'rules'=>'required|trim|matches[user_password_new]|'.
                        'min_length['.config('\User\Conifg\UserConfig')->password_min_length.']|'.
                        'max_length['.config('\User\Config\UserConfig')->password_max_length.']']
                ],['cb_not_null_user'=>lang('MY_user_lang.msg_err_user_not_exist')]);


            if ($this->validation->withRequest($this->request)->run()) {
                $password = $this->request->getPost('user_password_new');
                $password = password_hash($password, config('\User\Config\UserConfig')->password_hash_algorithm);
                $this->user_model->update($user_id, ['password' => $password]);
                return redirect()->to('/user/admin/list_user');
            }
        }

        $user = $this->user_model->withDeleted()->find($user_id);
        if (is_null($user)) return redirect()->to('/user/admin/list_user');

        $output = array(
            'user' => $user,
            'title' => lang('MY_user_lang.title_user_password_reset')
        );

        $this->display_view('\User\admin\password_change_user', $output);
    }



}