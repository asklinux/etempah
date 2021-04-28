<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>
<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($room) ? $room->room_id : 0); ?>">
    <?php if (!$this->tank_auth->is_subadmin()): ?>
    <div class="control-group">
        <label class="control-label" for="fk_user_id">Pentadbir</label>
        <?php if ($this->tank_auth->is_admin()): ?>
            <div class="controls">
                <select id="fk_user_id" name="fk_user_id" class="span4">
                    <option value="">-- Sila Pilih</option>
                    <?php foreach ($sub_admins as $sub_admin): ?>
                    <option value="<?php echo $sub_admin->id ?>" <?php echo ((!empty($room)) && ($sub_admin->id == $room->fk_user_id)) ? "selected" : ""; ?>><?php echo $sub_admin->full_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="control-group">
        <label class="control-label" for="name">Nama Bilik</label>
        <div class="controls">
            <input type="text" class="span4" id="name" name="name" value="<?php echo set_value('name', !empty($room) ? $room->name : ''); ?>">
            <span class="label label-important validate-required">Wajib</span>
            <p class="help-block"><i>"Bilik Mesyuarat Cyber", "Auditorium 1", "Bilik Latihan"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="location">Lokasi</label>
        <div class="controls">
            <input type="text" class="span4" id="location" name="location" value="<?php echo set_value('location', !empty($room) ? $room->location : ''); ?>">
            <span class="label label-important validate-required">Wajib</span>
            <p class="help-block"><i>"Presint 1, Putrajaya", "Cyberjaya"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="building">Blok / Bangunan</label>
        <div class="controls">
            <input type="text" class="span4" id="building" name="building" value="<?php echo set_value('building', !empty($room) ? $room->building : ''); ?>">
            <p class="help-block"><i>"Parcel A1", "B2"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="floor">Aras / Tingkat</label>
        <div class="controls">
            <input type="text" class="span2" id="floor" name="floor" maxlength="10" value="<?php echo set_value('floor', !empty($room) ? $room->floor : ''); ?>">
            <p class="help-block"><i>"3", "4A"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="capacity">Kapasiti Bilik</label>
        <div class="controls">
            <input type="text" class="span2" id="capacity" name="capacity" maxlength="4" value="<?php echo set_value('capacity', !empty($room) ? $room->capacity : ''); ?>">
            <span class="label label-important validate-number-only">Wajib</span>
            <p class="help-block">Jumlah orang boleh dimuatkan. <strong>Nombor sahaja</strong>.</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="facilities">Fasiliti / Kemudahan</label>
        <div class="controls">
            <input type="text" class="span4" id="facilities" name="facilities" value="<?php echo set_value('facilities', !empty($room) ? $room->facilities : ''); ?>">
            <p class="help-block"><i>"2 projektor, 1 whiteboard besar"</i></p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="description">Penerangan</label>
        <div class="controls">
            <textarea rows="2" class="span4" id="description" name="description"><?php echo set_value('description', !empty($room) ? $room->description : ''); ?></textarea>
            <p class="help-block">Penerangan bilik.</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="status">Status</label>
        <div class="controls">
            <label class="radio">
                <input type="radio" name="status" id="status1" value="1" <?php echo ((!empty($room) && $room->status == 1) || empty($room)) ? 'checked' : ''; ?>> Aktif
            </label>
            <label class="radio">
                <input type="radio" name="status" id="status2" value="0" <?php echo (!empty($room) && empty($room->status) ? 'checked' : ''); ?>> Tidak Aktif
            </label>
            <textarea <?php echo (!empty($room) && empty($room->status)) ? '' : 'disabled'; ?> rows="2" class="span4" id="status_description" name="status_description"><?php echo set_value('status_description', !empty($room) ? $room->status_description : ''); ?></textarea>
            <p class="help-block">Penerangan tidak aktif.</p>
        </div>
    </div>

    <hr/>

    <div class="control-group">
        <label class="control-label"></label>
        <div class="controls">
            <?php echo anchor("rooms", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
            <input type="submit" class="btn btn-primary" value="Simpan"/>
            <?php
            if (!empty($room)) {
                //echo anchor("#deleteItemModal", '<i class="icon-trash"></i> Hapus Rekod', 'class="btn" data-toggle="modal"');
            }
            ?>
        </div>
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
        <?php echo anchor("rooms/delete/{$room->room_id}", 'Hapus', 'class="btn btn-primary"'); ?>
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