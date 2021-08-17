<?php
/**
 * Config for common module
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */

namespace Config;


use User\Controllers\Admin;

class AdminPanelConfig extends \CodeIgniter\Config\BaseConfig
{
    /** Update this array to customize admin pannel tabs for your needs 
     *  Syntax : ['label'=>'tab label','title'=>'web page title','pageLink'=>'tab link']
    */
    public $views=[
        ['label'=>'user_lang.title_user_list','title'=>'user_lang.title_user_list','pageLink'=>'user/admin/list_user'],
    ];
}