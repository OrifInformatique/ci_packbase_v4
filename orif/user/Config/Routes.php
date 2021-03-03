<?php
$routes->get("admin","\User\Controllers\Admin");
$routes->group("user/auth",function($routes){
    $routes->add("login","\User\Controllers\Auth::login");
    $routes->add("logout","\User\Controllers\Auth::logout");
    $routes->add("change_password","\User\Controllers\Auth::change_password");

})
?>