<?php
$routes->group('user',function($routes){
    $routes->add('admin','\User\Controllers\Admin');
});


?>