<?php
/**
 * Custom Error list
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
if (! empty($errors)) : ?>
    <div class="alert alert-danger" role="alert">
        <ul>
            <?php foreach ($errors as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>