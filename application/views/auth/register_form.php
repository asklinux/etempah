<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>

<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($room) ? $room->id : 0); ?>">
    <div class="control-group">
        <label class="control-label" for="name">Peranan Pengguna</label>
        <div class="controls">
            <select id="user_level" name="user_level">
                <option value="">-- Sila Pilih</option>
                <?php foreach ($user_levels as $key => $val): ?>
                    <?php //if ($key == 0 || $key == 2): ?>
                    <?php
                    $selected_user_level = set_value('user_level');
                    $selected = (($selected_user_level != '') && ($selected_user_level == $key)) ? "selected" : '';
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                    <?php //endif; ?>
                <?php endforeach; ?>
            </select>
            <span class="label label-important validate-required">Wajib</span>
        </div>
    </div>
    <!--    <div class="control-group">
            <label class="control-label" for="Username">ID Pengguna</label>
            <div class="controls">
                <input autocomplete="off" type="text" class="span4" id="Username" name="Username" value="<?php echo set_value('Username'); ?>">
                <span class="label label-important validate-required">Wajib</span>
            </div>
        </div>-->
    <div class="control-group">
        <label class="control-label" for="email">Emel Pengguna</label>
        <div class="controls">
            <input autocomplete="off" type="text" class="span4" id="email" name="email" value="<?php echo set_value('email'); ?>">
            <span class="label label-important validate-email-only">Wajib</span>
        </div>
    </div>
    <div class="form-actions">
        <?php echo anchor("auth/users", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
        <input type="submit" class="btn" value="Simpan"/>
        <?php
//        if (!empty($room)) {
//            echo anchor("#deleteItemModal", '<i class="icon-trash"></i> Hapus Rekod', 'class="btn" data-toggle="modal"');
//        }
        ?>
    </div>
</fieldset>
<?php echo form_close(); ?>
