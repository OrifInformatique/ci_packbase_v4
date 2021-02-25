<?php
$routes->get("admin","\User\Controllers\Admin");
$routes->group("user/auth",function($routes){
    $routes->add("login","\User\Controllers\Auth::login");

})
?>