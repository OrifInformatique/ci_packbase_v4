<?php
/**
 * User Administration
 *
 * @author      Orif (ViDi)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
namespace User\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Admin extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Controller's accessibility is restricted for administrators only
        $this->access_level = config('User\Config\UserConfig')->access_lvl_admin;
        
        // Do Not Edit This Line
        parent::initController($request,$response,$logger);
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
        $oldName = NULL;
        $oldUsertype = NULL;
        if (count($_POST) > 0) {
            $user_id = $this->input->post('id');
            $oldName = $this->input->post('user_name');
            if($_SESSION['user_id'] != $user_id) {
                $oldUsertype = $this->input->post('user_usertype');
            }

            $this->form_validation->set_rules(
                'id', 'id',
                'callback_cb_not_null_user',
                ['cb_not_null_user' => $this->lang->line('msg_err_user_not_exist')]
            );
            $this->form_validation->set_rules('user_name', 'lang:field_user_name',
                [
                    'required', 'trim',
                    'min_length['.$this->config->item('username_min_length').']',
                    'max_length['.$this->config->item('username_max_length').']',
                    "callback_cb_unique_user[{$user_id}]"
                ],
                ['cb_unique_user' => $this->lang->line('msg_err_user_not_unique')]
            );
            $this->form_validation->set_rules('user_usertype', 'lang:field_user_usertype',
                ['required', 'callback_cb_not_null_user_type'],
                ['cb_not_null_user_type' => $this->lang->line('msg_err_user_type_not_exist')]
            );
            if ($this->input->post('user_email')) {
                $this->form_validation->set_rules('user_email', 'lang:field_email', [
                    'required', 'valid_email',
                    'max_length['.$this->config->item('email_max_length').']'
                ]);
            }

            if ($user_id == 0) {
                $this->form_validation->set_rules('user_password', lang('field_password'), [
                    'required', 'trim',
                    'min_length['.$this->config->item('password_min_length').']',
                    'max_length['.$this->config->item('password_max_length').']'
                ]);
                $this->form_validation->set_rules('user_password_again', $this->lang->line('field_password_confirm'), [
                    'required', 'trim', 'matches[user_password]',
                    'min_length['.$this->config->item('password_min_length').']',
                    'max_length['.$this->config->item('password_max_length').']'
                ]);
            }

            if ($this->form_validation->run()) {
                $user = array(
                    'fk_user_type' => $this->input->post('user_usertype'),
                    'username' => $this->input->post('user_name'),
                    'email' => $this->input->post('user_email') ?: NULL
                );
                if ($user_id > 0) {
                    $this->user_model->update($user_id, $user);
                } else {
                    $password = $this->input->post('user_password');
                    $user['password'] = password_hash($password, $this->config->item('password_hash_algorithm'));
                    $this->user_model->insert($user);
                }
                redirect('user/admin/list_user');
            }
        }

        $output = array(
            'title' => $this->lang->line('title_user_'.((bool)$user_id ? 'update' : 'new')),
            'user' => $this->user_model->with_deleted()->get($user_id),
            'user_types' => $this->user_type_model->dropdown('name'),
            'user_name' => $oldName,
            'user_usertype' => $oldUsertype
        );

        $this->display_view('user/admin/save_user', $output);
    }

}