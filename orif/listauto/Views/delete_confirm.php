<h2><?= esc($title) ?></h2>
<p><strong>Nom : </strong><?= esc($item['name']) ?></p>
<p><strong>No inventaire : </strong><?= esc($item['inventory_nb']) ?></p>
<br>
<p>
    <a href="<?= site_url('stock/item_delete/'.$item['id'].'/confirmed') ?>">Supprimer</a>&nbsp;&nbsp;&nbsp;
    <a href="<?= site_url('stock/items_list') ?>">Annuler</a>
</p>