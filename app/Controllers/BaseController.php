<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

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
	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
	    if (!$this->check_permission()){
            //throw new \Exception();
        }


		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();
	}
    /**
     * Check if user access level matches the required access level.
     * Required level can be the controller's default level or a custom
     * specified level.
     *
     * @param  $required_level : minimum level required to get permission
     * @return bool : true if user level is equal or higher than required level,
     *                false else
     */
    protected function check_permission($required_level = NULL)
    {
        if (!isset($_SESSION['logged_in'])) {
            // Tests can accidentally delete $_SESSION,
            // this makes sure it always exists.
            $_SESSION['logged_in'] = FALSE;
        }
        if (is_null($required_level)) {
            $required_level = $this->access_level;
        }

        if ($required_level == "*") {
            // page is accessible for all users
            return true;
        }
        else {
            // check if user is logged in
            // if not, redirect to login page
            if ($_SESSION['logged_in'] != true) {
                redirect("user/auth/login");
            }
            // check if page is accessible for all logged in users
            elseif ($required_level == "@") {
                return true;
            }
            // check access level
            elseif ($required_level <= $_SESSION['user_access']) {
                return true;
            }
            // no permission
            else {
                return false;
            }
        }
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
