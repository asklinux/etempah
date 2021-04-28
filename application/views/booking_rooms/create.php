<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>
<?php //echo date('ymdHis') . $this->tank_auth->get_user_id();      ?>
<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($booking) ? $booking->booking_id : 0); ?>">
    <input type="hidden" id="fk_user_id" name="fk_user_id" value="<?php echo $this->tank_auth->get_user_id(); ?>">

    <div class="well well-form">
        <div class="control-group">
            <label class="control-label" for="trip_type">Jenis Tempahan</label>
            <div class="controls">
                <select name="full_day">
                    <option value="1">Slot Masa</option>
                    <option value="2">Sepanjang Hari</option>
                </select>
                <!--                <label class="checkbox">
                                    <input type="checkbox" name="full_day" id="full_day" value="1"> <div class="">Sepanjang hari</div>
                                </label>-->
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
                <button id="check_availability" class="btn btn-small btn-primary" type="button"><i class="icon-search"></i> Periksa kekosongan</button>
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
            <div>&nbsp;</div>
        </div>
    </div>

    <div id="form-details">
        <ul class="nav nav-tabs margin-bottom0" id="myTab">
            <li class="active"><a href="#mesyuarat">Maklumat Mesyuarat</a></li>
            <li><a href="#makanan">Maklumat Tempahan Makanan</a></li>
            <li><a href="#peralatan">Maklumat Tempahan Peralatan</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade active in" id="mesyuarat">
                <div class="control-group">
                    <label class="control-label" for="fk_room_id">Bilik</label>
                    <div class="controls">
                        <select id="fk_room_id" name="fk_room_id" class="span4">
                            <option value="">-- Sila Pilih</option>

                            <?php $current_location = set_value('fk_room_id'); ?>
                            <?php foreach ($room_list as $room): ?>
                                <?php
                                if (empty($current_location) || (strtolower($current_location) != strtolower($room->location))):
                                    $current_location = $room->location;
                                    ?>
                        <!--                                <optgroup label="<?php echo "{$room->location}"; ?>"></optgroup>-->
                                <?php endif; ?>
                                <?php
                                $selected_room_id = set_value('fk_room_id');
                                $set_selected_room = (!empty($selected_room_id) && ($selected_room_id == $room->room_id)) ? 'selected' : '';
                                ?>

            <!--                            <option <?php echo $set_selected_room; ?> value="<?php echo $room->room_id; ?>"><?php echo "{$room->building} {$room->floor} {$room->name}"; ?> (<?php echo $room->capacity; ?>)</option>-->
                            <?php endforeach; ?>

                        </select>
                        <span class="label label-important validate-required">Wajib</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="room_purpose">Nama Mesyuarat</label>
                    <div class="controls">
                        <input type="text" class="span4" id="room_purpose" name="room_purpose" maxlength="150" value="<?php echo set_value('room_purpose', !empty($booking) ? $booking->room_purpose : ''); ?>">
                        <span class="label label-important validate-required">Wajib</span>
                        <p class="help-block"><i>"Mesyuarat Jawatankuasa A Bil. 1"</i></p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="secretariat">Urusetia</label>
                    <div class="controls">
                        <input autocomplete="off" type="text" id="secretariat" name="secretariat" class="span4" placeholder="Taip Nama Urusetia" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $user_list; ?>]'>
    <!--                    <input type="text" class="span4" id="secretariat" name="secretariat" placeholder="Taip Nama Urusetia" value="<?php //echo set_value('secretariat', !empty($booking) ? $booking->secretariat : '');    ?>">-->
                        <span class="label label-important validate-required">Wajib</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="chairman">Pengerusi</label>
                    <div class="controls">
                        <input autocomplete="off" type="text" id="chairman" name="chairman" class="span4" placeholder="Taip Nama Pengerusi" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $user_list; ?>]'>
    <!--                    <input type="text" class="span4" id="chairman" name="chairman" placeholder="Taip Nama Pengerusi" value="<?php //echo set_value('chairman', !empty($booking) ? $booking->chairman : '');    ?>">-->
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="total_from_agensi">Bil. Pegawai Agensi</label>
                    <div class="controls">
                        <input type="text" class="span1" id="total_from_agensi" name="total_from_agensi" value="<?php echo set_value('total_from_agensi', !empty($booking) ? $booking->total_from_agensi : 0); ?>">
                        <span class="label label-important validate-number-only">Wajib</span>
                        <p class="help-block"><strong>Nombor sahaja</strong>.</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="total_from_nonagensi">Bil. Pegawai Luar</label>
                    <div class="controls">
                        <input type="text" class="span1" id="total_from_nonagensi" name="total_from_nonagensi" value="<?php echo set_value('total_from_nonagensi', !empty($booking) ? $booking->total_from_nonagensi : 0); ?>">
                        <span class="label label-important validate-number-only">Wajib</span>
                        <p class="help-block"><strong>Nombor sahaja</strong>.</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="description">Catatan</label>
                    <div class="controls">
                        <textarea rows="2" class="span4" id="description" name="description"><?php echo set_value('description', !empty($booking) ? $booking->description : ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="makanan">
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
                    <label class="control-label" for="total_pack">Jumlah Pegawai</label>
                    <div class="controls">
                        <input type="text" class="span1" id="total_pack" name="total_pack" value="<?php echo set_value('total_pack', 0); ?>">
                        <p class="help-block"><strong>Nombor sahaja</strong>.</p>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="food_description">Catatan</label>
                    <div class="controls">
                        <textarea rows="2" class="span4" id="food_description" name="food_description"><?php echo set_value('food_description'); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="peralatan">
                <div class="control-group">
                    <label class="control-label" for="equipments_type_id">Peralatan</label>
                    <div class="controls">

                        <?php
                        $show_deleted_msg = false;
                        foreach ($equipments as $equipment):
                            ?>
                            <?php if (!$equipment->deleted && ($equipment->status)): ?>
                                <div class="input-prepend input-append">
                                    <span class="add-on width1-add-on"><?php echo $equipment->name ?></span>
                                    <span id="equipment<?php echo $equipment->equipment_id; ?>" class="add-on width2-add-on"><?php echo $equipment->quantity ?> unit</span>
                                    <input placeholder="0" class="span1 validate-quantity" name="equipments_type_id[<?php echo $equipment->equipment_id ?>]" type="text">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="control-group msg-check-equipment-success">
                    <label class="control-label"></label>
                    <div class="controls padding-right20">
                        <div id="ajax-placeholder-equipment-success" class="alert margin-bottom0 alert-success"></div>
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
                    <label class="control-label" for="equipment_description">Catatan</label>
                    <div class="controls">
                        <textarea rows="2" class="span4" id="equipment_description" name="equipment_description"><?php echo set_value('equipment_description'); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions padding-left0">
            <div class="control-group">
                <div class="controls">
                    <?php echo anchor("booking_rooms", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
                    <input type="submit" class="btn btn-primary" value="Simpan"/>
                </div>
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

        <?php echo anchor("booking_rooms/delete/{$booking->id}", 'Hapus', 'class="btn btn-primary"'); ?>

    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('#form-details').hide();
        $('.msg-check-success').hide();
        $('.msg-check-error').hide();
        $('.msg-check-equipment-success').hide();
        $('input[type=submit]').attr('disabled', true);
                
        $('#check_availability').click(function(){
            $('.msg-check-success').hide();
            $('.msg-check-error').hide();
                        
            var start_date = $('input[name=start_date]').val();
            var end_date = $('input[name=end_date]').val();
                
            if ((start_date != '') && (end_date != '')) {
                if (start_date == end_date) {
                    $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Sila pilih tetapkan tarikh tamat.');
                    $('.msg-check-error').fadeIn();
                }
                else {
                    $('#ajax-placeholder').html('<p><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></p>');
                    
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('ajaxify/check_availability/1'); ?>",
                        data: "start_date=" + start_date + "&end_date=" + end_date,
                        dataType: 'json',
                        success: function(msg){
                            
                            if (msg['result'] == true) {
                                $('input[type=submit]').attr('disabled', false);
                                $('#ajax-placeholder-success').html('<i class="icon-ok"></i> Ada kekosongan. Sila teruskan tempahan.');
                                
                                $('select[name=fk_room_id]').html('');
                                $('select[name=fk_room_id]').append('<option value="">-- Sila Pilih</option>');
                                $('select[name=fk_room_id]').append(msg['data']);
                                $('.msg-check-success').fadeIn();
                                
                                var start_date = $('input[name=start_date]').val();
                                var end_date = $('input[name=end_date]').val();
                
                                if ((start_date != '') && (end_date != '')) {
                                    //$('#ajax-placeholder-equipment').html('<p><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></p>');
                        
                                    $.ajax({
                                        type: "POST",
                                        url: "<?php echo site_url('ajaxify/check_availability/3'); ?>",
                                        data: "start_date=" + start_date + "&end_date=" + end_date + "&with_room=true",
                                        dataType: 'json',
                                        success: function(msg){
                                            for(var e in msg){
                                                $('#equipment' + e).html(msg[e] + ' unit');
                                            }
                        
                                            $('input[type=submit]').attr('disabled', false);
                                            //$('#ajax-placeholder-equipment-success').html('<i class="icon-ok"></i> Senarai kekosongan peralatan telah dikemaskinikan.');
                                            //$('.msg-check-equipment-success').fadeIn();
                        
                                        }
                                    }); 
                                    
                                    $('#form-details').fadeIn();
                                }
                                else {
                                    $('#form-details').hide();
                                    $('input[type=submit]').attr('disabled', true);
                                }
                            }
                            else {
                                $('input[type=submit]').attr('disabled', true);
                                $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Tiada kekosongan. Sila pilih masa atau tarikh lain.');
                                $('.msg-check-error').fadeIn();
                            }
                        }
                    }); 
                }
                                
            }
            else {
                $('input[type=submit]').attr('disabled', true);
                $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Sila pilih tarikh.');
                $('.msg-check-error').fadeIn();
            }
        });
                
        $('input[name=start_date], input[name=end_date]').focus(function(){
            $('.msg-check-success').fadeOut();
            $('.msg-check-error').fadeOut();
        });
        
        $('#check_equipment_availability').click(function(){
            $('.msg-check').show();
                        
            var start_date = $('input[name=start_date]').val();
            var end_date = $('input[name=end_date]').val();
                
            if ((start_date != '') && (end_date != '')) {
                //$('#ajax-placeholder-equipment').html('<p><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></p>');
                        
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('ajaxify/check_availability/3'); ?>",
                    data: "start_date=" + start_date + "&end_date=" + end_date,
                    dataType: 'json',
                    success: function(msg){
                        for(var e in msg){
                            $('#equipment' + e).html(msg[e] + ' unit').fadeIn();
                        }
                        
                        $('input[type=submit]').attr('disabled', false);
                        $('#ajax-placeholder-equipment-success').html('<i class="icon-ok"></i> Senarai kekosongan peralatan telah dikemaskinikan.');
                        $('.msg-check-equipment-success').fadeIn();
                        
                    }
                }); 
            }
            else {
                $('input[type=submit]').attr('disabled', true);
                $('#ajax-placeholder-equipment-error').html('<i class="icon-info-sign"></i> Sila pilih tarikh dan bilik terlebih dahulu.');
                $('.msg-check-equipment-error').fadeIn();
            }
        });
        
        $('input[name=total_from_agensi]').blur(function() {
            total_from_agensi = $(this).val();
            if (total_from_agensi== ''){
                total_from_agensi = 0;
                $(this).val(total_from_agensi);
            }            
            
            total_pack = parseInt(total_from_agensi) + parseInt($('input[name=total_from_nonagensi]').val());
            
            $('input[name=total_pack]').val(total_pack);
        });
        
        $('input[name=total_from_nonagensi]').blur(function() {
            total_from_nonagensi = $(this).val();
            if (total_from_nonagensi== ''){
                total_from_nonagensi = 0;
                $(this).val(total_from_nonagensi);
            }   
            
            total_pack = parseInt(total_from_nonagensi) + parseInt($('input[name=total_from_agensi]').val());
            $('input[name=total_pack]').val(total_pack);
        });
    });
</script>