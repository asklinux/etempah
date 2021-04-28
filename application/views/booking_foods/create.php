<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>

<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($booking) ? $booking->id : 0); ?>">
    <input type="hidden" id="fk_user_id" name="fk_user_id" value="<?php echo $this->tank_auth->get_user_id(); ?>">

    <ul class="nav nav-tabs margin-bottom0" id="myTab">
        <li class="active"><a href="#makanan">Maklumat Tempahan Makanan</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade active in" id="makanan">
            <div class="control-group">
                <label class="control-label" for="start_date">Tarikh Mula</label>
                <div class="controls">
                    <input readonly type="text" class="span3" id="start_date" name="start_date" autocomplete="off" value="<?php echo set_value('start_date'); ?>">
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="end_date">Tarikh Tamat</label>
                <div class="controls">
                    <input readonly type="text" class="span3" id="end_date" name="end_date" autocomplete="off" value="<?php echo set_value('end_date'); ?>">
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Jenis Tempahan</label>
                <div class="controls">
                    <?php foreach ($food_types as $key => $food_type): ?>
                        <label class="checkbox">
                            <input type="checkbox" name="food_type_id[]"  value="<?php echo $key; ?>"> <?php echo $food_type; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="secretariat">Urusetia</label>
                <div class="controls">
                    <input autocomplete="off" type="text" id="secretariat" name="secretariat" class="span4" placeholder="Taip Nama Urusetia" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $user_list; ?>]'>
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="place">Tempat</label>
                <div class="controls">
                    <input autocomplete="off" type="text" id="place" value="" name="place" class="span4" placeholder="Taip Nama Tempat" style="margin: 0 auto;" data-provide="typeahead" data-items="4" data-source='[<?php echo $room_list; ?>]'>
<!--                        <input type="text" class="span4" id="place" name="place" value="<?php echo set_value('place'); ?>">-->
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block">Lokasi untuk penghantaran dan penyediaan makanan.</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="food_purpose">Tujuan</label>
                <div class="controls">
                    <input type="text" class="span4" id="food_purpose" name="food_purpose" maxlength="150" value="<?php echo set_value('food_purpose'); ?>">
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block">Tujuan tempahan makanan.</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="total_pack">Jumlah Pegawai</label>
                <div class="controls">
                    <input type="text" class="span2" id="total_pack" name="total_pack" value="<?php echo set_value('total_pack'); ?>">
                    <span class="label label-important validate-number-only">Wajib</span>
                    <p class="help-block"><strong>Nombor sahaja</strong>.</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="booking_food_description">Penerangan</label>
                <div class="controls">
                    <textarea rows="2" class="span4" id="booking_food_description" name="booking_food_description"><?php echo set_value('booking_food_description'); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions padding-left0">
        <div class="control-group">
            <div class="controls">
                <?php echo anchor("booking_foods", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
                <input type="submit" class="btn btn-primary" value="Simpan"/>
            </div>
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
        <?php echo anchor("transports/delete/{$booking->id}", 'Hapus', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        $('#driver_status').popover().click(function(e) {e.preventDefault()});
    });
</script>