<?php
/**
 * change_password view
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */

?>
<div class="container">
  <div class="row">
    <div class="col-md-10 well">
      <?php
      $validation=\Config\Services::validation();
      $attributes = array("class" => "form-horizontal",
      "id" => "update_form",
      "name" => "update_form");
      echo form_open("user/profile/update_form", $attributes);
      ?>
      <fieldset>
        <legend><?= $title; ?></legend>

        <!-- ERROR MESSAGES -->
        <?php if(isset($errors)) {
          foreach ($errors as $error) { ?>
            <div class="alert alert-danger" role="alert">
              <?= $error ?>
            </div>
          <?php } ?>
        <?php } ?>

        <div class="alert alert-info">
          <?= lang('user_lang.page_username_choice'); ?>
        </div>

        <!-- Possibility to change username -->
        <div class="form-group">
          <div class="row colbox">
            <div class="col-md-4">
              <label for="username" class="control-label">
                <?= lang('user_lang.field_username'); ?>
              </label>
            </div>
            <div class="col-md-8">
              <input
                id="username"
                name="username"
                type="text"
                class="form-control"
                placeholder="<?= lang('user_lang.field_username'); ?>"
                value="<?= $username, set_value('new_username'); ?>"
              >
            </div>
          </div>
          </br>

        <!-- Display user info -->
          <div class="row colbox">
            <div class="col-md-4">
              <label for="mail" class="control-label">
                <?= lang('user_lang.field_email'); ?>
              </label>
            </div>
            <div class="col-md-8">
              <input
                id="email"
                name="email"
                type="email"
                class="form-control"
                placeholder="<?= lang('user_lang.field_email'); ?>"
                value="<?= $email; ?>"
                readonly
              >
            </div>
          </div>
          </br>
          <div class="row colbox">
            <div class="col-md-4">
              <label for="mail" class="control-label">
                <?= lang('user_lang.field_microsoft_email'); ?>
              </label>
            </div>
            <div class="col-md-8">
              <input
                id="ms_email"
                name="ms_email"
                type="mail"
                class="form-control"
                placeholder="<?= lang('user_lang.field_microsoft_email'); ?>"
                value="<?= $azure_mail; ?>"
                readonly
              >
            </div>
          </div>
          </br>

          <!-- PASSWORD -->    
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <?= form_label(lang('user_lang.field_new_password'), 'password_new', ['class' => 'form-label']); ?>
                <?= form_password('password_new', '', [
                'class' => 'form-control', 'id' => 'password_new',
                'maxlength' => config('\User\Config\UserConfig')->password_max_length
                ]); ?>
              </div>
            </div>
            </br>
            <div class="col-sm-6">
              <div class="form-group">
                <?= form_label(lang('user_lang.field_password_confirm'), 'password_confirm', ['class' => 'form-label']); ?>
                <?= form_password('password_confirm', '', [
                'class' => 'form-control', 'id' => 'password_confirm',
                'maxlength' => config('\User\Config\UserConfig')->password_max_length
                ]); ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Submit button-->
        <div class="form-group">
          <div class="col-md-12 text-right">
            <a id="btn_cancel" class="btn btn-secondary" href="<?= base_url(); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
            <input id="btn_update_form" name="btn_update_form" type="submit" class="btn btn-primary" value="<?= lang('common_lang.btn_save'); ?>" />
          </div>
        </div>

      </fieldset>
      <?= form_close(); ?>
    </div>
  </div>
</div>
