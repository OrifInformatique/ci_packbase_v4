<?php

namespace User\Filters;
use App\Controllers\BaseController;
use CodeIgniter\Debug\Exceptions;
use CodeIgniter\Security\Exceptions\SecurityException;
use User\Controllers\Auth;

class Logfilter implements \CodeIgniter\Filters\FilterInterface
{
    private $access_level='*';
    private $session;

    /**
     * @inheritDoc
     */
    /** @param null $arguments is the access level of the route
     * if the function return a boolean true that's the access is authorized otherwise false
     * and if the return value is an object then it's a redirection
     * @return boolean or object
     */
    public function before(\CodeIgniter\HTTP\RequestInterface $request, $arguments = null)
    {
        if($this->session==null)
        $this->session=\Config\Services::session();

        $returnvalue=($this->check_permission($arguments==null?null:$arguments[0]));
        if ($returnvalue&&gettype($returnvalue)!="object"){
            //it's ok
        }
        elseif (gettype($returnvalue)=="object"){
            return $returnvalue;
        }
        else{
            //access is forbiddden
        }
    }

    /**
     * @inheritDoc
     */
    public function after(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, $arguments = null)
    {
        // TODO: Implement after() method.
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
                return redirect()->to("/user/auth/login");
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
}
