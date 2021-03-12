<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;
use CodeIgniter\Session\Session;
use CodeIgniter\Validation\Validation;
use Psr\Log\LoggerInterface;
use User\Controllers\Auth;
use User\Models\User_model;
use User\Models\User_type_model;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];
    /** add access level BaseController like in v3*/
    protected $access_level = "*";
    protected Validation $validation;
    protected Session $session;
    protected Model $user_model;
    protected Model $user_type_model;
    /**
     * Constructor.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param LoggerInterface   $logger
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {



        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();
        $this->validation=\Config\Services::validation();
        $this->user_model=new User_model();
        $this->user_type_model=new User_type_model();

    }

    /**
     * Display one or multiple view(s), adding header, footer and
     * any other view part wich is common to all pages.
     *
     * @param  $view_parts : single view or array of view parts to display
     *         $data : data array to send to the view
     */
    public function display_view($view_parts, $data = NULL)
    {
        // If not defined in $data, set page title to empty string
        if (!isset($data['title'])) {
            $data['title'] = '';
        }

        // Display common headers
        echo view('Common\header', $data);
        echo view('Common\login_bar');

        if (is_array($view_parts)) {
            // Display multiple view parts
            foreach ($view_parts as $view_part) {
                echo view($view_part, $data);
            }
        }
        elseif (is_string($view_parts)) {
            // Display unique view part
            echo view($view_parts, $data);
        }

        // Display common footer
        echo view('Common\footer');
    }

}
