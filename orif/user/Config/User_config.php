<?php


class User_config extends \CodeIgniter\Config\BaseConfig
{
/* Access levels */
public $access_lvl_guest            =   1;
public $access_lvl_registered       =   2;
public $access_lvl_admin            =   4;
/* Validation rules */
public $username_min_length         =   3;
public $username_max_length         =   45;
public $password_min_length         =   6;
public $password_max_length         =   72;
public $email_max_length            =   100;
/* Other rules */
public $password_hash_algorithm     =   PASSWORD_BCRYPT;
}