<?php
/**
 * 403error view
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
http_response_code(403);
?>
<style>
    #message{
        width: 50vw!important;
        margin-left: 25%;
        height: 200px;
        border-radius: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: x-large;
        margin-top: 20px;
    }
</style>
<div style="width: 100%;background-color: #106DB4;color: white;margin: 0;padding: 0;height: 15vh;display: flex;justify-content: center;align-items: center"><h1 style="margin: 0;padding: 0"><?= lang('error_language.403_error')?></h1></center></div>
<div id="corper">
    <p id="message" class="alert-danger text-danger" style="display:flex;justify-content:center;width: 100%" onload="animate"><?= lang('error_language.msg_err_access_forbidden')?></p>
    <a href="<?=$_SESSION['_ci_previous_url']?>" style="padding-left: 27%"><?=lang('My_user_lang.btn_back');?></a>
</div>
