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
	}

	/**
    * Display one or multiple view(s), adding header, footer and
	* any other view part wich is common to all pages.
    *
    * @param  view_parts : single view or array of view parts to display
    * @param  data : data array to send to the view parts
	* @return complete_view : The complete view
    */
    public function display_view($view_parts = NULL, $data = NULL)
    {
        // If not defined in $data, set page title to empty string
        if (!isset($data['title'])) {
            $data['title'] = '';
        }
		
		// Initialize an empty view
		$complete_view = "";

        // Add common headers
        $complete_view .= view('Common\header', $data);
	    $complete_view .= view('Common\login_bar');

        if (is_array($view_parts)) {
            // Add multiple view parts
            foreach ($view_parts as $view_part) {
                $complete_view .= view($view_part, $data);
            }
        }
        elseif (is_string($view_parts)) {
            // Add unique view part
            $complete_view .= view($view_parts, $data);
        }

        // Add common footer
        $complete_view .= view('Common\footer');
		
		// Return complete view content
		return $complete_view;
    }
}
