<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>
<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('id', !empty($transport_type) ? $transport_type->transport_type_id : 0); ?>">
    <div class="control-group">
        <label class="control-label" for="transport_type_name">Jenis Kenderaan</label>
        <div class="controls">
            <input type="text" class="span4" id="transport_type_name" name="transport_type_name" value="<?php echo set_value('transport_type_name', !empty($transport_type) ? $transport_type->transport_type_name : ''); ?>">
            <span class="label label-important validate-required">Wajib</span>
            <p class="help-block"><i>"Pacuan Empat Roda", "Bas"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="transport_type_cargo">Jenis Muatan</label>
        <div class="controls">
            <select id="transport_type_cargo" name="transport_type_cargo">
                <option value="">-- Sila Pilih</option>
                <?php foreach ($cargo_types as $key => $val): ?>
                    <?php
                    $selected_type_cargo = set_value('transport_type_cargo', !empty($transport_type) ? $transport_type->transport_type_cargo : '');
                    $selected = (($selected_type_cargo != '') && ($selected_type_cargo == $key)) ? "selected" : '';
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
            <span class="label label-important validate-required">Wajib</span>
            <p class="help-block">Jenis muatan samaada orang mahupun berat.</p>
        </div>
    </div>
    <hr/>

    <div class="form-actions">
        <?php echo anchor("transports/list_type", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
        <input type="submit" class="btn btn-primary" value="Simpan"/>
        <?php
        if (!empty($room)) {
            //echo anchor("#deleteItemModal", '<i class="icon-trash"></i> Hapus Rekod', 'class="btn" data-toggle="modal"');
        }
        ?>
    </div>
</fieldset>
<?php echo form_close(); ?>

<div id="deleteItemModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Pengesahan</h3>
    </div>
    <div class="modal-body">
        <p>Hapus rekod dari sistem?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <?php echo anchor("transports/delete/{$transport->transport_type_id}", 'Hapus', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('input[name=status]').change(function() {
            if ($(this).val() == 0) {
                $('textarea[name=status_description]').attr('disabled', false);
            }
            else {
                $('textarea[name=status_description]').attr('disabled', true);
            }
        });
    });
</script>