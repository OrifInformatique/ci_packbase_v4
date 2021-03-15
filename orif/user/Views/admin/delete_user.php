<?php
?>
<div id="page-content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php if($_SESSION['user_id'] != $user['id']){ ?>
                    <div>
                        <h1><?= lang('MY_user_lang.user').' "'.$user['username'].'"' ?></h1>
                        <h4><?= lang('MY_user_lang.what_to_do')?></h4>
                        <div class = "alert alert-info" ><?= lang('MY_user_lang.user_delete_explanation')?></div>
                        <?php if ($user['archive']) { ?>
                            <div class = "alert alert-warning" ><?= lang('MY_user_lang.user_allready_disabled')?></div>
                        <?php } ?>
                    </div>
                    <div class="text-right">
                        <a href="<?= base_url('user/admin/list_user'); ?>" class="btn btn-default">
                            <?= lang('MY_user_lang.btn_cancel'); ?>
                        </a>
                        <?php if (!$user['archive']) { ?>
                        <a href="<?= base_url(uri_string().'/1'); ?>" class="btn btn-primary">
                            <?= lang('MY_user_lang.btn_disable'); ?>
                        </a>
                        <?php } ?>
                        <a href="<?= base_url(uri_string().'/2'); ?>" class="btn btn-danger">
                            <?= lang('MY_user_lang.btn_hard_delete'); ?>
                        </a>
                    </div>
                <?php } else { ?>
                    <div>
                        <h1><?= lang('MY_user_lang.user').' "'.$user['username'].'"' ?></h1>
                        <div class = "alert alert-danger" ><?= lang('MY_user_lang.user_delete_himself')?></div>
                    </div>
                    <div class="text-right">
                        <a href="<?= base_url('user/admin/list_user'); ?>" class="btn btn-secondary">
                            <?= lang('MY_user_lang.btn_back'); ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
