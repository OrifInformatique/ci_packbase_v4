<div class="list_auto container">
    <h3><?= esc($list_title) ?></h3>

    <div class="row mb-2">
        <div class="col-sm-5 text-left">
            <a class="btn btn-primary" href="<?php echo site_url('stock/item_create') ?>">Ajouter un élément</a>
        </div>
        <div class="col-sm-7 text-right">
            <label class="btn btn-default form-check-label" for="toggle_deleted">Afficher les éléments supprimés</label>
            <input type="checkbox" name="toggle_deleted" value=""  id="toggle_deleted" />
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nom</th>
                    <th scope="col">No inventaire</th>
                    <th scope="col">Date d'achat</th>
                    <th scope="col">Durée garantie</th>
                    <th class="text-right" scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= esc($item['id']) ?></td>
                    <td><a href="<?= site_url('stock/item_update/'.esc($item['id'])) ?>">
                        <?= esc($item['name']) ?>
                    </a></td>
                    <td><?= esc($item['inventory_nb']) ?></td>
                    <td><?= esc($item['buying_date']) ?></td>
                    <td><?= esc($item['warranty_duration']) ?> mois</td>
                    <td class="text-right"><a href="<?= site_url('stock/item_delete/'.esc($item['id'])) ?>">
                        <!-- Bootstrap Trash icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                        </svg>
                    </a></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>