<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>
<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($transport) ? $transport->transport_id : 0); ?>">
    <div class="control-group">
        <label class="control-label" for="name">Model</label>
        <div class="controls">
            <input type="text" class="span4" id="name" name="name" value="<?php echo set_value('name', !empty($transport) ? $transport->name : ''); ?>">
            <span class="label label-important validate-required">Wajib</span>
            <p class="help-block"><i>"Proton Perdana V6", "Toyota Fortuner"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="transport_type">Jenis</label>
        <div class="controls">
            <select id="transport_type" name="transport_type" class="span2">
                <option value="">-- Sila Pilih</option>
                <?php foreach ($transport_types as $transport_type): ?>
                    <?php
		    $selected = set_select('transport_type',$transport_type->transport_type_id, (!empty($transport) && ($transport->transport_type == $transport_type->transport_type_id)) ? TRUE : FALSE );
//                    $selected = (!empty($transport) && ($transport->transport_type == $transport_type->transport_type_id)) ? "selected" :  set_select('transport_type', $transport_type->transport_type_id, '' ); ?>;
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $transport_type->transport_type_id ?>"><?php echo $transport_type->transport_type_name; ?></option>
                <?php endforeach; ?>
            </select>
            <span class="label label-important validate-required">Wajib</span>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="capacity">Berat Muatan (tan) / <br/>Bil. Penumpang (orang)</label>
        <div class="controls">
            <input type="text" class="span2" id="capacity" name="capacity" maxlength="2" value="<?php echo set_value('capacity', !empty($transport) ? $transport->capacity : ''); ?>">
            <span class="label label-important validate-number-only">Wajib</span>
            <p class="help-block"><strong>Nombor sahaja</strong>.</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="description">Penerangan</label>
        <div class="controls">
            <textarea rows="2" class="span4" id="description" name="description"><?php echo set_value('description', !empty($transport) ? $transport->description : ''); ?></textarea>
            <p class="help-block">Penerangan kenderaan</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="status">Status</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="status" id="status1" value="1" <?php echo ((!empty($transport) && $transport->status == 1) || empty($transport)) ? 'checked' : ''; ?>> Aktif
            </label>
            <label class="radio">
                <input type="radio" name="status" id="status2" value="0" <?php echo (!empty($transport) && empty($transport->status) ? 'checked' : ''); ?>> Tidak Aktif
            </label>
            <textarea <?php echo (!empty($transport) && empty($transport->status)) ? '' : 'disabled'; ?> rows="2" class="span4" id="status_description" name="status_description"><?php echo set_value('status_description', !empty($transport) ? $transport->status_description : ''); ?></textarea>
            <p class="help-block">Penerangan tidak aktif.</p>
        </div>
    </div>

    <hr/>
    
    <div class="form-actions">
        <?php echo anchor("transports", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
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
        <?php echo anchor("transports/delete/{$transport->transport_id}", 'Hapus', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('input[name=status]').change(function(){
            if ($(this).val() == 0) {
                $('textarea[name=status_description]').attr('disabled', false);
            }
            else {
                $('textarea[name=status_description]').attr('disabled', true);
            }
        });
    });
</script>