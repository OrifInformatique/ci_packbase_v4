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
            <legend><?=$title;?></legend>
            <?php if(isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?= $errorMsg; ?>
                    <?php if($attemptsLeft = 2): ?>
                        <div>
                            <?= $msg_attemptsLeft;?>
                        </div>
                    <?php endif ?>
                </div>
            <?php endif ?>

            <?php
                $session=\Config\Services::session();  
                $attributes = array("class" => "form-horizontal",
                                    "id" => "verificationCode",
                                    "name" => "verificationCode");
                echo form_open("user/auth/verify_verification_code", $attributes);
            ?>
            <fieldset>
                <!-- S tatus messages -->
                <?php if(!is_null($session->getFlashdata('message-danger'))){ ?>
                    <div class="alert alert-danger text-center"><?= $session->getFlashdata('message-danger'); ?></div>
                    <?php } ?>
                    <div class="alert alert-info">
                        <?= lang('user_lang.user_validation_code'); ?>
                        <br><br>
                        <?= lang('user_lang.code_expiration_time'); ?> <span id="countdownTimer"></span>
                        
                        <!-- Countdown Timer Display -->
                        
                        <script>
                            function startCountdown(timeRemaining, display) {
                                var timer = timeRemaining, minutes, seconds;
                                var countdownInterval = setInterval(function () {
                                    minutes = parseInt(timer / 60, 10);
                                    seconds = parseInt(timer % 60, 10);

                                    minutes = minutes < 10 ? "0" + minutes : minutes;
                                    seconds = seconds < 10 ? "0" + seconds : seconds;

                                    display.textContent = minutes + ":" + seconds;

                                    if (--timer < 0) {
                                        clearInterval(countdownInterval);
                                        display.textContent = "Temps expiré";
                                    }
                                }, 1000);
                            }

                            window.onload = function () {
                                let timerEnd = <?= $_SESSION['timer_end']?>;
                                console.log("timerEnd " + timerEnd);

                                let currentTime = Math.floor(Date.now() / 1000); // milliseconds to seconds
                                console.log("currentTime " + currentTime);

                                var timeRemaining = timerEnd - currentTime; // Time remaining before the expiration of the validation code

                                var display = document.querySelector('#countdownTimer');
                                startCountdown(timeRemaining, display);
                            };

                        </script>
                    </div>
                    
                <div class="form-group">
                    <input class="form-control" id="user_verification_code" name="user_verification_code" placeholder="<?= lang('user_lang.field_verification_code'); ?>" type="text" value="<?= set_value('username'); ?>" />
                </div>
                <p style='color:#9e895a'><?= lang('user_lang.resend_msg'); ?></p>
                <div class="form-group row">
                    <div class = "col">
                        <!-- Resend button
                        => Redirect to prepare mail form, which'll ask again for the 'non-azure' mail before sending a new validation code -->        
                        <a href='<?= base_url('user/auth/prepare_mail_form'); ?>' id="resend_code" name="resend_code" type="submit" class="btn btn-secondary" ><?= lang('user_lang.button_resend_code'); ?></a>
                    </div>
                    <div class="col text-right">
                        <a id="btn_cancel" class="btn btn-secondary" href="<?= base_url(); ?>"><?= lang('common_lang.btn_cancel'); ?></a>
                        <input id="btn_submit" name="btn_submit" type="submit" class="btn btn-primary" value="<?= lang('user_lang.btn_next'); ?>" />
                    </div>
                </div>
                <div>
                    <?= form_hidden('user_email',
                    [
                        'id' => 'user_email',
                        'value' => $userdata['mail'] ?? $azure_mail ?? '',
                    ]); ?>
                <div>

            </fieldset>
            <?= form_close(); ?>
        </div>
    </div>
</div>