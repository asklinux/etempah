<?php if ($this->tank_auth->is_admin()): ?>
<?php echo anchor('rooms/create', '<i class="icon-plus-sign"></i> Tambah Rekod', 'class="btn"'); ?> 
<?php echo anchor('#deleteModal', '<i class="icon-trash"></i> Hapus Rekod', 'id="delete-list" class="btn disabled" data-toggle="modal"'); ?>
<br/><br/>
<?php endif; ?>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <?php if ($this->tank_auth->is_admin()): ?><th width="5%"></th><?php endif; ?>
            <th width="25%">Nama Bilik</th>
            <th width="15%">Lokasi</th>
            <th width="15%">Blok / Bangunan</th>
            <th width="10%">Aras</th>
            <th width="10%">Penyelia</th>
            <th width="25%" class="hidden-tablet hidden-phone">Maklumat Bilik</th>
            <th width="0%" class="hide">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
                <?php $row_type = ($room->status == 0) ? 'class="error"' : ''; ?>
                <tr data-provides="rowlink" <?php echo $row_type ?>>
                    <?php if ($this->tank_auth->is_admin()): ?><td class="nolink"><input type="checkbox" name="delete[<?php echo $room->room_id; ?>]" value="<?php echo $room->room_id; ?>"></td><?php endif; ?>
                    <td><?php echo $room->name; ?></td>
                    <td><?php echo $room->location; ?></td>
                    <td><?php echo $room->building; ?></td>
                    <td><?php echo $room->floor; ?></td>
                    <td class="hidden-tablet hidden-phone"><?php echo $room->fk_user_id ? 'Admin Bahagian' : 'Admin Utama'; ?></td>
                    <td><strong>Kapasiti:</strong> <?php echo $room->capacity; ?><br/><strong>Fasiliti:</strong> <?php echo $room->facilities ? $room->facilities : 'Tiada'; ?></td>
                    <td class="hide">
                        <?php echo anchor("rooms/create/{$room->room_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tiada senarai bilik dijumpai.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Pengesahan</h3>
    </div>
    <div class="modal-body">
        <p>Hapus rekod dari sistem?</p>
    </div>
    <div class="modal-footer">
        <?php echo form_open('rooms/delete', 'id="form-delete-list"'); ?>
        <input type="hidden" name="ids"/>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <input type="submit" class="btn btn-primary" value="Hapus Rekod"/>
        <?php echo form_close(); ?>
    </div>
</div>