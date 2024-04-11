<?php
/**
 * Routes for user module
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
use User\Controllers\Auth;
use User\Controllers\Profile;
$routes->add('user/auth/(:any)','\User\Controllers\Auth::$1');
$routes->add('user/admin/(:any)','\User\Controllers\Admin::$1');
$routes->add('user/auth/azure_login','Auth::azure_login',
    ['as' => 'azure_login']);
$routes->add('user/profile/update','\User\Controllers\Profile::$1');
$routes->add('user/profile/(:any)','\User\Controllers\Profile::$1');
?>
