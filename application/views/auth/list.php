<div class="btn-group">
    <?php echo anchor('auth/register', '<i class="icon-plus-sign"></i> Tambah Pengguna', 'class="btn"'); ?> 
    <?php echo anchor('auth/import', '<i class="icon-hdd"></i> Tambah Pengguna Secara Pukal', 'class="btn"'); ?>
</div>

<?php
if ($this->tank_auth->is_admin()) {
    //echo anchor('#deleteModal', '<i class="icon-trash"></i> Hapus Rekod', 'id="delete-list" class="btn disabled" data-toggle="modal"');
}
?> 

<br/><br/>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th width="35%">Nama Penuh</th>
            <th width="30%">Email</th>
            <th width="20%">Tahap Pengguna</th>
            <th width="15%">Status</th>
            <th width="10%" class="hide">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <?php //if ($user->user_level != 1): ?>
                    <?php $row_type = (in_array($user->activated, array(0,2))) ? 'class="error"' : ''; ?>
                    <tr data-provides="rowlink" <?php echo $row_type ?>>
                        <td><?php echo $user->full_name; ?></td>
                        <td><?php echo $user->email; ?></td>
                        <td><?php echo $user_levels[$user->user_level]; ?></td>
                        <td><?php echo $user_status[$user->activated]; ?></td>
                        <td class="hide">
                            <?php echo anchor("auth/profile/{$user->fk_user_id}", '<i class="icon-edit"></i>', 'class="btn btn-mini"'); ?>
                        </td>
                    </tr>
                <?php //endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tiada senarai tempahan peralatan dijumpai.</td>
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
        <?php echo form_open('auth/deactivate', 'id="form-delete-list"'); ?>
        <input type="hidden" name="ids"/>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <input type="submit" class="btn btn-primary" value="Hapus Rekod"/>
        <?php echo form_close(); ?>
    </div>
</div>