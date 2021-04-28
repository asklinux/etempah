<?php $current_id = (isset($this->uri->rsegments[3])) ? $this->uri->rsegments[3] : 0; ?>

<ul class="nav nav-tabs margin-bottom0" id="myTab">
    <li class="active"><a href="#kalendarTempahan">Kalendar Bilik</a></li>
    <?php if (!empty($equipments)): ?>
    <li><a href="#equipmentList">Senarai Peralatan</a></li>
    <?php endif; ?>
</ul>

<div class="tab-content">
    <div class="tab-pane fade active in" id="kalendarTempahan" style="margin: 0 20px 20px;">
        <div class="row">
            <div id="event-calendar" class="span4">
                <br/>
                <p>Petunjuk:</p>
                <div style="border-radius:3px; float:left; background:none repeat scroll 0 0 #89B814; width: 20px; height: 20px;"></div>&nbsp;&nbsp;Ada tempahan
                <div style="clear:both;"></div><br/>
                <div style="border-radius:3px; float:left; background:none repeat scroll 0 0 #E0E0E0; width: 20px; height: 20px;"></div>&nbsp;&nbsp;Tiada tempahan
            </div>
            <div class="eventsCalendar-list-wrap span6 padding-top10">
                <!--                <div class="btn-group" data-toggle="buttons-radio">
                                    <a href="<?php echo site_url(); ?>frontpage/index/" class="btn btn-info <?php echo ($current_id == 0) ? "disabled" : ''; ?>">Bilik</a>
                                    <a href="<?php echo site_url(); ?>frontpage/index/3" class="btn btn-info <?php echo ($current_id == 3) ? "disabled" : ''; ?>">Peralatan</a>
                                    <a href="<?php echo site_url(); ?>frontpage/index/4" class="btn btn-info <?php echo ($current_id == 4) ? "disabled" : ''; ?>">Kenderaan</a>
                                </div>-->
                <p class='eventsCalendar-subtitle'></p>
                <span class='eventsCalendar-loading calendar-pos'><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></span>
                <div class='eventsCalendar-list-content'>
                    <div id="accordion2" class="room_calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($equipments)): ?>
        <div class="tab-pane fade in padding-left30" id="equipmentList" style="margin: 0 20px 20px; min-height: 325px;">
            <div class="row">

                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="90%">Nama Peralatan</th>
                            <th width="10%">Kuantiti</th>
                            <th width="10%" class="hide">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($equipments)): ?>
                            <?php foreach ($equipments as $equipment): ?>
                                <?php if (!$equipment->deleted): ?>
                                    <?php $row_type = ($equipment->status == 0) ? 'class="error"' : ''; ?>
                                    <tr data-provides="rowlink" <?php echo $row_type ?>>
                                        <td><?php echo $equipment->name; ?></td>
                                        <td><?php echo $equipment->quantity; ?></td>
                                        <td class="hide">
                                            <?php echo anchor("equipments/create/{$equipment->equipment_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">Tiada senarai peralatan dijumpai.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('.msg-check-success').hide();
        $('.msg-check-error').hide();
        
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
                                $('select[name=fk_room_id]').append('<option>-- Sila Pilih</option>');
                                $('select[name=fk_room_id]').append(msg['data']);
                                $('.msg-check-success').fadeIn();
                                
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
        
        $("#event-calendar").eventCalendar({
            eventsjson: '<?php echo site_url(); ?>ajaxify/getTempahanList<?php echo (!empty($status_id)) ? "/$status_id" : ''; ?>'
        });
        
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
    });
</script>
