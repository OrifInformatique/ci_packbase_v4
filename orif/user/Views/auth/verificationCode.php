<?php
/**
 * login view
 *
 * @author      Orif (ViDi,MoDa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-sm-10 well">
            <legend><?= lang('user_lang.title_email_validation'); ?></legend>
            <?php
            $session=\Config\Services::session();
            $validation=\Config\Services::validation();
                $attributes = array("class" => "form-horizontal",
                                    "id" => "verificationCode",
                                    "name" => "verificationCode");
                echo form_open("user/auth/emailVerification", $attributes);
            ?>
            <fieldset>
                <!-- Status messages -->
                <?php if(!is_null($session->getFlashdata('message-danger'))){ ?>
                    <div class="alert alert-danger text-center"><?= $session->getFlashdata('message-danger'); ?></div>
                <?php } ?>
                <div class="bg-info"style="color:white">
                    <p><?= lang('user_lang.user_first_azure_connexion'); ?></p>
                </div>
                <div class="form-group">

                    <input class="form-control" id="verification_code" name="verification_code" placeholder="<?= lang('user_lang.field_verification_code'); ?>" type="text" value="<?= set_value('username'); ?>" />
                    
                </div>             
                <div class="form-group">
                    <div class="col-sm-12 text-right">
                        <a id="btn_cancel" class="btn btn-secondary" href="<?= base_url(); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
                        <input id="btn_login" name="btn_login" type="submit" class="btn btn-primary" value="<?= lang('user_lang.btn_next'); ?>" />
                    </div>
                </div>
            </fieldset>
            <?= form_close(); ?>
        </div>
    </div>
</div>
