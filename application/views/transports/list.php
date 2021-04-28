<?php
$btn_style_disabled = '';
$msg_style_disabled = 'hide';
if (empty($transport_types)):
    $btn_style_disabled = 'disabled';
    $msg_style_disabled = '';
endif;
?>

<div class="alert alert-error <?php echo $msg_style_disabled ?>">Rekod Jenis Kenderaan tidak wujud. Sila tambah rekod Jenis Kenderaan untuk teruskan.</div>

<?php echo anchor('transports/create', '<i class="icon-plus-sign"></i> Tambah Rekod', "class='btn {$btn_style_disabled}'"); ?> 
<?php echo anchor('#deleteTransportModal', '<i class="icon-trash"></i> Hapus Rekod', 'id="delete-list" class="btn disabled" data-toggle="modal"'); ?>

<br/><br/>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th width="5%"></th>
            <th width="50%">Model</th>
            <th width="20%">Jenis</th>
            <th width="25%">Berat Muatan (tan) / <br/>Bil. Penumpang (orang)</th>
            <th width="10%" class="hide">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($transports)): ?>
            <?php foreach ($transports as $transport): ?>
                <?php $row_type = ($transport->status == 0) ? 'class="error"' : ''; ?>
                <tr data-provides="rowlink" <?php echo $row_type ?>>
                    <td class="nolink"><input type="checkbox" name="delete[<?php echo $transport->transport_id; ?>]" value="<?php echo $transport->transport_id; ?>"></td>
                    <td><?php echo $transport->name; ?></td>
                    <td><?php echo $transport->transport_type_name; ?></td>
                    <td><?php echo $transport->capacity . " " . (!empty($transport->transport_type_cargo) ? $cargo_types_name[$transport->transport_type_cargo] : ''); ?></td>
                    <td class="hide">
                        <?php echo anchor("transports/create/{$transport->transport_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">Tiada senarai kenderaan dijumpai.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="deleteTransportModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Pengesahan</h3>
    </div>
    <div class="modal-body">
        <p>Hapus rekod dari sistem?</p>
    </div>
    <div class="modal-footer">
        <?php echo form_open('transports/delete', 'id="form-delete-list"'); ?>
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