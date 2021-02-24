<?php

namespace User\Controllers;
use App\Controllers\BaseController;

class Admin extends BaseController
{
    protected $access_level="@";
    public function index(){
        $this->display_view("\Welcome\welcome_message",['title'=>'admin']);
    }

}