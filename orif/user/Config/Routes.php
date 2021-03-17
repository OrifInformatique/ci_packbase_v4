<?php
/**
 * Routes for user module
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
$routes->group("user/auth",function($routes){
    $routes->add("login","\User\Controllers\Auth::login");
    $routes->add("logout","\User\Controllers\Auth::logout");
    $routes->add("change_password","\User\Controllers\Auth::change_password");
});
$routes->group("user/admin",function($routes){
   $routes->add("list_user","\User\Controllers\Admin::list_user");
   $routes->add("list_user/(:num)","\User\Controllers\Admin::list_user/$1");
   $routes->add("save_user/(:num)","\User\Controllers\Admin::save_user/$1");
   $routes->add("save_user","\User\Controllers\Admin::save_user");
   $routes->add("delete_user/(:num)","\User\Controllers\Admin::delete_user/$1");
   $routes->add("delete_user/(:num)/(:num)","\User\Controllers\Admin::delete_user/$1/$2");
   $routes->add("reactivate_user/(:num)","\User\Controllers\Admin::reactivate_user/$1");
   $routes->add("password_change_user/(:num)","\User\Controllers\Admin::password_change_user/$1");

});
?>