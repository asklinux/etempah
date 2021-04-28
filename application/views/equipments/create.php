<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>
<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($equipment) ? $equipment->equipment_id : 0); ?>">
    <div class="control-group">
        <label class="control-label" for="name">Nama Peralatan</label>
        <div class="controls">
            <input type="text" class="span4" id="name" name="name" value="<?php echo set_value('name', !empty($equipment) ? $equipment->name : ''); ?>">
            <span class="label label-important validate-required">Wajib</span>
            <p class="help-block"><i>"Komputer Riba", "Projektor"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="quantity">Kuantiti</label>
        <div class="controls">
            <input type="text" class="span2" id="quantity" name="quantity" maxlength="4" value="<?php echo set_value('quantity', !empty($equipment) ? $equipment->quantity : ''); ?>">
            <span class="label label-important validate-number-only">Wajib</span>
            <p class="help-block">Kuantiti peralatan yang ada. <strong>Nombor sahaja</strong>.</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="description">Penerangan</label>
        <div class="controls">
            <textarea rows="2" class="span4" id="description" name="description"><?php echo set_value('description', !empty($equipment) ? $equipment->description : ''); ?></textarea>
            <p class="help-block">Penerangan peralatan.</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="status">Status</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="status" id="status1" value="1" <?php echo ((!empty($equipment) && $equipment->status == 1) || empty($equipment)) ? 'checked' : ''; ?>> Aktif
            </label>
            <label class="radio">
                <input type="radio" name="status" id="status2" value="0" <?php echo (!empty($equipment) && empty($equipment->status) ? 'checked' : ''); ?>> Tidak Aktif
            </label>
            <textarea <?php echo (!empty($equipment) && empty($equipment->status)) ? '' : 'disabled'; ?> rows="2" class="span4" id="status_description" name="status_description"><?php echo set_value('status_description', !empty($equipment) ? $equipment->status_description : ''); ?></textarea>
            <p class="help-block">Penerangan tidak aktif.</p>
        </div>
    </div>

    <hr/>

    <div class="form-actions">
        <?php echo anchor("equipments", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
        <input type="submit" class="btn btn-primary" value="Simpan"/>
        <?php
        if (!empty($room)) {
//                                echo anchor("#deleteItemModal", '<i class="icon-trash"></i> Hapus Rekod', 'class="btn" data-toggle="modal"');
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
        <?php echo anchor("equipments/delete/{$equipment->equipment_id}", 'Hapus', 'class="btn btn-primary"'); ?>
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