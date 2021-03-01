<?php
    use CodeIgniter\I18n\Time;
?>

<h2><?= esc($title) ?></h2>

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
        <td><?php
            $buyingDate = Time::createFromFormat('Y-m-d', $item['buying_date']);
            echo esc($buyingDate->toLocalizedString('dd.MM.Y'));
        ?></td>
        <td><?= esc($item['warranty_duration']) ?> mois</td>
        <td><a href="<?= site_url('stock/item_delete/'.esc($item['id'])) ?>">
            X
        </a></td>
    </tr>
	<?php endforeach ?>
</table>
<p><a href="<?php echo site_url('stock/item_create') ?>">Ajouter un objet</a></p>