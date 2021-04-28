<?php echo anchor('transports/create_type', '<i class="icon-plus-sign"></i> Tambah Rekod', 'class="btn"'); ?> 
<?php echo anchor('#deleteModal', '<i class="icon-trash"></i> Hapus Rekod', 'id="delete-list" class="btn disabled" data-toggle="modal"'); ?>

<br/><br/>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th width="5%"></th>
            <th width="85%">Jenis</th>
            <th width="10%">Unit</th>
            <th width="10%" class="hide">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($transport_type_total)): ?>
            <?php foreach ($transport_type_total as $transport_type): ?>
                <tr data-provides="rowlink">
                    <td class="nolink"><input type="checkbox" name="delete[<?php echo $transport_type->transport_type_id; ?>]" value="<?php echo $transport_type->transport_type_id; ?>"></td>
                    <td><?php echo $transport_type->transport_type_name; ?></td>
                    <td><?php echo ($transport_type->total == '') ? '0' : $transport_type->total; ?></td>
                    <td class="hide">
                        <?php echo anchor("transports/create_type/{$transport_type->transport_type_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tiada senarai kenderaan dijumpai.</td>
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
        <?php echo form_open('transports/delete_type', 'id="form-delete-list"'); ?>
        <input type="hidden" name="ids"/>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <input type="submit" class="btn btn-primary" value="Hapus Rekod"/>
        <?php echo form_close(); ?>
    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        $('#myTab a').click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
