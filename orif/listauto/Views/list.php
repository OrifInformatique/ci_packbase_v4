<div id="list_auto" class="container">
    <h2><?= esc($list_title) ?></h2>

    <table id="item_list_table">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>No inventaire</th>
            <th>Date d'achat</th>
            <th>Durée garantie</th>
            <th>Supprimer</th>
        </tr>

        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= esc($item['id']) ?></td>
            <td><a href="<?= site_url('stock/item_update/'.esc($item['id'])) ?>">
                <?= esc($item['name']) ?>
            </a></td>
            <td><?= esc($item['inventory_nb']) ?></td>
            <td><?= esc($item['buying_date']) ?></td>
            <td><?= esc($item['warranty_duration']) ?> mois</td>
            <td><a href="<?= site_url('stock/item_delete/'.esc($item['id'])) ?>">
                X
            </a></td>
        </tr>
        <?php endforeach ?>
    </table>
    <p><a href="<?php echo site_url('stock/item_create') ?>">Ajouter un objet</a></p>
</div>