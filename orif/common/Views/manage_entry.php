<?php
/**
 * Common view for entry management
 *
 * @author      Orif (DeDy)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 *
 */

/**
 * Values needed
 *
 * @param array entry            => The entry being managed.
 *     [
 *          'type'               => string, Type of the entry being managed.
 *          'name'               => string, Name of the entry being managed.
 *          'message'            => string, Addidional info about the entry about to be managed. Can be empty.
 *          'data'               => array, Additional data about the entry being managed. Can be empty.
 *              [
 *                  'name'       => Name of the additional data
 *                  'value'      => Value of the additional data
 *              ]
 *     ]
 * @param array linked_entries   => Entries that are linked with the entry being managed. Can be empty.
 *     [
 *         'type'                => string, Type of the linked entry
 *         'name'                => string, Name of the linked entry
 *         'data'               => array, Additional data about the linked entry.
 *              [
 *                  'name'       => Name of the additional data
 *                  'value'      => Value of the additional data
 *              ]
 *     ]
 * @param string cancel_btn_url  => Url of the cancel button.
 * @param array primary_action   => Action we ask a confirmation for. Can be empty.
 *     [
 *         'name'                => string, required, Text of the primary action button
 *         'url'                 => string, required, Url of the primary action button
 *     ]
 * @param array secondary_action => Alternative action to do. Same structure as primary button. Can be empty.
 *
 * NB : Params without "Can be empty." at end of desc have to be provided.
 *      Empty values won't be displayed if they are not provided.
 *
 */

?>

<div id="page-content-wrapper">
    <div class="container">
        <h1><?= lang('common_lang.title_manage_entry') ?></h1>

        <p><?= lang('common_lang.manage_entry_confirmation') ?></p>

        <div class="alert alert-primary">
            <p class="mb-0">
                <strong>
                    <?= $entry['type'] ?>
                </strong>
                <br>
                <?= $entry['name'] ?>
            </p>

            <?php if(isset($entry['data']) && !empty($entry['data'])): ?>
                <p class="mt-2 mb-0">
                    <?php foreach($entry['data'] as $additional_data): ?>
                        <?= $additional_data['name'] ?> : <?= $additional_data['value'] ?><br>
                    <?php endforeach ?>
                </p>
            <?php endif ?>
        </div>

        <?php if(isset($linked_entries) && !empty($linked_entries)): ?>
            <div>
                <h2><?= lang('common_lang.entries_linked_to_entry_being_managed') ?></h2>

                <div>
                    <?php foreach($linked_entries as $linked_entry): ?>
                        <div class="alert alert-secondary">
                            <p class="mb-0">
                                <strong>
                                    <?= $linked_entry['type'] ?>
                                </strong>
                                <br>
                                <?= $linked_entry['name'] ?>
                            </p>

                            <?php if(isset($linked_entry['data']) && !empty($linked_entry['data'])): ?>
                                <p class="mt-2 mb-0">
                                    <?php foreach($linked_entry['data'] as $additional_data): ?>
                                        <?= $additional_data['name'] ?> : <?= $additional_data['value'] ?><br>
                                    <?php endforeach ?>
                                </p>
                            <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>

        <?php if(isset($entry['message']) && !empty($entry['message'])): ?>
            <p class="alert alert-<?= $entry['message']['type'] ?>">
                <?= $entry['message']['text'] ?>
            </p>
        <?php endif ?>

        <div class="text-right">
            <a class="btn btn-secondary" href="<?= $cancel_btn_url ?>">
                <?= lang('common_lang.btn_cancel'); ?>
            </a>

            <?php if(isset($secondary_action) && !empty($secondary_action)): ?>
                <a class="btn btn-primary" href="<?= $secondary_action['url'] ?>">
                    <?= $secondary_action['name'] ?>
                </a>
            <?php endif ?>

            <?php if(isset($primary_action) && !empty($primary_action)): ?>
                <a class="btn btn-danger" href="<?= $primary_action['url'] ?>">
                    <?= $primary_action['name'] ?>
                </a>
            <?php endif ?>
        </div>
    </div>
</div>