<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>

<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($booking) ? $booking->booking_id : 0); ?>">
    <input type="hidden" id="fk_user_id" name="fk_user_id" value="<?php echo $this->tank_auth->get_user_id(); ?>">

    <div class="well well-form">
        <div class="control-group">
            <label class="control-label" for="trip_type">Jenis Tempahan</label>
            <div class="controls">
                <label class="checkbox">
                    <input type="checkbox" name="full_day" id="full_day" value="1"> <div class="">Sepanjang hari</div>
                </label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="start_date">Masa Mula</label>
            <div class="controls">
                <input readonly autocomplete="off" type="text" class="span2" id="start_date" name="start_date" value="<?php echo set_value('start_date', !empty($room) ? $room->start_date : ''); ?>">
                <span class="label label-important validate-required">Wajib</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="end_date">Masa Tamat</label>
            <div class="controls">
                <input readonly autocomplete="off" type="text" class="span2" id="end_date" name="end_date" value="<?php echo set_value('end_date', !empty($room) ? $room->end_date : ''); ?>">
                <span class="label label-important validate-required">Wajib</span>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button id="check_availability" class="btn btn-small btn-primary" type="button"><i class="icon-refresh"></i> Kemaskini Status Peralatan</button>
            </div>
        </div>
        <div class="control-group msg-check-success">
            <label class="control-label"></label>
            <div class="controls padding-right20">
                <div id="ajax-placeholder-success" class="alert margin-bottom0 alert-success"></div>
            </div>
        </div>
        <div class="control-group msg-check-error">
            <label class="control-label"></label>
            <div class="controls padding-right20">
                <div id="ajax-placeholder-error" class="alert margin-bottom0 alert-error"></div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs margin-bottom0" id="myTab">
        <li class="active"><a href="#peralatan">Maklumat Tempahan Peralatan</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade active in" id="peralatan">
            <div class="control-group">
                <label class="control-label" for="equipments_type_id">Peralatan</label>
                <div class="controls">

                    <?php foreach ($equipments as $equipment): ?>
                        <?php if (!$equipment->deleted && ($equipment->status)): ?>
                            <div class="input-prepend input-append">
                                <span class="add-on width1-add-on"><?php echo $equipment->name ?></span>
                                <span id="equipment<?php echo $equipment->equipment_id; ?>" class="add-on width2-add-on"><?php echo $equipment->quantity ?> unit</span>
                                <input autocomplete="off" placeholder="0" class="span1 validate-quantity" name="equipments_type_id[<?php echo $equipment->equipment_id ?>]" type="text">
                            </div>
                        <?php endif; ?>
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
                    <input autocomplete="off" type="text" id="place" value="" name="place" class="span4" placeholder="Taip Nama Tempat" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $room_list; ?>]'>
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block">Tempat di mana peralatan akan digunakan.</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="equipment_purpose">Tujuan</label>
                <div class="controls">
                    <input type="text" class="span4" id="equipment_purpose" name="equipment_purpose" maxlength="150" value="<?php echo set_value('equipment_purpose', !empty($booking) ? $booking->equipment_purpose : ''); ?>">
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block">Tujuan tempahan peralatan.</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="require_technical_person">Bantuan Teknikal</label>
                <div class="controls">
                    <label class="checkbox">
                        <input type="checkbox" name="require_technical_person" id="require_technical_person" value="1"> Diperlukan
                    </label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="booking_equipment_description">Penerangan</label>
                <div class="controls">
                    <textarea rows="2" class="span4" id="booking_equipment_description" name="booking_equipment_description"><?php echo set_value('booking_equipment_description', !empty($booking) ? $booking->booking_equipment_description : ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions padding-left0">
        <div class="control-group">
            <div class="controls">
                <?php echo anchor("booking_equipments", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
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
        <?php echo anchor("booking_rooms/delete/{$booking->booking_id}", 'Hapus', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        $('.msg-check-success').hide();
        $('.msg-check-error').hide();
                
        $('#check_availability').click(function(){
            $('.msg-check').show();
                        
            var start_date = $('input[name=start_date]').val();
            var end_date = $('input[name=end_date]').val();
                
            if ((start_date != '') && (end_date != '')) {
                $('#ajax-placeholder').html('<p><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></p>');
                        
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('ajaxify/check_availability/3'); ?>",
                    data: "start_date=" + start_date + "&end_date=" + end_date,
                    dataType: 'json',
                    success: function(msg){
                        
                        for(var e in msg){
                            $('#equipment' + e).html(msg[e] + ' unit');
                        }
                        
                        $('input[type=submit]').attr('disabled', false);
                        $('#ajax-placeholder-success').html('<i class="icon-ok"></i> Senarai kekosongan peralatan telah dikemaskinikan.');
                        $('.msg-check-success').fadeIn();
                        
                    }
                }); 
            }
            else{
                $('input[type=submit]').attr('disabled', true);
                $('#ajax-placeholder-equipment-error').html('<i class="icon-info-sign"></i> Sila pilih tarikh dan bilik terlebih dahulu.');
                $('.msg-check-equipment-error').fadeIn();
            }
        });
                
        $('input[name=start_date], input[name=end_date]').focus(function(){
            $('.msg-check-success').fadeOut();
            $('.msg-check-error').fadeOut();
        });
                
    });
</script>