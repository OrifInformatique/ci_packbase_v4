<?php

namespace User\Controllers;
use App\Controllers\BaseController;

class Admin extends BaseController
{
    public function index(){
        $this->display_view("\Welcome\welcome_message",['title'=>'admin']);
    }
    public function tester(){
        echo "tester";
    }

}