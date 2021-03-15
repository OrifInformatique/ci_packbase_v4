<?php
$routes->group("user/auth",function($routes){
    $routes->add("login","\User\Controllers\Auth::login");
    $routes->add("logout","\User\Controllers\Auth::logout");
    $routes->add("change_password","\User\Controllers\Auth::change_password");
});
$routes->group("user/admin",function($routes){
   $routes->add("list_user","\User\Controllers\Admin::list_user",['filter'=>"logfilter:{config('\User\Config\User_config')->access_lvl_admin}"]);
   $routes->add("list_user/(:num)","\User\Controllers\Admin::list_user/$1",['filter'=>"logfilter:{config('\User\Config\User_config')->access_lvl_admin}"]);
   $routes->add("save_user/(:num)","\User\Controllers\Admin::save_user/$1",['filter'=>"logfilter:{config('\User\Config\User_config')->access_lvl_admin}"]);
   $routes->add("save_user","\User\Controllers\Admin::save_user",['filter'=>"logfilter:{config('\User\Config\User_config')->access_lvl_admin}"]);

});
?>