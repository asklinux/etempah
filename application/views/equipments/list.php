<?php echo anchor('equipments/create', '<i class="icon-plus-sign"></i> Tambah Rekod', 'class="btn"'); ?> 
<?php echo anchor('#deleteModal', '<i class="icon-trash"></i> Hapus Rekod', 'id="delete-list" class="btn disabled" data-toggle="modal"'); ?>

<br/><br/>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th width="5%"></th>
            <th width="85%">Nama Peralatan</th>
            <th width="10%">Kuantiti</th>
            <th width="10%" class="hide">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($equipments)): ?>
            <?php foreach ($equipments as $equipment): ?>
                <?php if (!$equipment->deleted): ?>
                    <?php $row_type = ($equipment->status == 0) ? 'class="error"' : ''; ?>
                    <tr data-provides="rowlink" <?php echo $row_type ?>>
                        <td class="nolink"><input type="checkbox" name="delete[<?php echo $equipment->equipment_id; ?>]" value="<?php echo $equipment->equipment_id; ?>"></td>
                        <td><?php echo $equipment->name; ?></td>
                        <td><?php echo $equipment->quantity; ?></td>
                        <td class="hide">
                            <?php echo anchor("equipments/create/{$equipment->equipment_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tiada senarai peralatan dijumpai.</td>
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
        <?php echo form_open('equipments/delete', 'id="form-delete-list"'); ?>
        <input type="hidden" name="ids"/>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <input type="submit" class="btn btn-primary" value="Hapus Rekod"/>
        <?php echo form_close(); ?>
    </div>
</div>